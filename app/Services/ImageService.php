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

        // $originalName = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
        $originalName = Str::random(40);
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        // $extension = 'jpg';

        $sizes = Photo::getSizes();

        $paths = [];

        foreach ($sizes as $label => $size) {
            $resizedImage = Image::make($imageContents)->resize($size['width'], $size['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $weekFolder = "photos/".now()->format('o-\WW');
            $filename = "{$originalName}_{$label}.{$extension}";
            $path = "{$weekFolder}/{$filename}";

            Storage::disk('public')->makeDirectory($weekFolder);
            Storage::disk('public')->put($path, (string) $resizedImage->encode());

            $resizedImage->destroy(); // Zwolnij zasoby

            $paths[$label] = 'storage/'.$path;
        }

        return $paths;
    }

    public function store(string $url)//: Photo
    {
        ini_set('memory_limit', '512M');

        $imageContents = file_get_contents($url);

        if (strlen($imageContents) > 10 * 1024 * 1024) { // 10MB
            throw new \Exception('Image too large to process. URL:'.$url);
        }

        // $originalName = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
        $originalName = Str::random(40);
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

        $sizes = Photo::getSizes();

        $paths = [];

        foreach ($sizes as $label => [$width, $height]) {
            $resizedImage = Image::make($imageContents)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $weekFolder = "photos/".now()->format('o-\WW');
            $filename = "{$originalName}_{$label}.{$extension}";
            $path = "{$weekFolder}/{$filename}";

            Storage::disk('public')->makeDirectory($weekFolder);
            Storage::disk('public')->put($path, (string) $resizedImage->encode());

            $resizedImage->destroy(); // Zwolnij zasoby

            $paths[$label] = 'storage/'.$path;
        }

        return $paths;
    }
}
