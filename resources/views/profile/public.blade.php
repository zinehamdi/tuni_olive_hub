@php
    use Illuminate\Support\Str;

    $isRTL = in_array(app()->getLocale(), ['ar','ar_TN','ar-SA']);

    // ----- Cover URL (prefer controller-provided, else compute) -----
    $computedCoverPath = collect($user->cover_photos ?? [])
        ->flatten()
        ->filter(fn($v) => is_string($v) && trim($v) !== "")
        ->first();
    $coverUrl = $coverUrl ?? ($computedCoverPath ? asset("storage/".$computedCoverPath) : null);

    // ----- Avatar URL (from profile_picture; supports http or storage path) -----
    $avatarRaw = $user->profile_picture ?? null;
    if ($avatarRaw) {
        $avatarUrl = Str::startsWith($avatarRaw, ['http://','https://'])
            ? $avatarRaw
            : asset('storage/'.$avatarRaw);
    } else {
        $avatarUrl = null;
    }

    // ----- Latest Listings for this user (no controller change needed) -----
    $latestListings = $user->listings()->with('product')->latest()->take(8)->get();

    // ----- Gallery (all cover_photos) -----
    $gallery = collect($user->cover_photos ?? [])
        ->flatten()
        ->filter(fn($v) => is_string($v) && trim($v) !== '')
        ->take(12)
        ->map(fn($p) => asset('storage/'.$p));
@endphp

<x-app-layout>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-green-50 py-8 px-4 sm:px-6 lg:px-8" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
    <div class="max-w-7xl mx-auto space-y-8">

      {{-- Header Card --}}
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden relative">
        {{-- Cover --}}
        <div class="relative w-full h-48 sm:h-64 bg-gray-200">
          @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="Cover" class="w-full h-full object-cover">
          @else
            <div class="absolute inset-0 flex items-center justify-center text-gray-500">No cover</div>
          @endif
        </div>

        {{-- Avatar + Basic info --}}
        <div class="px-6 sm:px-8 pb-8">
          <div class="relative -mt-12 sm:-mt-16 flex items-end gap-4">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl ring-4 ring-white bg-gray-100 overflow-hidden">
              @if($avatarUrl)
                <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="Avatar">
              @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">No avatar</div>
              @endif
            </div>
            <div class="pb-2">
              <h1 class="text-2xl sm:text-3xl font-bold">{{ $user->name }}</h1>
              @if(!empty($user->email))
                <p class="text-gray-600 text-sm sm:text-base">{{ $user->email }}</p>
              @endif
              @if(!empty($user->phone))
                <p class="text-gray-600 text-sm sm:text-base">{{ $user->phone }}</p>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Gallery --}}
      @if($gallery->isNotEmpty())
      <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8">
        <h2 class="text-xl font-semibold mb-4">{{ $isRTL ? 'الصور' : 'Photos' }}</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
          @foreach($gallery as $g)
            <a href="{{ $g }}" target="_blank" class="block group rounded-xl overflow-hidden bg-gray-100">
              <img src="{{ $g }}" class="w-full h-36 object-cover group-hover:opacity-90 transition" alt="Photo">
            </a>
          @endforeach
        </div>
      </div>
      @endif
@include('profile.partials.account_info')
      {{-- Latest Listings --}}
      <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8">
        <h2 class="text-xl font-semibold mb-4">{{ $isRTL ? 'آخر العروض' : 'Latest Listings' }}</h2>

        @if($latestListings->isEmpty())
          <p class="text-gray-500">{{ $isRTL ? 'لا توجد عروض.' : 'No listings yet.' }}</p>
        @else
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($latestListings as $listing)
              @php
                $thumb = null;
                $p = $listing->product ?? null;
                if ($p && !empty($p->image_path)) {
                    $thumb = Str::startsWith($p->image_path, ['http://','https://'])
                      ? $p->image_path
                      : asset('storage/'.$p->image_path);
                }
              @endphp
              <a href="{{ route('listings.show', $listing->id) }}" class="block rounded-xl border hover:shadow-md transition overflow-hidden">
                <div class="w-full h-40 bg-gray-100">
                  @if($thumb)
                    <img src="{{ $thumb }}" class="w-full h-full object-cover" alt="Listing">
                  @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">No image</div>
                  @endif
                </div>
                <div class="p-4">
                  <div class="font-semibold truncate">{{ $listing->title ?? ('#'.$listing->id) }}</div>
                  @if(!empty($listing->price))
                    <div class="text-green-700 font-bold mt-1">{{ $listing->price }}</div>
                  @endif
                  @if(!empty($listing->status))
                    <div class="text-xs text-gray-500 mt-1">{{ ucfirst($listing->status) }}</div>
                  @endif
                </div>
              </a>
            @endforeach
          </div>
        @endif
      </div>

    </div>
  </div>
</x-app-layout>
