<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DealRequest;
use Illuminate\Http\Request;

class DealRequestController extends Controller
{
    public function index()
    {
        $requests = DealRequest::with('deal')->latest()->paginate(15);
        return view('admin.deals.requests', compact('requests'));
    }

    public function updateStatus(Request $request, DealRequest $dealRequest)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,closed',
        ]);

        $dealRequest->update(['status' => $request->status]);

        return back()->with('success', __('Status updated successfully.'));
    }

    public function destroy(DealRequest $dealRequest)
    {
        $dealRequest->delete();
        return back()->with('success', __('Request deleted successfully.'));
    }
}
