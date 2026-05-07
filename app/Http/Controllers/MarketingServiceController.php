<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingService;
use App\Models\ServiceAnalytic;
use App\Notifications\AppointmentBooked;

class MarketingServiceController extends Controller
{
    public function appointmentForm(MarketingService $service)
    {
        // Log analytic event (optional, treat visiting appointment as add_to_cart for funnel)
        ServiceAnalytic::create([
            'event_type' => 'add_to_cart',
            'service_id' => $service->id,
            'value' => $service->price_tnd_weekly,
            'currency' => $service->currency,
            'session_id' => session()->getId(),
            'user_id' => auth()->id()
        ]);
        
        $cart = [
            $service->id => [
                'name' => $service->title_ar,
                'price' => $service->price_tnd_weekly,
                'quantity' => 1,
                'icon' => $service->icon_url,
                'currency' => $service->currency
            ]
        ];
        
        return view('public.services_appointment', compact('cart', 'service'));
    }
    
    public function submitAppointment(Request $request, MarketingService $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'business_info' => 'required|string',
            'appointment_date' => 'required|date|after:today',
        ]);
        
        $cart = [
            $service->id => [
                'name' => $service->title_ar,
                'price' => $service->price_tnd_weekly,
                'quantity' => 1,
                'icon' => $service->icon_url,
                'currency' => $service->currency
            ]
        ];
        $total = $service->price_tnd_weekly;
        
        // Log checkout initiation
        ServiceAnalytic::create([
            'event_type' => 'checkout_initiated',
            'service_id' => $service->id,
            'value' => $total,
            'currency' => $service->currency,
            'session_id' => session()->getId(),
            'user_id' => auth()->id()
        ]);
        
        // Save appointment
        $appointment = \App\Models\MarketingAppointment::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'business_info' => $request->business_info,
            'appointment_date' => $request->appointment_date,
            'cart_data' => $cart,
            'total_budget' => $total,
            'status' => 'pending',
        ]);

        // Send notification
        auth()->user()->notify(new AppointmentBooked($appointment));
        
        // Log purchase completion to keep the metrics functional
        ServiceAnalytic::create([
            'event_type' => 'purchase',
            'service_id' => $service->id,
            'value' => $total,
            'currency' => $service->currency,
            'session_id' => session()->getId(),
            'user_id' => auth()->id()
        ]);
        
        return redirect()->route('services.pricing')->with('success', __('Your appointment has been booked successfully! We will contact you soon.'));
    }
}
