<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    public function landing()
    {
        try {
            $data = Cache::remember('public:landing', 300, function(){
                $featured = Product::query()
                    ->where('export_ready', true)
                    ->latest()
                    ->take(6)
                    ->get(['id','variety','quality','price','media']);
                $map = $featured->map(fn($p) => [
                    'id' => $p->id,
                    'variety' => $p->variety,
                    'quality' => $p->quality,
                    'price' => ['currency' => 'USD', 'unit_price' => (float)$p->price],
                    'photos' => $p->media,
                ]);
                return [
                    'featured' => $map,
                    'message' => 'Welcome to Tunisian Olive Oil Platform',
                ];
            });
        } catch (\Throwable $e) {
            $data = [
                'featured' => [],
                'message' => 'Welcome to Tunisian Olive Oil Platform',
            ];
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function sitemap()
    {
        $listings = \App\Models\Listing::where('status', 'active')->latest()->take(1000)->get(['id','updated_at']);
        $articles = \App\Models\Article::where('is_active', true)->latest()->take(200)->get(['id','updated_at']);
        $premiumProducts = \App\Models\Product::where('export_ready', true)->latest()->take(200)->get(['id','updated_at']);
        
        $regionalSouks = array_keys(\App\Models\SoukPrice::getFamousSouks());
        
        $xml = view('public.sitemap', compact('listings', 'articles', 'premiumProducts', 'regionalSouks'))->render();
        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function rss()
    {
        $items = Product::query()->where('export_ready', true)->latest()->take(500)->get(['id','variety','created_at']);
        $xml = view('public.rss', ['items' => $items])->render();
        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    public function ogListing(Product $product)
    {
        return response()->view('public.og', ['product' => $product])
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    // Public storefront HTML: Catalog + Product detail
    public function catalog(Request $request)
    {
        $q = \App\Models\Listing::query()->with(['seller', 'product', 'product.seller'])
            ->whereHas('product', function ($query) {
                $query->where('export_ready', true);
            });

        if ($request->filled('variety')) {
            $q->whereHas('product', function ($query) use ($request) {
                $query->where('variety', 'like', '%' . $request->variety . '%');
            });
        }
        if ($request->filled('quality')) {
            $q->whereHas('product', function ($query) use ($request) {
                $query->where('quality', 'like', '%' . $request->quality . '%');
            });
        }
        if ($request->filled('organic')) {
            $q->whereHas('product', function ($query) {
                $query->where('is_organic', true);
            });
        }
        if ($request->filled('min_pack')) {
            $q->whereHas('product', function ($query) use ($request) {
                $query->where('weight_kg', '>=', (float)$request->min_pack)
                      ->orWhere('volume_liters', '>=', (float)$request->min_pack);
            });
        }

        if ($request->sort === 'newest') {
            $q->latest();
        } elseif ($request->sort === 'price_asc') {
            $q->orderBy('price', 'asc');
        } else {
            $q->latest(); // Default sort since we can't easily do the premium_rank math on listings without joining
        }

        $paginator = $q->paginate(12);

        return view('public.catalog', [
            'listings' => $paginator,
            'query' => $request->query(),
        ]);
    }

    // (Removed unused gulfProduct method)
}
