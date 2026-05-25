<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SoukPrice;
use App\Models\WorldOlivePrice;
use Illuminate\Support\Facades\Redirect;

class PriceManagementController extends Controller
{
    // Souk Prices Management
    public function indexSouk()
    {
        $prices = SoukPrice::orderBy('date', 'desc')
            ->orderBy('souk_name')
            ->paginate(20);
        
        return view('admin.prices.souk-index', compact('prices'));
    }

    public function refreshSoukDates()
    {
        // Update all active souk prices to today
        SoukPrice::where('is_active', true)->update(['date' => \Illuminate\Support\Carbon::today()]);

        // Clear general application cache to reflect changes immediately
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return Redirect::route('admin.prices.souk.index')
            ->with('success', __('تم تحديث تاريخ جميع الأسعار النشطة إلى اليوم بنجاح.'));
    }

    public function createSouk()
    {
        $souks = SoukPrice::getFamousSouks();
        return view('admin.prices.souk-create', compact('souks'));
    }

    public function storeSouk(Request $request)
    {
        $validated = $request->validate([
            'souk_name' => 'required|string|max:255',
            'governorate' => 'required|string|max:255',
            'variety' => 'required|string|max:64',
            'product_type' => 'required|in:olive,oil',
            'quality' => 'nullable|string|max:64',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|min:0|gte:price_min',
            'currency' => 'required|string|max:8',
            'unit' => 'required|string|max:16',
            'date' => 'required|date',
            'trend' => 'required|in:up,down,stable',
            'change_percentage' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $validated['price_avg'] = ($validated['price_min'] + $validated['price_max']) / 2;
        $validated['is_active'] = true;

        SoukPrice::create($validated);

        return Redirect::route('admin.prices.souk.index')
            ->with('success', __('Price added successfully'));
    }

    public function editSouk(SoukPrice $price)
    {
        $souks = SoukPrice::getFamousSouks();
        return view('admin.prices.souk-edit', compact('price', 'souks'));
    }

    public function updateSouk(Request $request, SoukPrice $price)
    {
        $validated = $request->validate([
            'souk_name' => 'required|string|max:255',
            'governorate' => 'required|string|max:255',
            'variety' => 'required|string|max:64',
            'product_type' => 'required|in:olive,oil',
            'quality' => 'nullable|string|max:64',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|min:0|gte:price_min',
            'currency' => 'required|string|max:8',
            'unit' => 'required|string|max:16',
            'date' => 'required|date',
            'trend' => 'required|in:up,down,stable',
            'change_percentage' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['price_avg'] = ($validated['price_min'] + $validated['price_max']) / 2;

        $price->update($validated);

        return Redirect::route('admin.prices.souk.index')
            ->with('success', __('Price updated successfully'));
    }

    public function destroySouk(SoukPrice $price)
    {
        $price->delete();
        return Redirect::route('admin.prices.souk.index')
            ->with('success', __('Price deleted successfully'));
    }

    // World Prices Management
    public function indexWorld()
    {
        $prices = WorldOlivePrice::orderBy('date', 'desc')
            ->orderBy('country')
            ->paginate(20);
        
        return view('admin.prices.world-index', compact('prices'));
    }

    public function createWorld()
    {
        $countries = WorldOlivePrice::getMajorProducers();
        return view('admin.prices.world-create', compact('countries'));
    }

    public function storeWorld(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'variety' => 'nullable|string|max:64',
            'quality' => 'required|in:EVOO,virgin,refined,lampante',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:8',
            'unit' => 'required|string|max:16',
            'date' => 'required|date',
            'trend' => 'required|in:up,down,stable',
            'change_percentage' => 'nullable|numeric',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        WorldOlivePrice::create($validated);

        return Redirect::route('admin.prices.world.index')
            ->with('success', __('World price added successfully'));
    }

    public function editWorld(WorldOlivePrice $price)
    {
        $countries = WorldOlivePrice::getMajorProducers();
        return view('admin.prices.world-edit', compact('price', 'countries'));
    }

    public function updateWorld(Request $request, WorldOlivePrice $price)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'variety' => 'nullable|string|max:64',
            'quality' => 'required|in:EVOO,virgin,refined,lampante',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:8',
            'unit' => 'required|string|max:16',
            'date' => 'required|date',
            'trend' => 'required|in:up,down,stable',
            'change_percentage' => 'nullable|numeric',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $price->update($validated);

        return Redirect::route('admin.prices.world.index')
            ->with('success', __('World price updated successfully'));
    }

    public function destroyWorld(WorldOlivePrice $price)
    {
        $price->delete();
        return Redirect::route('admin.prices.world.index')
            ->with('success', __('World price deleted successfully'));
    }
}
