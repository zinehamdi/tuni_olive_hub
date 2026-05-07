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

        DealRequest::create($data);

        return back()->with('status', __('Your request has been sent successfully. We will contact you soon.'));
    }
}
