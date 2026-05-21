import re

with open('resources/views/listings/index.blade.php', 'r') as f:
    content = f.read()

old_td = """          <td class="p-3">{{ $listing->product->quality ?? '-' }}</td>"""
new_td = """          <td class="p-3">
              @if($listing->product->quality)
                  @if($listing->product->quality === 'bio')
                      {{ __('بيولوجي (Bio)') }}
                  @elseif($listing->product->quality === 'evoo')
                      {{ __('بكر ممتاز (EVOO)') }}
                  @elseif($listing->product->quality === 'virgin')
                      {{ __('بكر (Virgin)') }}
                  @elseif($listing->product->quality === 'raffinee')
                      {{ __('مكرر (Raffinée)') }}
                  @elseif($listing->product->quality === 'pomace')
                      {{ __('زيت فيتورة (Pomace)') }}
                  @else
                      {{ $listing->product->quality }}
                  @endif
              @else
                  -
              @endif
          </td>"""

if old_td in content:
    content = content.replace(old_td, new_td)
    with open('resources/views/listings/index.blade.php', 'w') as f:
        f.write(content)
    print("Updated index.blade.php")
else:
    print("Could not find the td block in index.blade.php")

