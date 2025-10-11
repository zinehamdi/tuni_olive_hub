<?php

namespace App\Services\Search;

use MeiliSearch\Client;
use App\Models\Product;
use App\Models\Listing;

class SearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'), env('MEILISEARCH_KEY', ''));
    }

    public function indexProduct(Product $product)
    {
        $this->client->index('products')->addDocuments([
            [
                'id' => $product->id,
                'name' => $product->name,
                'variety' => $product->variety,
                'quality' => $product->quality,
                'price' => $product->price,
                'currency' => $product->currency,
                'is_organic' => $product->is_organic,
                'seller_id' => $product->seller_id,
                'created_at' => $product->created_at,
            ]
        ]);
    }

    public function indexListing(Listing $listing)
    {
        $this->client->index('listings')->addDocuments([
            [
                'id' => $listing->id,
                'title' => $listing->title,
                'product_id' => $listing->product_id,
                'price' => $listing->price,
                'currency' => $listing->currency,
                'status' => $listing->status,
                'created_at' => $listing->created_at,
            ]
        ]);
    }

    public function searchProducts($query, $filters = [])
    {
        return $this->client->index('products')->search($query, [
            'filter' => $filters
        ]);
    }

    public function searchListings($query, $filters = [])
    {
        return $this->client->index('listings')->search($query, [
            'filter' => $filters
        ]);
    }
}
