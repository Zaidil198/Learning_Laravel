<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    /**
     * Simpan gambar ke disk public sebagai WebP yang sudah diperkecil.
     *
     * @return string Path relatif terhadap disk public, mis. "mahasiswa/uuid.webp".
     */
    public function store(UploadedFile $file, string $folder = 'mahasiswa', int $maxWidth = 600): string
    {
        $image = (new ImageManager(new Driver()))->read($file->getRealPath());

        $image->scaleDown(width: $maxWidth);

        $path = $folder.'/'.Str::uuid().'.webp';

        Storage::disk('public')->put($path, $image->toWebp(75)->toString());

        return $path;
    }

    /**
     * Simpan gambar ke disk public dengan dua ukuran: Utama (600px) dan Thumbnail (150px).
     *
     * @return array Mengembalikan array berisi key 'foto' dan 'thumbnail'
     */
    public function storeWithThumbnail(UploadedFile $file, string $folder = 'dosen'): array
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());
        
        $uuid = Str::uuid();
        
        // 1. Proses ukuran utama (600px)
        $imgMain = clone $image;
        $imgMain->scaleDown(width: 600);
        $pathMain = $folder.'/'.$uuid.'.webp';
        Storage::disk('public')->put($pathMain, $imgMain->toWebp(75)->toString());

        // 2. Proses ukuran thumbnail (150px)
        $imgThumb = clone $image;
        $imgThumb->scaleDown(width: 150);
        $pathThumb = $folder.'/'.$uuid.'_thumb.webp';
        Storage::disk('public')->put($pathThumb, $imgThumb->toWebp(75)->toString());

        return [
            'foto' => $pathMain,
            'thumbnail' => $pathThumb,
        ];
    }

    /**
     * Hapus gambar dari disk public.
     */
    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}