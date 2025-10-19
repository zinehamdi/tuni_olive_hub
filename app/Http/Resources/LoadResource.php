<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Load */
class LoadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner' => $this->whenLoaded('owner', function () { return [ 'id' => $this->owner->id, 'name' => $this->owner->name, 'role' => $this->owner->role ]; }),
            'order_id' => $this->order_id,
            'kind' => $this->kind,
            'qty' => $this->qty,
            'unit' => $this->unit,
            'pickup_address' => $this->whenLoaded('pickup', function () { return [ 'id' => $this->pickup->id, 'governorate' => $this->pickup->governorate, 'lat' => $this->pickup->lat, 'lng' => $this->pickup->lng ]; }),
            'dropoff_address' => $this->whenLoaded('dropoff', function () { return [ 'id' => $this->dropoff->id, 'governorate' => $this->dropoff->governorate, 'lat' => $this->dropoff->lat, 'lng' => $this->dropoff->lng ]; }),
            'status' => $this->status,
            'carrier' => $this->whenLoaded('carrier', function () { return [ 'id' => $this->carrier->id, 'name' => $this->carrier->name, 'role' => $this->carrier->role ]; }),
            'deadline_at' => $this->deadline_at,
            'eta_minutes' => $this->eta_minutes,
            'price_floor' => $this->price_floor,
            'price_ceiling' => $this->price_ceiling,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
