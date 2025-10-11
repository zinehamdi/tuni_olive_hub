<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationController extends ApiController
{
    protected function ensureAdmin(Request $request): void
    {
        if ($request->user()->role !== 'admin') abort(403, trans('auth.forbidden_action'));
    }

    public function resolve(Request $request, Report $report)
    {
        $this->ensureAdmin($request);
        $data = $request->validate(['action' => ['required','in:hide,reject,ban_user'], 'note' => ['nullable','string','max:500']]);
        DB::transaction(function() use ($report, $data, $request) {
            $report->status = 'resolved';
            $report->moderator_id = $request->user()->id;
            $report->note = $data['note'] ?? null;
            if ($data['action'] === 'hide') {
                if ($report->object_type === 'message') {
                    Message::where('id', $report->object_id)->update(['is_hidden' => true]);
                } elseif ($report->object_type === 'review') {
                    Review::where('id', $report->object_id)->update(['is_visible' => false]);
                }
            } elseif ($data['action'] === 'reject') {
                $report->status = 'rejected';
            } elseif ($data['action'] === 'ban_user') {
                if ($report->object_type === 'user') {
                    User::where('id', $report->object_id)->update(['banned_at' => now()]);
                }
                if ($report->object_type === 'message') {
                    Message::where('id', $report->object_id)->update(['is_hidden' => true]);
                }
                if ($report->object_type === 'review') {
                    Review::where('id', $report->object_id)->update(['is_visible' => false]);
                }
            }
            $report->save();
        });
        $this->audit('moderation.resolved', 'report', $report->id);
        return $this->ok($report);
    }

    public function queue(Request $request)
    {
        $this->ensureAdmin($request);
        $request->validate(['type' => ['required','in:message,review'], 'status' => ['required','in:new,reviewing']]);
        $q = Report::query()->where('object_type', $request->input('type'))->where('status', $request->input('status'))->latest('id');
        $p = $q->paginate(max(1, min(100, (int)$request->input('per_page', 15))))->appends($request->query());
        $meta = [ 'current_page'=>$p->currentPage(),'per_page'=>$p->perPage(),'total'=>$p->total(),'last_page'=>$p->lastPage(),'from'=>$p->firstItem(),'to'=>$p->lastItem() ];
        return response()->json(['success'=>true,'data'=>$p->items(),'meta'=>$meta]);
    }

    public function hideMessage(Request $request, int $id)
    {
        $this->ensureAdmin($request);
        Message::where('id', $id)->update(['is_hidden' => true]);
        $this->audit('moderation.hide_message', 'message', $id);
        return $this->ok(['id' => $id, 'hidden' => true]);
    }

    public function hideReview(Request $request, int $id)
    {
        $this->ensureAdmin($request);
        Review::where('id', $id)->update(['is_visible' => false]);
        $this->audit('moderation.hide_review', 'review', $id);
        return $this->ok(['id' => $id, 'visible' => false]);
    }
}
