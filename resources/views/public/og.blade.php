<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta property="og:title" content="{{ ($product->variety ?? 'Product').' #'.$product->id }}" />
  <meta property="og:type" content="product" />
  <meta property="og:url" content="{{ url('/products/'.$product->id) }}" />
  @php $photo = is_array($product->media ?? null) && count($product->media) ? $product->media[0] : null; @endphp
  @if($photo)
  <meta property="og:image" content="{{ $photo }}" />
  @endif
  <meta property="og:description" content="Premium Tunisian Olive Oil" />
  <title>Listing Preview</title>
</head>
<body>
  <h1>Product #{{ $product->id }}</h1>
  @if($photo)
    <img src="{{ $photo }}" alt="photo" width="600" />
  @endif
</body>
</html>
