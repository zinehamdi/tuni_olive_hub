<rss version="2.0">
  <channel>
    <title>Tunisian Olive Oil Platform — Premium Feed</title>
    <link>{{ url('/') }}</link>
    <description>Latest premium, export-ready products</description>
    @foreach($items as $item)
    <item>
      <title><![CDATA[Product #{{ $item->id }}]]></title>
      <link>{{ url('/products/'.$item->id) }}</link>
      <guid>{{ url('/products/'.$item->id) }}</guid>
      <pubDate>{{ optional($item->created_at)->toRssString() }}</pubDate>
    </item>
    @endforeach
  </channel>
</rss>
