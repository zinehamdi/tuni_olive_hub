<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewFollowNotification;
use App\Notifications\NewLikeNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserInteractionController extends Controller
{
    /**
     * Toggle follow status for a user
     */
    public function toggleFollow(User $user): JsonResponse
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return response()->json([
                'success' => false,
                'message' => __('You must be logged in to follow users'),
            ], 401);
        }

        // Can't follow yourself
        if ($authUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('You cannot follow yourself'),
            ], 400);
        }

        $isFollowing = $authUser->isFollowing($user);

        if ($isFollowing) {
            $authUser->following()->detach($user->id);
            $followed = false;
        } else {
            $authUser->following()->attach($user->id);
            $followed = true;
            // Notify followed user (throttled — max 1 email per 5 min)
            $user->notify(new NewFollowNotification($authUser));
        }

        return response()->json([
            'success' => true,
            'followed' => $followed,
            'followers_count' => $user->followers()->count(),
            'message' => $followed ? __('You are now following this user') : __('You have unfollowed this user'),
        ]);
    }

    /**
     * Toggle like status for a user
     */
    public function toggleLike(User $user): JsonResponse
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return response()->json([
                'success' => false,
                'message' => __('You must be logged in to like profiles'),
            ], 401);
        }

        // Can't like yourself
        if ($authUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('You cannot like your own profile'),
            ], 400);
        }

        $hasLiked = $authUser->hasLiked($user);

        if ($hasLiked) {
            $authUser->likedUsers()->detach($user->id);
            $liked = false;
        } else {
            $authUser->likedUsers()->attach($user->id);
            $liked = true;
            // Notify liked user (throttled — max 1 email per 5 min)
            $user->notify(new NewLikeNotification($authUser));
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $user->likers()->count(),
            'message' => $liked ? __('You liked this profile') : __('You unliked this profile'),
        ]);
    }

    /**
     * Get interaction status for a user
     */
    public function getStatus(User $user): JsonResponse
    {
        $authUser = auth()->user();

        $data = [
            'followers_count' => $user->followers()->count(),
            'likes_count' => $user->likers()->count(),
            'is_following' => false,
            'has_liked' => false,
        ];

        if ($authUser && $authUser->id !== $user->id) {
            $data['is_following'] = $authUser->isFollowing($user);
            $data['has_liked'] = $authUser->hasLiked($user);
        }

        return response()->json($data);
    }
}
