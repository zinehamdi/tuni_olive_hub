<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'buyer' => $this->whenLoaded('buyer', function () { return [ 'id' => $this->buyer->id, 'name' => $this->buyer->name, 'role' => $this->buyer->role ]; }),
            'seller' => $this->whenLoaded('seller', function () { return [ 'id' => $this->seller->id, 'name' => $this->seller->name, 'role' => $this->seller->role ]; }),
            'listing_id' => $this->listing_id,
            'qty' => $this->qty,
            'unit' => $this->unit,
            'price_unit' => $this->price_unit,
            'total' => $this->total,
            'status' => $this->status,
            'payment' => [ 'method' => $this->payment_method, 'status' => $this->payment_status ],
            'meta' => $this->meta ?? (object)[],
            'trip_id' => $this->trip_id ?? null,
            'escrow_id' => $this->escrow_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
