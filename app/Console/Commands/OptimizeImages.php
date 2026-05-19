<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Listing;
use App\Models\Article;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zintoop:optimize-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan all old images in the database, optimize them to WebP, and replace the originals.';

    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Zintoop Image Optimization Scan...');
        
        // 1. Optimize Users
        $this->info('Scanning Users...');
        $users = User::all();
        foreach ($users as $user) {
            // Profile Picture
            if (!empty($user->profile_picture) && is_string($user->profile_picture) && !Str::endsWith($user->profile_picture, '.webp')) {
                $newPath = $this->optimizeAndReplace($user->profile_picture, 'profile-pictures', 800);
                if ($newPath) {
                    $user->profile_picture = $newPath;
                    $user->save();
                    $this->line("✅ Optimized Profile Picture for User ID: {$user->id}");
                }
            }
            
            // Cover Photos
            if (!empty($user->cover_photos) && is_array($user->cover_photos)) {
                $updatedCovers = [];
                $changed = false;
                foreach ($user->cover_photos as $cover) {
                    if (is_string($cover) && !Str::endsWith($cover, '.webp')) {
                        $newCover = $this->optimizeAndReplace($cover, 'cover-photos', 1920);
                        if ($newCover) {
                            $updatedCovers[] = $newCover;
                            $changed = true;
                            $this->line("✅ Optimized Cover Photo for User ID: {$user->id}");
                            continue;
                        }
                    }
                    $updatedCovers[] = $cover;
                }
                if ($changed) {
                    $user->cover_photos = $updatedCovers;
                    $user->save();
                }
            }
        }

        // 2. Optimize Listings
        $this->info('Scanning Listings...');
        $listings = Listing::all();
        foreach ($listings as $listing) {
            if (!empty($listing->media) && is_array($listing->media)) {
                $updatedMedia = [];
                $changed = false;
                foreach ($listing->media as $mediaPath) {
                    if (is_string($mediaPath) && !Str::endsWith($mediaPath, '.webp') && !Str::endsWith($mediaPath, '.mp4')) {
                        $newMedia = $this->optimizeAndReplace($mediaPath, 'listings/' . $listing->id, 1200);
                        if ($newMedia) {
                            $updatedMedia[] = $newMedia;
                            $changed = true;
                            $this->line("✅ Optimized Media for Listing ID: {$listing->id}");
                            continue;
                        }
                    }
                    $updatedMedia[] = $mediaPath;
                }
                if ($changed) {
                    $listing->media = $updatedMedia;
                    $listing->save();
                }
            }
        }
        
        $this->info('🎉 All Done! Old images have been optimized to WebP.');
    }

    private function optimizeAndReplace($oldPath, $directory, $maxWidth)
    {
        $fullPath = storage_path('app/public/' . $oldPath);
        
        if (!file_exists($fullPath)) {
            $this->error("❌ File not found: {$oldPath}");
            return null;
        }

        try {
            $filename = Str::uuid() . '.webp';
            $newPath = $directory . '/' . $filename;
            $newFullPath = storage_path('app/public/' . $newPath);

            // Read, scale, and convert to WebP
            $image = Image::read($fullPath);
            $image->scaleDown(width: $maxWidth);
            $image->toWebp(85)->save($newFullPath);

            // Delete original
            @unlink($fullPath);

            return $newPath;
        } catch (\Exception $e) {
            $this->error("❌ Failed to optimize {$oldPath}: " . $e->getMessage());
            return null;
        }
    }
}
