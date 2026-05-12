<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Load;
use App\Models\Address;
use App\Notifications\NewMessage;

class LoadController extends Controller
{
    /**
     * Assign a Carrier to an Order (Create a Load)
     */
    public function summon(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'carrier_id' => 'required|exists:users,id',
        ]);

        $order = Order::with('listing.product', 'seller.addresses', 'buyer.addresses')->findOrFail($request->order_id);
        $carrier = User::findOrFail($request->carrier_id);
        $user = auth()->user();

        // Ensure the auth user is buyer or seller
        if (!in_array($user->id, [$order->buyer_id, $order->seller_id])) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        // Auto-assign addresses (or create placeholders)
        $pickup = $order->seller->addresses->first() ?? Address::create([
            'user_id' => $order->seller_id,
            'title' => 'Default Pickup',
            'state' => 'Unknown',
            'city' => 'Unknown',
            'lat' => 0,
            'lng' => 0,
        ]);

        $dropoff = $order->buyer->addresses->first() ?? Address::create([
            'user_id' => $order->buyer_id,
            'title' => 'Default Dropoff',
            'state' => 'Unknown',
            'city' => 'Unknown',
            'lat' => 0,
            'lng' => 0,
        ]);

        // Create Load
        $load = Load::create([
            'owner_id' => $user->id,
            'kind' => $order->listing->product->type,
            'qty' => $order->qty,
            'unit' => $order->unit,
            'pickup_addr_id' => $pickup->id,
            'dropoff_addr_id' => $dropoff->id,
            'status' => Load::ST_MATCHED,
            'order_id' => $order->id,
            'carrier_id' => $carrier->id,
        ]);

        // Send Push Notification to Carrier
        $carrier->notify(new \App\Notifications\NewTransportDeal($load, $user));

        return response()->json([
            'success' => true,
            'message' => __('Transporter summoned successfully.'),
            'load_id' => $load->id
        ]);
    }
}
