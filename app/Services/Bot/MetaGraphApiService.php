<?php

namespace App\Services\Bot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaGraphApiService
{
    protected string $pageAccessToken;
    protected string $graphApiUrl = 'https://graph.facebook.com/v21.0';

    public function __construct(?string $token = null)
    {
        $this->pageAccessToken = $token ?? (string) (\App\Models\BotSetting::get('meta_page_access_token') ?: config('services.meta.page_access_token', env('META_PAGE_ACCESS_TOKEN', '')));
    }

    /**
     * Reply to a comment on a Facebook Page post.
     * Endpoint: POST /{comment_id}/comments
     */
    public function replyToComment(string $commentId, string $message): array
    {
        try {
            // Meta comments endpoint requires form params or query string
            $response = Http::asForm()->post("{$this->graphApiUrl}/{$commentId}/comments", [
                'access_token' => $this->pageAccessToken,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("Facebook comment reply posted successfully to {$commentId}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("Failed to reply to Facebook comment {$commentId}", [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        } catch (\Throwable $e) {
            Log::error("Exception replying to Facebook comment {$commentId}: {$e->getMessage()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Like a Facebook comment to increase engagement.
     * Endpoint: POST /{comment_id}/likes
     */
    public function likeComment(string $commentId): array
    {
        try {
            $response = Http::asForm()->post("{$this->graphApiUrl}/{$commentId}/likes", [
                'access_token' => $this->pageAccessToken,
            ]);

            return ['success' => $response->successful()];
        } catch (\Throwable $e) {
            Log::warning("Could not like comment {$commentId}: {$e->getMessage()}");
            return ['success' => false];
        }
    }

    /**
     * Send a private reply to a user who commented on a post.
     * Endpoint: POST /{page_id}/messages with recipient = {comment_id: ...}
     */
    public function sendPrivateReply(string $commentId, string $message): array
    {
        try {
            $pageId = (string) (\App\Models\BotSetting::get('meta_page_id') ?: config('services.meta.page_id', env('META_PAGE_ID', '828942590302317')));
            $response = Http::withToken($this->pageAccessToken)->post("{$this->graphApiUrl}/{$pageId}/messages", [
                'recipient' => ['comment_id' => $commentId],
                'message' => ['text' => $message],
            ]);

            if ($response->successful()) {
                Log::info("Facebook private reply sent successfully for comment {$commentId}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("Failed to send private reply for comment {$commentId}", [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        } catch (\Throwable $e) {
            Log::error("Exception sending private reply for comment {$commentId}: {$e->getMessage()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a standard Messenger text message to a PSID.
     * Endpoint: POST /{page_id}/messages
     */
    public function sendMessengerText(string $recipientPsid, string $message): array
    {
        try {
            $pageId = (string) (\App\Models\BotSetting::get('meta_page_id') ?: config('services.meta.page_id', env('META_PAGE_ID', '828942590302317')));
            $response = Http::withToken($this->pageAccessToken)->post("{$this->graphApiUrl}/{$pageId}/messages", [
                'recipient' => ['id' => $recipientPsid],
                'message' => ['text' => $message],
            ]);

            if ($response->successful()) {
                Log::info("Messenger message sent to {$recipientPsid}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("Failed to send Messenger message to {$recipientPsid}", [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        } catch (\Throwable $e) {
            Log::error("Exception sending Messenger message to {$recipientPsid}: {$e->getMessage()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fetch original post content to understand context.
     * Endpoint: GET /{post_id}?fields=message,created_time,story
     */
    public function fetchPostContent(string $postId): ?string
    {
        try {
            $response = Http::get("{$this->graphApiUrl}/{$postId}", [
                'access_token' => $this->pageAccessToken,
                'fields' => 'message,story',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['message'] ?? $data['story'] ?? null;
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning("Could not fetch post content for {$postId}: {$e->getMessage()}");
            return null;
        }
    }
}
