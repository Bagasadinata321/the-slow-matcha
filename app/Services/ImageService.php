<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Compress, resize, convert image to WebP, and save to storage.
     */
    public static function compressAndUpload($file, string $folder, int $maxWidth = 1000, int $quality = 80): array
    {
        // 1. Inisialisasi Image Manager dengan GD Driver
        $manager = new ImageManager(new Driver());
        
        // 2. Baca file image
        $image = $manager->read($file->getRealPath());

        // 3. Resize proporsional jika lebar melebihi batas $maxWidth
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        // 4. Encode ke format WebP
        $encodedImage = $image->toWebp($quality);

        // 5. Generate nama file unik & path
        $filename = Str::random(40) . '.webp';
        $fullPath = rtrim($folder, '/') . '/' . $filename;

        // 6. Simpan file ke Storage Disk Public
        Storage::disk('public')->put($fullPath, (string) $encodedImage);

        // 7. Return metadata sesuai tabel media
        return [
            'filename'          => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'path'              => $fullPath,
            'mime_type'         => 'image/webp',
            'file_size'         => strlen((string) $encodedImage),
        ];
    }
}