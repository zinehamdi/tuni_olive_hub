<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingAppointment;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusUpdated;

class MarketingAppointmentController extends Controller
{
    public function index()
    {
        $appointments = MarketingAppointment::latest()->paginate(20);
        return view('admin.marketing.index', compact('appointments'));
    }

    public function edit(MarketingAppointment $appointment)
    {
        return view('admin.marketing.edit', compact('appointment'));
    }

    public function update(Request $request, MarketingAppointment $appointment)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'business_info' => 'required|string',
            'appointment_date' => 'required|date',
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
            'total_budget' => 'required|numeric|min:0',
        ]);

        $appointment->update($data);

        // Notify user if they have an account
        if ($appointment->user) {
            $appointment->user->notify(new AppointmentStatusUpdated($appointment));
        }

        return redirect()->route('admin.marketing.index')->with('success', __('Marketing appointment updated successfully.'));
    }

    public function updateStatus(Request $request, MarketingAppointment $appointment)
    {
        $request->validate(['status' => 'required|string|in:pending,confirmed,completed,cancelled']);
        $appointment->update(['status' => $request->status]);

        // Notify user if they have an account
        if ($appointment->user) {
            $appointment->user->notify(new AppointmentStatusUpdated($appointment));
        }

        return back()->with('success', __('Status updated successfully.'));
    }

    public function destroy(MarketingAppointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.marketing.index')->with('success', __('Appointment deleted successfully.'));
    }
}
