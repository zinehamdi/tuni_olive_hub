<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::with('user')->latest()->paginate(10);
        return view('admin.deals.index', compact('deals'));
    }

    public function create()
    {
        return view('admin.deals.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:demand,service,supply',
            'title' => 'required|array',
            'title.ar' => 'required|string',
            'title.en' => 'required|string',
            'title.fr' => 'required|string',
            'description' => 'required|array',
            'description.ar' => 'required|string',
            'description.en' => 'required|string',
            'description.fr' => 'required|string',
            'price_range' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'required|in:active,expired,closed',
            'is_featured' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $data['user_id'] = auth()->id();
        $data['is_featured'] = $request->has('is_featured');

        Deal::create($data);

        return redirect()->route('admin.deals.index')->with('success', __('Deal created successfully.'));
    }

    public function edit(Deal $deal)
    {
        return view('admin.deals.edit', compact('deal'));
    }

    public function update(Request $request, Deal $deal)
    {
        $data = $request->validate([
            'type' => 'required|in:demand,service,supply',
            'title' => 'required|array',
            'title.ar' => 'required|string',
            'title.en' => 'required|string',
            'title.fr' => 'required|string',
            'description' => 'required|array',
            'description.ar' => 'required|string',
            'description.en' => 'required|string',
            'description.fr' => 'required|string',
            'price_range' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'required|in:active,expired,closed',
            'is_featured' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $data['is_featured'] = $request->has('is_featured');

        $deal->update($data);

        return redirect()->route('admin.deals.index')->with('success', __('Deal updated successfully.'));
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();
        return redirect()->route('admin.deals.index')->with('success', __('Deal deleted successfully.'));
    }
}
