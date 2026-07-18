<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MarketingService;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('addresses')
            ->whereIn('role', [
                'carrier', 'mill', 'packer', 'transiteur', 
                'comptable', 'service_bureau', 'agri_equipment', 'agri_materials', 'agri_study_office'
            ]);

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('role', $request->type);
        }

        // Filter by Governorate
        if ($request->filled('governorate')) {
            $query->whereHas('addresses', function ($q) use ($request) {
                $q->where('governorate', $request->governorate);
            });
        }

        // Search Name or Description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('meta_data->service_description', 'like', '%' . $search . '%')
                  ->orWhere('meta_data->services', 'like', '%' . $search . '%');
            });
        }

        $providers = $query->latest()->get();
        $digitalServices = MarketingService::all();

        return view('public.service_providers', compact('providers', 'digitalServices'));
    }
}
