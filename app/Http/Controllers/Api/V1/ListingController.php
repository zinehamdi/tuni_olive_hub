<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\ListingStoreRequest;
use App\Http\Requests\V1\ListingUpdateRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends ApiController
{
    // Guarded by route middleware in routes/api.php

    public function index(Request $request)
    {
        $this->authorize('viewAny', Listing::class);
        $sort = $request->input('sort', 'ranked');
        $q = Listing::query()->with(['product','seller'])->where('status','!=','archived');

        if ($sort === 'price_asc') {
            $q->join('products', 'products.id', '=', 'listings.product_id')->orderBy('products.price', 'asc');
        } elseif ($sort === 'price_desc') {
            $q->join('products', 'products.id', '=', 'listings.product_id')->orderBy('products.price', 'desc');
        } elseif ($sort === 'newest') {
            $q->orderByDesc('listings.created_at');
        } else { // ranked
            // Compute score server-side via selectRaw; fallback for sqlite
            $now = now();
            $q->leftJoin('products', 'products.id', '=', 'listings.product_id')
              ->leftJoin('users as sellers', 'sellers.id', '=', 'listings.seller_id')
              ->select('listings.*')
          ->selectRaw('(0.5 * (COALESCE(sellers.trust_score,0)/100.0)
                   + 0.3 * (CASE WHEN julianday(?) - julianday(listings.created_at) <= 1 THEN 1
                             WHEN julianday(?) - julianday(listings.created_at) >= 30 THEN 0
                             ELSE 1 - ((julianday(?) - julianday(listings.created_at) - 1) / 29.0) END)
                   + 0.2 * (
                       CASE WHEN (products.media IS NOT NULL AND json_array_length(products.media) > 0) THEN 1
                          ELSE 0 END
                     )) as ranked_score', [$now, $now, $now])
              ->orderByDesc('ranked_score')
              ->orderByDesc('listings.created_at');
        }

        $per = (int) $request->input('per_page', 15);
        $p = $q->paginate(max(1, min(100, $per)))->appends($request->query());
        $meta = [
            'current_page' => $p->currentPage(),
            'per_page' => $p->perPage(),
            'total' => $p->total(),
            'last_page' => $p->lastPage(),
            'from' => $p->firstItem(),
            'to' => $p->lastItem(),
            'sort' => $sort,
        ];
        $data = ListingResource::collection($p->items());
        return response()->json(['success' => true, 'data' => $data, 'meta' => $meta]);
    }

    public function show(Listing $listing)
    {
        $this->authorize('view', $listing);
    return $this->ok(new ListingResource($listing->load(['product','seller'])));
    }

    public function store(ListingStoreRequest $request)
    {
        $this->authorize('create', Listing::class);
        $listing = Listing::create($request->validated());
        $this->audit('listing.created', 'listing', $listing->id);
    return $this->ok(new ListingResource($listing->load(['product','seller'])), 201);
    }

    public function update(ListingUpdateRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);
        $listing->update($request->validated());
        $this->audit('listing.updated', 'listing', $listing->id);
    return $this->ok(new ListingResource($listing->load(['product','seller'])));
    }

    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);
        $listing->delete();
        $this->audit('listing.deleted', 'listing', $listing->id);
        return $this->ok(null, 204);
    }
}
