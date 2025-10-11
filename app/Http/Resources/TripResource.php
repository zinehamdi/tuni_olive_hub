<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Trip */
class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sr_code' => $this->sr_code,
            'carrier' => $this->whenLoaded('carrier', function () { return [ 'id' => $this->carrier->id, 'name' => $this->carrier->name, 'role' => $this->carrier->role ]; }),
            'loads' => collect((array) $this->load_ids)->map(fn($id) => ['id' => $id])->values(),
            'status' => $this->inferredStatus(),
            'start_at' => $this->start_at,
            'delivered_at' => $this->delivered_at,
            'distance_km' => $this->distance_km,
            'pin_token' => $this->pin_token ? (str($this->pin_token)->substr(0, 5).'****') : null,
            'pod' => [ 'verified_at' => optional($this->pods()->latest('id')->first())->verified_at ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
