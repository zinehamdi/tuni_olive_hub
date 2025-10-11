<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\CarrierOffer;
use App\Models\Load;
use Illuminate\Http\Request;
use App\Http\Requests\V1\CarrierOfferStoreRequest;
use App\Events\CarrierOfferProposed;
use App\Services\Chat;

class CarrierOfferController extends ApiController
{
    public function store(CarrierOfferStoreRequest $request, Load $load)
    {
        $this->authorize('view', $load);
        $data = $request->validated();
        // Set carrier as authenticated user by default
        $carrierId = $request->user()->id;
        $offer = CarrierOffer::create([
            'load_id' => $load->id,
            'carrier_id' => $carrierId,
            'offer_price' => $data['offer_price'],
            'eta_minutes' => $data['eta_minutes'],
            'status' => 'proposed',
        ]);
        $this->audit('offer.proposed', 'carrier_offer', $offer->id);
        event(new CarrierOfferProposed($offer->id, $load->id, (float)$offer->offer_price, (int)$offer->eta_minutes));
        $thread = Chat::ensureThread('load', $load->id, [$load->owner_id, $carrierId]);
    Chat::system($thread, '🚚 عرض نقل: '.(string)$offer->offer_price.' / ETA '.(string)$offer->eta_minutes.' دقيقة');
        return $this->ok($offer, 201);
    }

    public function accept(Request $request, CarrierOffer $offer)
    {
        $load = $offer->freight;
        $this->authorize('update', $load); // only load owner/admin can accept
        $offer->status = 'accepted';
        $offer->save();
        // associate load with carrier and eta
        $load->carrier_id = $offer->carrier_id;
        $load->eta_minutes = $offer->eta_minutes;
        $load->moveTo('matched');
        $this->audit('offer.accepted', 'carrier_offer', $offer->id);
        $thread = Chat::ensureThread('load', $load->id, [$load->owner_id, $offer->carrier_id]);
        Chat::system($thread, 'تم قبول عرض النقل.');
        return $this->ok($offer->fresh());
    }
}
