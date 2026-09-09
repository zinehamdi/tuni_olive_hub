<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Listing;
use Illuminate\Support\Facades\Log;

class OgImageService
{
    public const CANVAS_WIDTH = 1200;
    public const CANVAS_HEIGHT = 630;

    /**
     * Get or generate the absolute path of the cached OG image for a listing.
     */
    public function getOrGenerate(Listing $listing, ?string $locale = 'ar'): string
    {
        $cacheDir = storage_path('app/public/og_cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $mediaSignature = is_array($listing->media) ? implode('|', $listing->media) : (string) $listing->media;
        $hash = md5($listing->id . '_' . $mediaSignature . '_' . $listing->price . '_' . $listing->updated_at . '_' . $locale);
        $cachePath = $cacheDir . '/listing_' . $listing->id . '_' . $hash . '.jpg';

        if (file_exists($cachePath) && filesize($cachePath) > 1000) {
            return $cachePath;
        }

        try {
            $this->generate($listing, $cachePath, $locale);
            return $cachePath;
        } catch (\Throwable $e) {
            Log::warning('OgImageService generation failed for listing ' . $listing->id . ': ' . $e->getMessage());
            return public_path('images/zintoop-logo.png');
        }
    }

    /**
     * Invalidate all cached OG images for a listing.
     */
    public static function clearCache(Listing $listing): void
    {
        try {
            $cacheDir = storage_path('app/public/og_cache');
            if (is_dir($cacheDir)) {
                $pattern = $cacheDir . '/listing_' . $listing->id . '_*.jpg';
                foreach (glob($pattern) ?: [] as $file) {
                    @unlink($file);
                }
            }
        } catch (\Throwable $e) {
            // Ignore cache clear errors
        }
    }

    /**
     * Generate the 1200x630 composite image with aspect-ratio adaptive layout.
     */
    protected function generate(Listing $listing, string $outputPath, ?string $locale = 'ar'): void
    {
        $w = self::CANVAS_WIDTH;
        $h = self::CANVAS_HEIGHT;

        // 1. Create truecolor canvas
        $canvas = imagecreatetruecolor($w, $h);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // 2. Background: Deep Luxury Olive Slate (#0C140E)
        $bgR = 12;
        $bgG = 20;
        $bgB = 14;
        $bgColor = imagecolorallocate($canvas, $bgR, $bgG, $bgB);
        imagefilledrectangle($canvas, 0, 0, $w, $h, $bgColor);

        for ($i = 0; $i < $h; $i++) {
            $ratio = $i / $h;
            $r = (int) round($bgR + ($ratio * 7));
            $g = (int) round($bgG + ($ratio * 10));
            $b = (int) round($bgB + ($ratio * 7));
            $lineColor = imagecolorallocate($canvas, $r, $g, $b);
            imageline($canvas, 0, $i, $w, $i, $lineColor);
        }

        // 3. Resolve real image paths from listing
        $imagePaths = $this->resolveImagePaths($listing);
        $totalImages = count($imagePaths);

        $heroImg = !empty($imagePaths[0]) ? $this->loadImage($imagePaths[0]) : null;
        if (!$heroImg) {
            $heroImg = $this->loadImage(public_path('images/zintoop-logo.png'));
        }

        $heroW = $heroImg ? imagesx($heroImg) : 1;
        $heroH = $heroImg ? imagesy($heroImg) : 1;
        $heroAspect = $heroW / max(1, $heroH);

        // 4. Content Area: Y=18 to Y=554 (Height = 536px)
        $contentX = 24;
        $contentY = 18;
        $contentW = 1152;
        $contentH = 536;
        $gap = 14;

        // Collect all loaded images and aspect ratios
        $loadedImages = [];
        $aspects = [];
        foreach ($imagePaths as $p) {
            $img = $this->loadImage($p);
            if ($img) {
                $loadedImages[] = $img;
                $aspects[] = imagesx($img) / max(1, imagesy($img));
            }
        }

        if (empty($loadedImages) && $heroImg) {
            $loadedImages[] = $heroImg;
            $aspects[] = $heroAspect;
        }

        $numImages = count($loadedImages);
        $heroAspect = $aspects[0] ?? 1.0;

        if ($numImages <= 1) {
            // ── 1 IMAGE: Smart Centered or Full-Width Hero ──
            if (!empty($loadedImages[0])) {
                if ($heroAspect < 0.85) {
                    // Portrait hero: Centered card 540x536
                    $cardW = 540;
                    $cardX = $contentX + (int) round(($contentW - $cardW) / 2);
                    $this->copySmartFit($canvas, $loadedImages[0], $cardX, $contentY, $cardW, $contentH, 20, $bgR, $bgG, $bgB);
                } elseif ($heroAspect <= 1.25) {
                    // Square/Near-Square hero: Centered card 720x536
                    $cardW = 720;
                    $cardX = $contentX + (int) round(($contentW - $cardW) / 2);
                    $this->copySmartFit($canvas, $loadedImages[0], $cardX, $contentY, $cardW, $contentH, 20, $bgR, $bgG, $bgB);
                } else {
                    // Landscape hero: Full width 1152x536
                    $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $contentW, $contentH, 20, $bgR, $bgG, $bgB);
                }
            }
        } elseif ($numImages === 2) {
            // ── 2 IMAGES ──
            $asp0 = $aspects[0];
            $asp1 = $aspects[1];

            if ($asp0 >= 1.25 && $asp1 >= 1.25) {
                // Both landscape: Top/Bottom 2 horizontal rows
                $topH = 320;
                $botH = $contentH - $topH - $gap; // 202px
                $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $contentW, $topH, 18, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[1], $contentX, $contentY + $topH + $gap, $contentW, $botH, 16, $bgR, $bgG, $bgB);
            } else {
                // Side by side 2 vertical columns
                $leftW = 580;
                $rightW = $contentW - $leftW - $gap; // 558px
                $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $leftW, $contentH, 18, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[1], $contentX + $leftW + $gap, $contentY, $rightW, $contentH, 16, $bgR, $bgG, $bgB);
            }
        } elseif ($numImages === 3) {
            // ── 3 IMAGES: Listing 82 / Adaptive Tri-Layout ──
            $asp0 = $aspects[0];
            $asp1 = $aspects[1];
            $asp2 = $aspects[2];

            if ($asp0 < 1.25 && ($asp1 < 1.25 || $asp2 < 1.25)) {
                // 3 Vertical Columns Side-by-Side: Main Hero Left + 2 Vertical Cards on Side
                // Perfect for bottles, cans, portrait products so 100% is visible with zero cropping
                $leftW = 470;
                $sideW = (int) round(($contentW - $leftW - ($gap * 2)) / 2); // 327px
                $midX = $contentX + $leftW + $gap; // 508
                $rightX = $midX + $sideW + $gap; // 849

                $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $leftW, $contentH, 18, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[1], $midX, $contentY, $sideW, $contentH, 16, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[2], $rightX, $contentY, $sideW, $contentH, 16, $bgR, $bgG, $bgB);
            } elseif ($asp0 < 1.25) {
                // Hero is Portrait, but secondaries are Landscape: Left Hero + 2 Stacked Right Cards
                $leftW = 580;
                $rightX = $contentX + $leftW + $gap; // 618
                $rightW = $contentW - $leftW - $gap; // 558
                $subH = (int) round(($contentH - $gap) / 2); // 261px

                $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $leftW, $contentH, 18, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[1], $rightX, $contentY, $rightW, $subH, 14, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[2], $rightX, $contentY + $subH + $gap, $rightW, $subH, 14, $bgR, $bgG, $bgB);
            } else {
                // Hero is Landscape: Top Banner + Bottom 2 Thumbnails Strip
                $topH = 356;
                $botH = $contentH - $topH - $gap; // 166px
                $subW = (int) round(($contentW - $gap) / 2); // 569px

                $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $contentW, $topH, 18, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[1], $contentX, $contentY + $topH + $gap, $subW, $botH, 14, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[2], $contentX + $subW + $gap, $contentY + $topH + $gap, $subW, $botH, 14, $bgR, $bgG, $bgB);
            }
        } else {
            // ── 4+ IMAGES ──
            $asp0 = $aspects[0];

            if ($asp0 < 1.25) {
                // Hero Portrait/Square on Left, Grid on Right
                $leftW = 540;
                $rightX = $contentX + $leftW + $gap; // 578
                $rightW = $contentW - $leftW - $gap; // 598

                // Left: Hero
                $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $leftW, $contentH, 18, $bgR, $bgG, $bgB);

                // Right: Top Card + Bottom 2 Cards (with +N overlay if > 4)
                $topH = (int) round(($contentH - $gap) / 2); // 261px
                $botH = $contentH - $topH - $gap; // 261px
                $botW = (int) round(($rightW - $gap) / 2); // 292px

                $this->copySmartFit($canvas, $loadedImages[1], $rightX, $contentY, $rightW, $topH, 14, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[2], $rightX, $contentY + $topH + $gap, $botW, $botH, 14, $bgR, $bgG, $bgB);

                $thumb4X = $rightX + $botW + $gap;
                $thumb4Y = $contentY + $topH + $gap;
                $this->copySmartFit($canvas, $loadedImages[3], $thumb4X, $thumb4Y, $botW, $botH, 14, $bgR, $bgG, $bgB);

                if ($numImages > 4) {
                    $this->drawMoreBadge($canvas, $thumb4X, $thumb4Y, $botW, $botH, $numImages - 3, $locale);
                }
            } else {
                // Hero Landscape on Top, Strip of 3 Thumbnails on Bottom
                $topH = 356;
                $botH = $contentH - $topH - $gap; // 166px
                $subW = (int) round(($contentW - ($gap * 2)) / 3); // 374px

                $this->copySmartFit($canvas, $loadedImages[0], $contentX, $contentY, $contentW, $topH, 18, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[1], $contentX, $contentY + $topH + $gap, $subW, $botH, 14, $bgR, $bgG, $bgB);
                $this->copySmartFit($canvas, $loadedImages[2], $contentX + $subW + $gap, $contentY + $topH + $gap, $subW, $botH, 14, $bgR, $bgG, $bgB);

                $thumb3X = $contentX + (($subW + $gap) * 2);
                $thumb3Y = $contentY + $topH + $gap;
                $this->copySmartFit($canvas, $loadedImages[3], $thumb3X, $thumb3Y, $subW, $botH, 14, $bgR, $bgG, $bgB);

                if ($numImages > 4) {
                    $this->drawMoreBadge($canvas, $thumb3X, $thumb3Y, $subW, $botH, $numImages - 3, $locale);
                }
            }
        }

        // Clean up loaded images
        foreach ($loadedImages as $img) {
            imagedestroy($img);
        }

        // 5. Draw Bottom Bar: Circular Logo + Platform Name + Morphoglass Pill Button
        $this->drawBottomBar($canvas, $listing, $locale, $bgR, $bgG, $bgB);

        // 6. Save final high-quality JPEG
        imagejpeg($canvas, $outputPath, 92);
        imagedestroy($canvas);
    }

    /**
     * Copy source image to destination with smart containment / card fitting to prevent any cutting.
     */
    protected function copySmartFit(
        \GdImage $dst,
        \GdImage $src,
        int $dstX,
        int $dstY,
        int $dstW,
        int $dstH,
        int $radius,
        int $bgR,
        int $bgG,
        int $bgB
    ): void {
        $srcW = imagesx($src);
        $srcH = imagesy($src);

        if ($srcW <= 0 || $srcH <= 0) {
            return;
        }

        $srcAspect = $srcW / $srcH;
        $dstAspect = $dstW / $dstH;

        // Sample corners to detect if background is white/light
        $samples = [
            imagecolorat($src, min(5, $srcW - 1), min(5, $srcH - 1)),
            imagecolorat($src, max(0, $srcW - 6), min(5, $srcH - 1)),
            imagecolorat($src, min(5, $srcW - 1), max(0, $srcH - 6)),
            imagecolorat($src, max(0, $srcW - 6), max(0, $srcH - 6)),
        ];

        $lightCount = 0;
        foreach ($samples as $c) {
            $r = ($c >> 16) & 0xFF;
            $g = ($c >> 8) & 0xFF;
            $b = $c & 0xFF;
            if ($r > 215 && $g > 215 && $b > 215) {
                $lightCount++;
            }
        }
        $isLightBackground = ($lightCount >= 2);

        $temp = imagecreatetruecolor($dstW, $dstH);
        imagealphablending($temp, true);

        if ($isLightBackground) {
            // Studio shot on white: Clean white card
            $cardBgColor = imagecolorallocate($temp, 255, 255, 255);
            imagefilledrectangle($temp, 0, 0, $dstW, $dstH, $cardBgColor);
        } else {
            // Natural photo: Ultra-smooth ambient blurred background
            $blurW = max(80, (int) round($dstW / 4));
            $blurH = max(60, (int) round($dstH / 4));
            $thumb = imagecreatetruecolor($blurW, $blurH);
            imagecopyresampled($thumb, $src, 0, 0, 0, 0, $blurW, $blurH, $srcW, $srcH);
            for ($bi = 0; $bi < 6; $bi++) {
                imagefilter($thumb, IMG_FILTER_GAUSSIAN_BLUR);
            }
            imagefilter($thumb, IMG_FILTER_BRIGHTNESS, -30);
            imagecopyresampled($temp, $thumb, 0, 0, 0, 0, $dstW, $dstH, $blurW, $blurH);
            imagedestroy($thumb);
        }

        // Always CONTAIN to guarantee 100% of product, bottle, packaging is visible and uncropped
        if ($srcAspect <= $dstAspect) {
            $fitH = $dstH;
            $fitW = (int) round($dstH * $srcAspect);
            $fitX = (int) round(($dstW - $fitW) / 2);
            $fitY = 0;
        } else {
            $fitW = $dstW;
            $fitH = (int) round($dstW / $srcAspect);
            $fitX = 0;
            $fitY = (int) round(($dstH - $fitH) / 2);
        }
        imagecopyresampled($temp, $src, $fitX, $fitY, 0, 0, $fitW, $fitH, $srcW, $srcH);

        // Apply smooth rounded corner mask
        if ($radius > 0) {
            $maskBg = imagecolorallocate($temp, $bgR, $bgG, $bgB);
            for ($x = 0; $x < $radius; $x++) {
                for ($y = 0; $y < $radius; $y++) {
                    $dist = sqrt(pow($radius - $x, 2) + pow($radius - $y, 2));
                    if ($dist > $radius) {
                        imagesetpixel($temp, $x, $y, $maskBg);
                        imagesetpixel($temp, $dstW - 1 - $x, $y, $maskBg);
                        imagesetpixel($temp, $x, $dstH - 1 - $y, $maskBg);
                        imagesetpixel($temp, $dstW - 1 - $x, $dstH - 1 - $y, $maskBg);
                    }
                }
            }
        }

        imagecopy($dst, $temp, $dstX, $dstY, 0, 0, $dstW, $dstH);
        imagedestroy($temp);
    }

    /**
     * Draw Bottom Bar: Circular Logo on left + Morphoglass Pill CTA button on right
     */
    protected function drawBottomBar(\GdImage $canvas, Listing $listing, ?string $locale, int $bgR, int $bgG, int $bgB): void
    {
        $fontCandidates = [
            public_path('fonts/Arial-Bold.ttf'),
            public_path('fonts/Arial.ttf'),
            '/usr/share/fonts/google-droid/DroidSansArabic.ttf',
            '/usr/share/fonts/liberation-mono/LiberationMono-Bold.ttf',
        ];

        $font = null;
        foreach ($fontCandidates as $fc) {
            if (file_exists($fc) && is_readable($fc)) {
                $font = $fc;
                break;
            }
        }

        // ── 1. Circular ZinToop Logo ──
        $logoPath = public_path('images/zintoop-logo.png');
        if (file_exists($logoPath)) {
            $logoRaw = @imagecreatefrompng($logoPath);
            if ($logoRaw) {
                $logoSize = 42;
                $logoTemp = imagecreatetruecolor($logoSize, $logoSize);
                imagealphablending($logoTemp, true);
                imagecopyresampled($logoTemp, $logoRaw, 0, 0, 0, 0, $logoSize, $logoSize, imagesx($logoRaw), imagesy($logoRaw));

                // Circular mask
                $maskColor = imagecolorallocate($logoTemp, $bgR, $bgG, $bgB);
                $rad = $logoSize / 2;
                for ($x = 0; $x < $logoSize; $x++) {
                    for ($y = 0; $y < $logoSize; $y++) {
                        $dist = sqrt(pow($rad - $x, 2) + pow($rad - $y, 2));
                        if ($dist > $rad) {
                            imagesetpixel($logoTemp, $x, $y, $maskColor);
                        }
                    }
                }
                imagecopy($canvas, $logoTemp, 24, 570, 0, 0, $logoSize, $logoSize);
                imagedestroy($logoTemp);
                imagedestroy($logoRaw);
            }
        }

        $gold = imagecolorallocate($canvas, 212, 175, 55); // #D4AF37
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $gray = imagecolorallocate($canvas, 165, 185, 165);

        // ── 2. Platform Branding Text ──
        if ($font) {
            try {
                imagettftext($canvas, 22, 0, 78, 600, $gold, $font, 'ZinToop');
                imagettftext($canvas, 12, 0, 188, 598, $gray, $font, '•  Tunisian Olive Oil Platform');
            } catch (\Throwable $e) {
                imagestring($canvas, 5, 78, 582, 'ZinToop Platform', $gold);
            }
        } else {
            imagestring($canvas, 5, 78, 582, 'ZinToop Platform', $gold);
        }

        // ── 3. Morphoglass Pill Button (Olive Green -> Oil Gold Gradient) ──
        $btnW = 214;
        $btnH = 46;
        $btnX = self::CANVAS_WIDTH - 24 - $btnW; // 962
        $btnY = 568;
        $btnRad = (int) ($btnH / 2); // 23px full pill

        $btnTemp = imagecreatetruecolor($btnW, $btnH);
        imagealphablending($btnTemp, true);

        // Gradient: Start #265222 (Deep Olive Green), End #C8A356 (Yellow Gold)
        $gR1 = 38;
        $gG1 = 82;
        $gB1 = 34;
        $gR2 = 200;
        $gG2 = 163;
        $gB2 = 86;

        for ($x = 0; $x < $btnW; $x++) {
            $ratio = $x / $btnW;
            $r = (int) round($gR1 + (($gR2 - $gR1) * $ratio));
            $g = (int) round($gG1 + (($gG2 - $gG1) * $ratio));
            $b = (int) round($gB1 + (($gB2 - $gB1) * $ratio));
            $gradCol = imagecolorallocate($btnTemp, $r, $g, $b);
            imageline($btnTemp, $x, 0, $x, $btnH, $gradCol);
        }

        // Morphoglass Sheen (Translucent highlight on upper 45%)
        for ($y = 0; $y < (int) ($btnH * 0.45); $y++) {
            $alpha = (int) round(75 + (($y / ($btnH * 0.45)) * 40));
            $sheen = imagecolorallocatealpha($btnTemp, 255, 255, 255, $alpha);
            imageline($btnTemp, 0, $y, $btnW, $y, $sheen);
        }

        // Full Pill Rounded Mask
        $maskBg = imagecolorallocate($btnTemp, $bgR, $bgG, $bgB);
        for ($x = 0; $x < $btnW; $x++) {
            for ($y = 0; $y < $btnH; $y++) {
                if ($x < $btnRad) {
                    $d = sqrt(pow($btnRad - $x, 2) + pow($btnRad - $y, 2));
                    if ($d > $btnRad) {
                        imagesetpixel($btnTemp, $x, $y, $maskBg);
                    }
                }
                if ($x >= $btnW - $btnRad) {
                    $d = sqrt(pow($btnRad - ($btnW - 1 - $x), 2) + pow($btnRad - $y, 2));
                    if ($d > $btnRad) {
                        imagesetpixel($btnTemp, $x, $y, $maskBg);
                    }
                }
            }
        }

        imagecopy($canvas, $btnTemp, $btnX, $btnY, 0, 0, $btnW, $btnH);
        imagedestroy($btnTemp);

        // CTA Button Text (Localized based on share URL locale)
        $btnTexts = [
            'fr' => 'Contacter le Vendeur',
            'en' => 'Contact Seller',
            'it' => 'Contatta il Venditore',
            'es' => 'Contactar al Vendedor',
            'de' => 'Verkäufer Kontaktieren',
            'ar' => 'Contact Seller',
        ];
        $btnText = $btnTexts[$locale] ?? 'Contact Seller';

        if ($font) {
            try {
                $bbox = imagettfbbox(14, 0, $font, $btnText);
                $txtW = abs($bbox[4] - $bbox[0]);
                $txtH = abs($bbox[5] - $bbox[1]);
                $tx = $btnX + (int) round(($btnW - $txtW) / 2);
                $ty = $btnY + (int) round(($btnH + $txtH) / 2) - 2;

                $shadow = imagecolorallocate($canvas, 15, 30, 15);
                imagettftext($canvas, 14, 0, $tx + 1, $ty + 1, $shadow, $font, $btnText);
                imagettftext($canvas, 14, 0, $tx, $ty, $white, $font, $btnText);
                return;
            } catch (\Throwable $e) {
                // fallback
            }
        }
        imagestring($canvas, 5, $btnX + 35, $btnY + 15, $btnText, $white);
    }

    /**
     * Draw "+N Photos" badge overlay on top of the last thumbnail
     */
    protected function drawMoreBadge(\GdImage $canvas, int $x, int $y, int $w, int $h, int $count, ?string $locale = 'ar'): void
    {
        $overlay = imagecolorallocatealpha($canvas, 10, 18, 12, 45); // ~65% opacity
        imagefilledrectangle($canvas, $x, $y, $x + $w, $y + $h, $overlay);

        $fontCandidates = [
            public_path('fonts/Arial-Bold.ttf'),
            public_path('fonts/Arial.ttf'),
            '/usr/share/fonts/google-droid/DroidSansArabic.ttf',
        ];

        $font = null;
        foreach ($fontCandidates as $fc) {
            if (file_exists($fc) && is_readable($fc)) {
                $font = $fc;
                break;
            }
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        $gold = imagecolorallocate($canvas, 212, 175, 55);

        $badgeTexts = [
            'fr' => '+' . $count . ' PHOTOS',
            'it' => '+' . $count . ' FOTO',
            'es' => '+' . $count . ' FOTOS',
            'de' => '+' . $count . ' FOTOS',
            'en' => '+' . $count . ' MORE',
            'ar' => '+' . $count . ' MORE',
        ];
        $text = $badgeTexts[$locale] ?? ('+' . $count . ' MORE');

        if ($font) {
            try {
                $bbox = imagettfbbox(20, 0, $font, $text);
                $textW = abs($bbox[4] - $bbox[0]);
                $textH = abs($bbox[5] - $bbox[1]);
                $tx = $x + (int) round(($w - $textW) / 2);
                $ty = $y + (int) round(($h + $textH) / 2);
                imagettftext($canvas, 20, 0, $tx, $ty, $gold, $font, $text);
                return;
            } catch (\Throwable $e) {
                // fallback
            }
        }
        imagestring($canvas, 5, $x + (int) ($w / 3), $y + (int) ($h / 2) - 8, $text, $gold);
    }

    /**
     * Load image resource from path with format detection and EXIF rotation
     */
    protected function loadImage(string $path): ?\GdImage
    {
        if (!file_exists($path) || !is_readable($path)) {
            return null;
        }

        $content = @file_get_contents($path);
        if (!$content) {
            return null;
        }

        $img = @imagecreatefromstring($content);
        if (!$img) {
            return null;
        }

        if (function_exists('exif_read_data')) {
            try {
                $exif = @exif_read_data($path);
                if (!empty($exif['Orientation'])) {
                    $orientation = (int) $exif['Orientation'];
                    if ($orientation === 3) {
                        $img = imagerotate($img, 180, 0);
                    } elseif ($orientation === 6) {
                        $img = imagerotate($img, -90, 0);
                    } elseif ($orientation === 8) {
                        $img = imagerotate($img, 90, 0);
                    }
                }
            } catch (\Throwable $e) {
                // Ignore EXIF errors
            }
        }

        return $img;
    }

    /**
     * Resolve absolute paths for all images of a listing
     */
    protected function resolveImagePaths(Listing $listing): array
    {
        $paths = [];

        if (!empty($listing->media) && is_array($listing->media)) {
            foreach ($listing->media as $m) {
                $p = $this->findLocalPath((string) $m);
                if ($p) {
                    $paths[] = $p;
                }
            }
        }

        if (empty($paths) && !empty($listing->product?->media) && is_array($listing->product->media)) {
            foreach ($listing->product->media as $m) {
                $p = $this->findLocalPath((string) $m);
                if ($p) {
                    $paths[] = $p;
                }
            }
        }

        if (empty($paths) && !empty($listing->product?->image_url)) {
            $p = $this->findLocalPath((string) $listing->product->image_url);
            if ($p) {
                $paths[] = $p;
            }
        }

        return $paths;
    }

    /**
     * Locate the file path in local storage or public directories
     */
    protected function findLocalPath(string $m): ?string
    {
        $clean = ltrim($m, '/');

        $candidates = [
            storage_path('app/public/' . $clean),
            public_path('storage/' . $clean),
            public_path($clean),
            storage_path('app/' . $clean),
        ];

        foreach ($candidates as $c) {
            if (file_exists($c) && is_readable($c) && !is_dir($c)) {
                return $c;
            }
        }

        return null;
    }
}
