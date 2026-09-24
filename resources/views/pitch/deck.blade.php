@php
    $pitchHtmlPath = public_path('pitch.html');
    if (file_exists($pitchHtmlPath)) {
        echo file_get_contents($pitchHtmlPath);
    }
@endphp
