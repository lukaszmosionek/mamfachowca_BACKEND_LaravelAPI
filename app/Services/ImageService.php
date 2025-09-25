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

        try{
            $imageContents = file_get_contents($url);
        }catch(\Exception $e){
            return ('Failed to fetch image from Url: '.$url);
        }

        if (strlen($imageContents) > Photo::$uploadLimits['max_size'] ) { // 10MB
            throw new \Exception('Image too large to process. URL:'.$url);
        }
        $originalName = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
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
            $filename = Str::random(10)."_{$label}.{$extension}";
            $path = "{$weekFolder}/{$filename}";

            Storage::disk('public')->makeDirectory($weekFolder);
            Storage::disk('public')->put($path, (string) $resizedImage->encode() );

            $resizedImage->destroy(); // Zwolnij zasoby

            $paths[$label] = $path;
        }

        $paths['original'] = $weekFolder.'/original-'.Str::random(10).'.jpg';
        $paths['original_filename'] = Str::limit( $originalName.'.'.$extension, 255, '');

        Storage::disk('public')->put($paths['original'], $imageContents);

        return $paths;
    }

    public function resizeImage(string $imagePath, int $width = 200, int $height = 200): string
    {
        ini_set('memory_limit', '512M');

        $image = Image::make(Storage::disk('public')->get($imagePath));

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // $resizedPath = 'resized/' . basename($imagePath);
        $resizedPath = 'avatars/resized/' . Str::random(10).'.jpg';
        Storage::disk('public')->put($resizedPath, (string) $image->encode());

        return $resizedPath;
    }

    public function deletePhotoFiles(Photo $photo): void
    {
        foreach (Photo::getSizeKeys() as $size) {
            if (!empty($photo->{$size})) {
                Storage::disk('public')->delete($photo->{$size});
            }
        }
    }

}
