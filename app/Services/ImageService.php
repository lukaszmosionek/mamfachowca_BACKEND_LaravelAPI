<?php

 namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use App\Models\Photo;

class ImageService
{
    public function storeImageFromUrl(string $url)//: Photo
    {
        ini_set('memory_limit', '512M');

        $imageContents = file_get_contents($url);

        if (strlen($imageContents) > Photo::$uploadLimits['max_size'] ) { // 10MB
            throw new \Exception('Image too large to process. URL:'.$url);
        }

        $originalName = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
        // $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $extension = 'jpg';

        $sizes = Photo::getSizes();

        $paths = [];

        foreach ($sizes as $label => $size) {
            $resizedImage = Image::make($imageContents)->resize($size['width'], $size['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // $weekFolder = "photos/".now()->format('o-\WW');
            // $filename = "{$originalName}_{$label}.{$extension}";
            // $path = "{$weekFolder}/{$filename}";

            // Storage::disk('public')->makeDirectory($weekFolder);
            // Storage::disk('public')->put($path, (string) $resizedImage->encode());

            $paths[$label] = Photo::storeFile((string) $resizedImage->encode(), false);

            $resizedImage->destroy(); // Zwolnij zasoby

            // $paths[$label] = 'storage/'.$path;
        }

        $paths['original'] = Photo::storeFile($imageContents, false);
        $paths['original_filename'] = Str::limit( $originalName, 255, '');
        return $paths;
    }

    public function resizeImage(string $imagePath, int $width = 200, int $height = 200): string
    {
        $image = Image::make(Storage::disk('public')->get($imagePath));

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $resizedPath = 'resized/' . basename($imagePath);
        Storage::disk('public')->put($resizedPath, (string) $image->encode());

        return $resizedPath;
    }

}
