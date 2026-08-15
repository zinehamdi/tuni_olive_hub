<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

  @php
    $locales = ['ar', 'fr', 'en'];

    // Helper: generate hreflang links for a given path
    // Usage: $hreflangFor('/') or $hreflangFor('/listings/42')
    $hreflangFor = function ($path) use ($locales) {
        $links = '';
        foreach ($locales as $lang) {
            $links .= '    <xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . url($lang . $path) . '"/>' . "\n";
        }
        return $links;
    };
  @endphp

  {{-- Static Pages --}}
  @foreach($locales as $lang)
  <url>
    <loc>{{ url($lang . '/') }}</loc>
{!! $hreflangFor('/') !!}    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  @endforeach

  @foreach($locales as $lang)
  <url>
    <loc>{{ url($lang . '/register') }}</loc>
{!! $hreflangFor('/register') !!}    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach

  @foreach($locales as $lang)
  <url>
    <loc>{{ url($lang . '/catalog') }}</loc>
{!! $hreflangFor('/catalog') !!}    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  @endforeach

  @foreach($locales as $lang)
  <url>
    <loc>{{ url($lang . '/articles/olive-varieties') }}</loc>
{!! $hreflangFor('/articles/olive-varieties') !!}    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach

  {{-- Dynamic Listings --}}
  @foreach($listings as $listing)
  @foreach($locales as $lang)
  <url>
    <loc>{{ url($lang . '/listings/' . $listing->id) }}</loc>
{!! $hreflangFor('/listings/' . $listing->id) !!}    <lastmod>{{ optional($listing->updated_at)->toAtomString() }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach
  @endforeach

  {{-- Dynamic Articles --}}
  @foreach($articles as $article)
  @foreach($locales as $lang)
  <url>
    <loc>{{ url($lang . '/articles/' . $article->id) }}</loc>
{!! $hreflangFor('/articles/' . $article->id) !!}    <lastmod>{{ optional($article->updated_at)->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  @endforeach
  @endforeach
</urlset>


