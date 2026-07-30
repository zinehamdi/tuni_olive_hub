<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OgImageController extends Controller
{
    /**
     * Generate a 1200x630 Open Graph image for Facebook/Twitter sharing
     */
    public function generate(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        
        $cachePath = 'og-images/listing_' . $id . '_' . $listing->updated_at->timestamp . '.jpg';
        $fullCachePath = storage_path('app/public/' . $cachePath);

        if (file_exists($fullCachePath)) {
            return response()->file($fullCachePath, ['Content-Type' => 'image/jpeg']);
        }

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/og-images'))) {
            mkdir(storage_path('app/public/og-images'), 0755, true);
        }

        // Clear old cached images for this listing
        array_map('unlink', glob(storage_path('app/public/og-images/listing_' . $id . '_*.jpg')));

        // Create 1200x630 canvas (Facebook recommended)
        $imageWidth = 1200;
        $imageHeight = 630;
        $img = imagecreatetruecolor($imageWidth, $imageHeight);
        $bgColor = imagecolorallocate($img, 248, 244, 236); // #F8F4EC
        imagefill($img, 0, 0, $bgColor);

        $sourceImagePath = null;
        if (!empty($listing->media) && is_array($listing->media) && isset($listing->media[0])) {
            $sourceImagePath = storage_path('app/public/' . $listing->media[0]);
        }

        if ($sourceImagePath && file_exists($sourceImagePath)) {
            $mime = mime_content_type($sourceImagePath);
            $sourceImg = null;
            if (strpos($mime, 'jpeg') !== false || strpos($mime, 'jpg') !== false) {
                $sourceImg = @imagecreatefromjpeg($sourceImagePath);
            } elseif (strpos($mime, 'png') !== false) {
                $sourceImg = @imagecreatefrompng($sourceImagePath);
            } elseif (strpos($mime, 'webp') !== false) {
                $sourceImg = @imagecreatefromwebp($sourceImagePath);
            }

            if ($sourceImg) {
                $origW = imagesx($sourceImg);
                $origH = imagesy($sourceImg);
                
                // Crop and resize to cover 1200x630
                $ratio = max($imageWidth / $origW, $imageHeight / $origH);
                $cropW = $imageWidth / $ratio;
                $cropH = $imageHeight / $ratio;
                $cropX = ($origW - $cropW) / 2;
                $cropY = ($origH - $cropH) / 2;

                imagecopyresampled($img, $sourceImg, 0, 0, $cropX, $cropY, $imageWidth, $imageHeight, $cropW, $cropH);
                imagedestroy($sourceImg);
            }
        }

        // Add watermark overlay if logo exists
        $logoPath = public_path('images/zintoop-logo.png');
        if (file_exists($logoPath)) {
            $logo = @imagecreatefrompng($logoPath);
            if ($logo) {
                // Enable alpha blending for the logo
                imagealphablending($img, true);
                $logoW = imagesx($logo);
                $logoH = imagesy($logo);
                
                // Resize logo to max 200px width
                $newLogoW = 200;
                $newLogoH = ($logoH / $logoW) * $newLogoW;
                
                // Position bottom right with 30px padding
                $dstX = $imageWidth - $newLogoW - 30;
                $dstY = $imageHeight - $newLogoH - 30;
                
                imagecopyresampled($img, $logo, $dstX, $dstY, 0, 0, $newLogoW, $newLogoH, $logoW, $logoH);
                imagedestroy($logo);
            }
        }

        // Save
        imagejpeg($img, $fullCachePath, 90);
        imagedestroy($img);

        return response()->file($fullCachePath, ['Content-Type' => 'image/jpeg']);
    }
}
