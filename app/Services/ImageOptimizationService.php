<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Optimize and save profile picture
     * Converts to WebP, resizes to max 800px, quality 85%
     */
    public function optimizeProfilePicture(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.webp';
        $path = storage_path('app/public/profile-pictures/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(storage_path('app/public/profile-pictures'))) {
            mkdir(storage_path('app/public/profile-pictures'), 0755, true);
        }

        // Read, resize, and optimize image
        $image = Image::read($file);
        
        // Resize to max 800x800 (maintains aspect ratio)
        $image->scale(width: 800, height: 800);
        
        // Convert to WebP with 85% quality
        $image->toWebp(85)->save($path);

        return 'profile-pictures/' . $filename;
    }

    /**
     * Optimize and save cover photo
     * Converts to WebP, resizes to max 1920px width, quality 80%
     */
    public function optimizeCoverPhoto(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.webp';
        $path = storage_path('app/public/cover-photos/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(storage_path('app/public/cover-photos'))) {
            mkdir(storage_path('app/public/cover-photos'), 0755, true);
        }

        // Read, resize, and optimize image
        $image = Image::read($file);
        
        // Resize to max 1920px width (maintains aspect ratio)
        $image->scaleDown(width: 1920);
        
        // Convert to WebP with 80% quality
        $image->toWebp(80)->save($path);

        return 'cover-photos/' . $filename;
    }

    /**
     * Delete old image file
     */
    public function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Get optimized image info
     */
    public function getImageInfo(string $path): array
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            return [
                'exists' => false,
                'size' => 0,
                'format' => 'unknown'
            ];
        }

        return [
            'exists' => true,
            'size' => filesize($fullPath),
            'size_kb' => round(filesize($fullPath) / 1024, 2),
            'format' => 'webp',
            'url' => Storage::url($path)
        ];
    }
}
