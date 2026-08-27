<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use App\Models\SoukPrice;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoLandingController extends Controller
{
    /**
     * B2B Bulk Olive Oil Wholesale & Export Landing Page
     */
    public function bulkOliveOil(Request $request)
    {
        $locale = app()->getLocale();

        // Fetch active listings suitable for bulk / wholesale sourcing
        $query = Listing::with(['product', 'seller.addresses'])
            ->where('status', 'active');

        // Optional filters
        if ($request->filled('variety')) {
            $query->whereHas('product', fn($q) => $q->where('variety', 'like', '%' . $request->variety . '%'));
        }
        if ($request->filled('organic')) {
            $query->whereHas('product', fn($q) => $q->where('is_organic', true));
        }
        if ($request->filled('governorate')) {
            $gov = $request->governorate;
            $query->whereHas('seller.addresses', fn($q) => $q->where('governorate', 'like', '%' . $gov . '%')->orWhere('delegation', 'like', '%' . $gov . '%'));
        }

        $listings = $query->latest('updated_at')->paginate(12)->withQueryString();

        $totalBulkLitres = Cache::remember('seo_bulk_total_volume', 3600, function() {
            return Listing::where('status', 'active')->sum('quantity') ?: 150000;
        });

        $activeProducersCount = Cache::remember('seo_producers_count', 3600, function() {
            return User::whereIn('role', ['farmer', 'mill', 'packer'])->count();
        });

        // Related educational articles
        $relatedArticles = Article::where('is_active', true)->latest()->take(3)->get();

        return view('public.seo.bulk_olive_oil', compact(
            'listings',
            'totalBulkLitres',
            'activeProducersCount',
            'relatedArticles',
            'locale'
        ));
    }

    /**
     * Verified Tunisian Olive Oil Suppliers & Producers Directory
     */
    public function suppliers(Request $request)
    {
        $locale = app()->getLocale();

        $query = User::with(['addresses', 'listings' => fn($q) => $q->where('status', 'active')])
            ->whereIn('role', ['farmer', 'mill', 'packer']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('governorate')) {
            $gov = $request->governorate;
            $query->whereHas('addresses', fn($q) => $q->where('governorate', 'like', '%' . $gov . '%')->orWhere('delegation', 'like', '%' . $gov . '%'));
        }

        $suppliers = $query->latest()->paginate(16)->withQueryString();

        $millsCount = User::where('role', 'mill')->count();
        $farmersCount = User::where('role', 'farmer')->count();
        $packersCount = User::where('role', 'packer')->count();

        return view('public.seo.suppliers', compact(
            'suppliers',
            'millsCount',
            'farmersCount',
            'packersCount',
            'locale'
        ));
    }

    /**
     * Olive Oil Mills in Tunisia (Moulins / Pressoirs) Directory
     */
    public function mills(Request $request)
    {
        $locale = app()->getLocale();

        $query = User::with(['addresses', 'listings' => fn($q) => $q->where('status', 'active')])
            ->where('role', 'mill');

        if ($request->filled('governorate')) {
            $gov = $request->governorate;
            $query->whereHas('addresses', fn($q) => $q->where('governorate', 'like', '%' . $gov . '%')->orWhere('delegation', 'like', '%' . $gov . '%'));
        }

        $mills = $query->latest()->paginate(16)->withQueryString();
        $totalMills = User::where('role', 'mill')->count();

        return view('public.seo.mills', compact('mills', 'totalMills', 'locale'));
    }

    /**
     * Olive Oil Packaging & Bottling Units (Conditionneurs)
     */
    public function packers(Request $request)
    {
        $locale = app()->getLocale();

        $query = User::with(['addresses', 'listings' => fn($q) => $q->where('status', 'active')])
            ->where('role', 'packer');

        if ($request->filled('governorate')) {
            $gov = $request->governorate;
            $query->whereHas('addresses', fn($q) => $q->where('governorate', 'like', '%' . $gov . '%')->orWhere('delegation', 'like', '%' . $gov . '%'));
        }

        $packers = $query->latest()->paginate(16)->withQueryString();
        $totalPackers = User::where('role', 'packer')->count();

        return view('public.seo.packers', compact('packers', 'totalPackers', 'locale'));
    }

    /**
     * Private Label Olive Oil Manufacturing & Custom Bottling in Tunisia
     */
    public function privateLabel()
    {
        $locale = app()->getLocale();

        $packers = User::with('addresses')
            ->where('role', 'packer')
            ->latest()
            ->take(8)
            ->get();

        $exportListings = Listing::with(['product', 'seller'])
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        return view('public.seo.private_label', compact('packers', 'exportListings', 'locale'));
    }
}
