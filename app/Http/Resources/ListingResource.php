<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Listing */
class ListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seller' => $this->whenLoaded('seller', function () {
                return [ 'id' => $this->seller->id, 'name' => $this->seller->name, 'role' => $this->seller->role ];
            }),
            'product' => $this->whenLoaded('product', function () {
                return [
                    'type' => $this->product->type,
                    'variety' => $this->product->variety,
                    'quality' => $this->product->quality,
                    'is_organic' => (bool) $this->product->is_organic,
                    'media' => $this->media ?? $this->product->media,
                ];
            }),
            'price' => [ 'unit' => 'TND', 'value' => $this->relationLoaded('product') && $this->product ? $this->product->price : null ],
            'stock' => $this->relationLoaded('product') && $this->product ? $this->product->stock : null,
            'min_order' => $this->min_order,
            'governorate' => $this->governorate,
            'delegation' => $this->delegation,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->whenLoaded('address', function () {
                return $this->address ? [
                    'id' => $this->address->id,
                    'governorate' => $this->address->governorate,
                    'delegation' => $this->address->delegation,
                    'lat' => $this->address->lat ? (float)$this->address->lat : null,
                    'lng' => $this->address->lng ? (float)$this->address->lng : null,
                ] : null;
            }),
            'status' => $this->status,
            'payment_methods' => $this->payment_methods,
            'delivery_options' => $this->delivery_options,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
