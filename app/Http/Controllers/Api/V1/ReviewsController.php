<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewsController extends ApiController
{
    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Review::class);
        $request->validate([
            'target_user_id' => ['required','integer','exists:users,id'],
            'object_type' => ['required','in:order,trip'],
            'object_id' => ['required','integer'],
            'rating' => ['required','integer','min:1','max:5'],
            'title' => ['nullable','string','max:120'],
            'comment' => ['nullable','string','max:2000'],
            'photos' => ['nullable','array'],
        ]);

        // Rate-limit reviews: 10/hour per user
        if (!app()->runningUnitTests() && !\Illuminate\Support\Facades\RateLimiter::attempt('reviews:'.$request->user()->id, 10, fn()=>true, 3600)) {
            abort(429, 'Too many reviews.');
        }

        $data = $request->only(['target_user_id','object_type','object_id','rating','title','comment','photos']);
        $actor = $request->user();

        $verified = false;
        if ($data['object_type'] === 'order') {
            $order = Order::find($data['object_id']);
            if ($order && $order->status === Order::STATUS_DELIVERED) {
                $verified = in_array((int)$actor->id, [(int)$order->buyer_id, (int)$order->seller_id], true);
            }
        } else {
            $trip = Trip::find($data['object_id']);
            if ($trip && $trip->delivered_at) {
                // Reviewer is buyer of any attached order or the carrier
                $isCarrier = (int)$actor->id === (int)$trip->carrier_id;
                $isBuyer = false;
                foreach ((array)$trip->load_ids as $lid) {
                    $ld = \App\Models\Load::find($lid);
                    if ($ld && $ld->order && (int)$ld->order->buyer_id === (int)$actor->id) { $isBuyer = true; break; }
                }
                $verified = $isCarrier || $isBuyer;
            }
        }
        if (!$verified) abort(403, trans('auth.forbidden_action'));

        $review = \App\Models\Review::create([
            'reviewer_id' => $actor->id,
            'target_user_id' => $data['target_user_id'],
            'object_type' => $data['object_type'],
            'object_id' => $data['object_id'],
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'] ?? null,
            'photos' => $data['photos'] ?? null,
            'is_verified_purchase' => true,
            'is_visible' => true,
        ]);
        $this->audit('review.created', 'review', $review->id);
        // Update aggregates on target user
        $agg = DB::table('reviews')->where('target_user_id', $review->target_user_id)->selectRaw('avg(rating) as avg, count(*) as cnt')->first();
        User::where('id', $review->target_user_id)->update(['rating_avg' => round((float)$agg->avg,2), 'rating_count' => (int)$agg->cnt]);
        return $this->ok($review, 201);
    }

    public function reply(Request $request, \App\Models\Review $review)
    {
        $user = $request->user();
        if ((int)$user->id !== (int)$review->target_user_id && $user->role !== 'admin') {
            abort(403, trans('auth.forbidden_action'));
        }
        $request->validate(['body' => ['required','string','max:2000']]);
        // Single official reply: upsert
        $reply = \App\Models\ReviewReply::updateOrCreate(
            ['review_id' => $review->id],
            ['replier_id' => $user->id, 'body' => $request->input('body')]
        );
        $this->audit('review.reply', 'review', $review->id);
        return $this->ok($reply, 201);
    }

    public function index(Request $request)
    {
        $q = DB::table('reviews')->where('is_visible', true);
        if ($request->filled('target_user_id')) $q->where('target_user_id', (int)$request->input('target_user_id'));
        if ($request->filled('object_type')) $q->where('object_type', $request->input('object_type'));
        if ($request->filled('rating_min')) $q->where('rating', '>=', (int)$request->input('rating_min'));
        $sort = $request->input('sort','recent');
        if ($sort === 'top') $q->orderByDesc('rating'); else $q->orderByDesc('id');

        $per = max(1, min(100, (int) $request->input('per_page', 15)));
        $p = $q->paginate($per)->appends($request->query());
        $meta = [
            'current_page' => $p->currentPage(),
            'per_page' => $p->perPage(),
            'total' => $p->total(),
            'last_page' => $p->lastPage(),
            'from' => $p->firstItem(),
            'to' => $p->lastItem(),
        ];
        return response()->json(['success' => true, 'data' => $p->items(), 'meta' => $meta]);
    }

    public function report(Request $request, \App\Models\Review $review)
    {
        $this->authorize('create', \App\Models\Report::class);
        $request->validate(['reason' => ['required','string','max:200'], 'evidence' => ['nullable','array']]);
        $rep = \App\Models\Report::create([
            'reporter_id' => $request->user()->id,
            'object_type' => 'review',
            'object_id' => $review->id,
            'reason' => $request->input('reason'),
            'evidence' => $request->input('evidence'),
            'status' => 'new',
        ]);
        $this->audit('review.reported', 'review', $review->id);
        return $this->ok(['message' => trans('review.report_received'), 'report_id' => $rep->id]);
    }

    public function reputation(User $user)
    {
        return $this->ok([
            'rating_avg' => (float) ($user->rating_avg ?? 0),
            'rating_count' => (int) ($user->rating_count ?? 0),
            'trust_score' => (int) ($user->trust_score ?? 0),
            'badges' => [],
        ]);
    }
}
