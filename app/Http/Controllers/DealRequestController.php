<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealRequest;
use Illuminate\Http\Request;

class DealRequestController extends Controller
{
    public function store(Request $request, Deal $deal)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'requirements' => 'nullable|array',
            'message' => 'nullable|string|max:2000',
        ]);

        $data['deal_id'] = $deal->id;
        $data['status'] = 'new';

        $dealRequest = DealRequest::create($data);

        // Send notifications
        if ($deal->user) {
            $deal->user->notify(new \App\Notifications\NewDealRequest($dealRequest));
        }

        // Also notify all admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            if (!$deal->user || $admin->id !== $deal->user->id) {
                $admin->notify(new \App\Notifications\NewDealRequest($dealRequest));
            }
        }

        return back()->with('status', __('Your request has been sent successfully. We will contact you soon.'));
    }
}
