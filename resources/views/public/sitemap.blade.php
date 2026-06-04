<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <!-- Static Pages -->
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ url('/register') }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>{{ route('gulf.catalog') }}</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>

  <!-- Dynamic Listings -->
  @foreach($listings as $listing)
  <url>
    <loc>{{ route('listings.show', $listing->id) }}</loc>
    <lastmod>{{ optional($listing->updated_at)->toAtomString() }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach

  <!-- Dynamic Articles -->
  @foreach($articles as $article)
  <url>
    <loc>{{ url('/articles/'.$article->id) }}</loc>
    <lastmod>{{ optional($article->updated_at)->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  @endforeach

  <!-- B2B Gulf Products -->
  @foreach($gulfProducts as $product)
  <url>
    <loc>{{ route('gulf.product', $product->id) }}</loc>
    <lastmod>{{ optional($product->updated_at)->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach
</urlset>
