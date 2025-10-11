<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GulfCatalogController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $cacheKey = 'catalog:' . md5(json_encode($request->query()));
            $data = Cache::tags(['catalog'])->remember($cacheKey, 300, function() use ($request) {
                $q = Product::query()->with('seller')->where('is_premium', true)->where('export_ready', true);
                if ($v = $request->input('variety')) $q->where('variety', $v);
                if ($qf = $request->input('quality')) $q->where('quality', $qf);
                if ($request->filled('organic')) $q->where('is_organic', (bool)$request->boolean('organic'));
                if ($request->filled('min_pack')) $q->where('weight_kg', '>=', (float)$request->input('min_pack'));
                if ($request->filled('max_pack')) $q->where('weight_kg', '<=', (float)$request->input('max_pack'));
                $sort = $request->input('sort','premium_rank');
                if ($sort === 'price_asc') $q->orderBy('price','asc');
                elseif ($sort === 'newest') $q->orderBy('products.created_at','desc');
                else {
                    $q->leftJoin('users as sellers','sellers.id','=','products.seller_id')
                      ->select('products.*')
                      ->selectRaw('( (CASE WHEN products.certs IS NOT NULL THEN 1 ELSE 0 END)
                                   + (COALESCE(sellers.trust_score,0)/100.0)
                                   + (CASE WHEN products.weight_kg IS NOT NULL AND products.volume_liters IS NOT NULL THEN 1 ELSE 0.5 END)
                                   + (CASE WHEN julianday(?) - julianday(products.created_at) <= 7 THEN 1 ELSE 0 END)
                                   ) as premium_rank', [now()])
                      ->orderByDesc('premium_rank');
                }
                $per = max(1, min(50, (int)$request->input('per_page', 15)));
                $p = $q->paginate($per)->appends($request->query());
                $items = collect($p->items())->map(function($prod){
                    return [
                        'id' => $prod->id,
                        'variety' => $prod->variety,
                        'quality' => $prod->quality,
                        'organic' => (bool)$prod->is_organic,
                        'seller' => $prod->relationLoaded('seller') ? ['id'=>$prod->seller->id,'name'=>$prod->seller->name,'role'=>$prod->seller->role] : null,
                        'price' => ['currency' => 'USD', 'unit_price' => (float)$prod->price],
                        'packaging' => ['weight_kg' => $prod->weight_kg, 'volume_liters' => $prod->volume_liters],
                        'certs' => $prod->certs,
                        'marketing' => ['photos' => $prod->media],
                    ];
                });
                $meta = ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage(), 'from' => $p->firstItem(), 'to' => $p->lastItem()];
                return ['items' => $items, 'meta' => $meta];
            });
            return response()->json(['success' => true, 'data' => $data['items'], 'meta' => $data['meta']]);
        } catch (\Throwable $e) {
            return response()->json(['success' => true, 'data' => [], 'meta' => ['current_page'=>1,'per_page'=>15,'total'=>0,'last_page'=>1,'from'=>null,'to'=>null]]);
        }
    }

    public function show(Product $product)
    {
        if (!$product->is_premium || !$product->export_ready) abort(404);
        $product->load('seller');
        return $this->ok([
            'id' => $product->id,
            'variety' => $product->variety,
            'quality' => $product->quality,
            'organic' => (bool)$product->is_organic,
            'seller' => ['id'=>$product->seller->id,'name'=>$product->seller->name,'role'=>$product->seller->role],
            'price' => ['currency' => 'USD', 'unit_price' => (float)$product->price],
            'packaging' => ['weight_kg' => $product->weight_kg, 'volume_liters' => $product->volume_liters],
            'certs' => $product->certs,
            'marketing' => ['photos' => $product->media],
        ]);
    }
}
