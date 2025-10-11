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
                    ->where('is_premium', true)
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
        $items = Product::query()->where('is_premium', true)->where('export_ready', true)->latest()->take(500)->get(['id','updated_at']);
        $xml = view('public.sitemap', ['items' => $items])->render();
        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function rss()
    {
        $items = Product::query()->where('is_premium', true)->where('export_ready', true)->latest()->take(500)->get(['id','variety','created_at']);
        $xml = view('public.rss', ['items' => $items])->render();
        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    public function ogListing(Product $product)
    {
        return response()->view('public.og', ['product' => $product])
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    // Public storefront HTML: Catalog + Product detail
    public function gulfCatalog(Request $request)
    {
        $cacheKey = 'public:catalog:' . md5(json_encode($request->query()));
        $paginator = Cache::tags(['catalog'])->remember($cacheKey, 300, function() use ($request) {
            $q = Product::query()->with('seller')
                ->where('is_premium', true)
                ->where('export_ready', true);

            if ($v = $request->input('variety')) $q->where('variety', $v);
            if ($qf = $request->input('quality')) $q->where('quality', $qf);
            if ($request->filled('organic')) $q->where('is_organic', (bool)$request->boolean('organic'));
            if ($request->filled('min_pack')) $q->where('weight_kg', '>=', (float)$request->input('min_pack'));
            if ($request->filled('max_pack')) $q->where('weight_kg', '<=', (float)$request->input('max_pack'));

            $sort = $request->input('sort','premium_rank');
            if ($sort === 'price_asc') {
                $q->orderBy('price','asc');
            } elseif ($sort === 'newest') {
                $q->orderBy('products.created_at','desc');
            } else {
                $q->leftJoin('users as sellers','sellers.id','=','products.seller_id')
                  ->select('products.*')
                  ->selectRaw('( (CASE WHEN products.certs IS NOT NULL THEN 1 ELSE 0 END)
                               + (COALESCE(sellers.trust_score,0)/100.0)
                               + (CASE WHEN products.weight_kg IS NOT NULL AND products.volume_liters IS NOT NULL THEN 1 ELSE 0.5 END)
                               + (CASE WHEN julianday(?) - julianday(products.created_at) <= 7 THEN 1 ELSE 0 END)
                               ) as premium_rank', [now()])
                  ->orderByDesc('premium_rank');
            }

            $per = max(1, min(30, (int)$request->input('per_page', 12)));
            return $q->paginate($per)->appends($request->query());
        });
        return view('public.catalog', [
            'products' => $paginator,
            'query' => $request->query(),
        ]);
    }

    public function gulfProduct(Product $product)
    {
        if (!$product->is_premium || !$product->export_ready) abort(404);
        $product->load('seller');
        return view('public.product', ['product' => $product]);
    }
}
