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
        $imageContents = file_get_contents($url);

        // $originalName = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
        $originalName = Str::random(40);
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

        $sizes = [
            'thumbnail' => [150, 150],
            'medium'    => [300, 300],
            'large'     => [800, 600],
        ];

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

            $paths[$label] = 'storage/'.$path;
        }

        // $photo = Photo::create([
        //     'thumbnail' => $paths['thumbnail'],
        //     'medium'    => $paths['medium'],
        //     'large'     => $paths['large'],
        // ]);

        return $paths;
    }
}
