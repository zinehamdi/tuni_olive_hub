<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

  @php
    $locales = ['ar', 'fr', 'en'];

    // Helper: generate hreflang links for a given path
    $hreflangFor = function ($path) use ($locales) {
        $links = '';
        foreach ($locales as $lang) {
            $links .= '    <xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . url($lang . $path) . '"/>' . "\n";
        }
        $links .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . url('ar' . $path) . '"/>' . "\n";
        return $links;
    };

    $staticPaths = [
        '/' => ['priority' => '1.0', 'freq' => 'daily'],
        '/catalog' => ['priority' => '0.9', 'freq' => 'daily'],
        '/prices' => ['priority' => '0.95', 'freq' => 'daily'],
        '/bulk-tunisian-olive-oil' => ['priority' => '0.9', 'freq' => 'daily'],
        '/tunisian-olive-oil-suppliers' => ['priority' => '0.8', 'freq' => 'weekly'],
        '/olive-oil-mills-tunisia' => ['priority' => '0.8', 'freq' => 'weekly'],
        '/olive-oil-packers-tunisia' => ['priority' => '0.8', 'freq' => 'weekly'],
        '/private-label-olive-oil-tunisia' => ['priority' => '0.8', 'freq' => 'weekly'],
        '/servicehub' => ['priority' => '0.8', 'freq' => 'weekly'],
        '/services/pricing' => ['priority' => '0.8', 'freq' => 'weekly'],
        '/about' => ['priority' => '0.6', 'freq' => 'monthly'],
        '/how-it-works' => ['priority' => '0.6', 'freq' => 'monthly'],
        '/olive-varieties' => ['priority' => '0.8', 'freq' => 'monthly'],
        '/register' => ['priority' => '0.7', 'freq' => 'monthly'],
    ];

    $internationalHubUrls = [
        'ar' => url('ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية')),
        'fr' => url('fr/prix-huile-olive-international'),
        'en' => url('en/international-olive-oil-prices'),
    ];
  @endphp

  {{-- Static & Programmatic B2B & National Tunisian Price Hub --}}
  @foreach($staticPaths as $path => $meta)
    @foreach($locales as $lang)
    <url>
      <loc>{{ url($lang . $path) }}</loc>
{!! $hreflangFor($path) !!}      <changefreq>{{ $meta['freq'] }}</changefreq>
      <priority>{{ $meta['priority'] }}</priority>
    </url>
    @endforeach
  @endforeach

  {{-- International & Global Benchmark Price Hub (Localized Semantic Slugs) --}}
  @foreach($internationalHubUrls as $lang => $locUrl)
  <url>
    <loc>{{ $locUrl }}</loc>
    <xhtml:link rel="alternate" hreflang="ar" href="{{ $internationalHubUrls['ar'] }}"/>
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $internationalHubUrls['fr'] }}"/>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $internationalHubUrls['en'] }}"/>
    <xhtml:link rel="alternate" hreflang="x-default" href="{{ $internationalHubUrls['ar'] }}"/>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  @endforeach

  {{-- Premium Export Products --}}
  @if(!empty($premiumProducts))
    @foreach($premiumProducts as $prod)
      @foreach($locales as $lang)
      <url>
        <loc>{{ url($lang . '/products/' . $prod->id) }}</loc>
{!! $hreflangFor('/products/' . $prod->id) !!}        <lastmod>{{ optional($prod->updated_at)->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
      </url>
      @endforeach
    @endforeach
  @endif

  {{-- Dynamic Active Listings --}}
  @foreach($listings as $listing)
    @foreach($locales as $lang)
    <url>
      <loc>{{ url($lang . '/listings/' . $listing->id) }}</loc>
{!! $hreflangFor('/listings/' . $listing->id) !!}      <lastmod>{{ optional($listing->updated_at)->toAtomString() }}</lastmod>
      <changefreq>daily</changefreq>
      <priority>0.8</priority>
    </url>
    @endforeach
  @endforeach

  {{-- Dynamic Educational & B2B Articles --}}
  @foreach($articles as $article)
    @foreach($locales as $lang)
    <url>
      <loc>{{ url($lang . '/articles/' . $article->id) }}</loc>
{!! $hreflangFor('/articles/' . $article->id) !!}      <lastmod>{{ optional($article->updated_at)->toAtomString() }}</lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.75</priority>
    </url>
    @endforeach
  @endforeach

</urlset>
