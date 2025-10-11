<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($items as $item)
  <url>
    <loc>{{ url('/products/'.$item->id) }}</loc>
    <lastmod>{{ optional($item->updated_at)->toAtomString() }}</lastmod>
  </url>
@endforeach
</urlset>
