<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0"
     xmlns:g="http://base.google.com/ns/1.0">
  <channel>
    <title>ZinToop — Tunisian Olive Oil Marketplace</title>
    <link>https://zintoop.com</link>
    <description>Best prices for premium Tunisian olive oil directly from producers. Bulk olive oil market, EVOO, organic, virgin olive oil from Tunisia.</description>
    @foreach($listings as $listing)
    @php
        $product   = $listing->product;
        $seller    = $listing->seller;

        /* ── Product title ─────────────────────────────────────────────── */
        $variety   = $product->variety ?? 'Tunisian Olive Oil';
        $quality   = $product->quality ?? '';
        $qualityEn = '';
        $qLow = strtolower($quality);
        if (str_contains($qLow,'evoo')||str_contains($qLow,'ممتاز')||str_contains($qLow,'extra')) {
            $qualityEn = 'Extra Virgin Olive Oil (EVOO)';
        } elseif (str_contains($qLow,'virgin')||str_contains($qLow,'بكر')||str_contains($qLow,'vierge')) {
            $qualityEn = 'Virgin Olive Oil';
        } elseif (str_contains($qLow,'bio')||str_contains($qLow,'organic')||str_contains($qLow,'بيولوجي')) {
            $qualityEn = 'Organic Olive Oil';
        } elseif (str_contains($qLow,'pomace')||str_contains($qLow,'فيتورة')) {
            $qualityEn = 'Pomace Olive Oil';
        } elseif (str_contains($qLow,'raffinee')||str_contains($qLow,'refined')||str_contains($qLow,'مكرر')) {
            $qualityEn = 'Refined Olive Oil';
        } else {
            $qualityEn = trim($quality) ?: 'Premium Olive Oil';
        }
        $title = trim($variety . ' — ' . $qualityEn . ' | Tunisia');

        /* ── Description ───────────────────────────────────────────────── */
        $city       = optional($seller?->addresses?->first())->governorate ?? 'Tunisia';
        $unit       = $listing->unit ?? 'kg';
        $unitLabel  = $unit === 'liter' ? 'L' : 'kg';
        $minOrder   = $listing->min_order ? floatval($listing->min_order) . ' ' . $unitLabel . ' min. order' : '';
        $certStr    = '';
        if (!empty($product->certs) && is_array($product->certs)) {
            $certStr = ' Certified: ' . implode(', ', $product->certs) . '.';
        }
        $description = 'Buy ' . $qualityEn . ' directly from ' . ($seller->name ?? 'Tunisian producer') . ' in ' . $city . ', Tunisia.'
            . ($minOrder ? ' ' . ucfirst($minOrder) . '.' : '')
            . ($product->weight_kg ? ' Pack: ' . $product->weight_kg . 'kg.' : '')
            . ($product->volume_liters ? ' Volume: ' . $product->volume_liters . 'L.' : '')
            . $certStr
            . ' Best olive oil prices direct from producers. Bulk olive oil Tunisia. ZinToop marketplace.';

        /* ── Image ─────────────────────────────────────────────────────── */
        // Priority: listing media → product media → fallback placeholder
        $imageUrl = null;
        $allImages = [];
        if (!empty($listing->media) && is_array($listing->media)) {
            foreach ($listing->media as $m) {
                $allImages[] = url('storage/' . ltrim($m, '/'));
            }
        }
        if (empty($allImages) && !empty($product?->media) && is_array($product->media)) {
            foreach ($product->media as $m) {
                $allImages[] = url('storage/' . ltrim($m, '/'));
            }
        }
        $imageUrl        = $allImages[0] ?? url('images/olive-oil-placeholder.jpg');
        $additionalImages = array_slice($allImages, 1, 9); // Google allows up to 10 additional

        /* ── Price ─────────────────────────────────────────────────────── */
        $price    = number_format(floatval($listing->price), 2, '.', '');
        $currency = strtoupper($listing->currency ?? 'TND');

        /* ── Google Product Category ───────────────────────────────────── */
        // 422 = Food, Beverages & Tobacco > Food Items > Cooking Oils
        $gpc = 422;

        /* ── Availability ─────────────────────────────────────────────── */
        $qty = floatval($listing->quantity ?? 1);
        $availability = $qty > 0 ? 'in stock' : 'out of stock';

        /* ── Condition ─────────────────────────────────────────────────── */
        $condition = 'new';

        /* ── Listing URL ───────────────────────────────────────────────── */
        $listingUrl = url('/listings/' . $listing->id);
    @endphp
    <item>
      <g:id>listing-{{ $listing->id }}</g:id>
      <g:title><![CDATA[{{ $title }}]]></g:title>
      <g:description><![CDATA[{{ $description }}]]></g:description>
      <g:link>{{ $listingUrl }}</g:link>
      <g:image_link>{{ $imageUrl }}</g:image_link>
      @foreach($additionalImages as $addImg)
      <g:additional_image_link>{{ $addImg }}</g:additional_image_link>
      @endforeach
      <g:price>{{ $price }} {{ $currency }}</g:price>
      <g:availability>{{ $availability }}</g:availability>
      <g:condition>{{ $condition }}</g:condition>
      <g:brand>ZinToop</g:brand>
      <g:google_product_category>{{ $gpc }}</g:google_product_category>
      <g:identifier_exists>no</g:identifier_exists>
      <g:product_type><![CDATA[Olive Oil > {{ $qualityEn }} > Tunisia]]></g:product_type>
      @if($product?->is_organic)
      <g:custom_label_0>Organic</g:custom_label_0>
      @endif
      @if($listing->sale_mode)
      <g:custom_label_1>{{ $listing->sale_mode }}</g:custom_label_1>
      @endif
      <g:shipping_weight>{{ $product->weight_kg ?? '' }} kg</g:shipping_weight>
      <g:item_group_id>variety-{{ \Illuminate\Support\Str::slug($variety) }}</g:item_group_id>
    </item>
    @endforeach
  </channel>
</rss>
