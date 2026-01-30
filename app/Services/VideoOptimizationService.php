<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoOptimizationService
{
    /**
     * Optimize and save story video
     * Re-encodes to MP4 (H.264/AAC), caps resolution to 720p, and enables faststart.
     */
    public function optimizeStoryVideo(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.mp4';
        $outputDir = storage_path('app/public/story-videos');
        $outputPath = $outputDir . '/' . $filename;

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $inputPath = $file->getRealPath();
        $ffmpeg = trim(shell_exec('command -v ffmpeg'));

        if ($ffmpeg) {
            $cmd = sprintf(
                '%s -y -i %s -vf "scale=min(1280,iw):min(720,ih):force_original_aspect_ratio=decrease" -c:v libx264 -preset veryfast -crf 23 -c:a aac -b:a 128k -movflags +faststart %s 2>&1',
                escapeshellcmd($ffmpeg),
                escapeshellarg($inputPath),
                escapeshellarg($outputPath)
            );
            shell_exec($cmd);
        }

        if (!file_exists($outputPath)) {
            // Fallback: store original (will be larger) but keep flow working
            $stored = $file->storeAs('story-videos', $filename, 'public');
            return $stored;
        }

        return 'story-videos/' . $filename;
    }
}
