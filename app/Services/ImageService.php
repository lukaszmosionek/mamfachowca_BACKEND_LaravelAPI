<?php

 namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use App\Models\Photo;

class ImageService
{
    public function storeSvgContents(string $svgContents, string $originalFilename): array
    {
        if ($svgContents === '' || strlen($svgContents) > Photo::$uploadLimits['max_size']) {
            throw new \InvalidArgumentException('SVG contents are empty or exceed the maximum allowed size.');
        }

        $document = new \DOMDocument();
        $previousLibxmlErrorSetting = libxml_use_internal_errors(true);
        try {
            $isValidSvg = $document->loadXML($svgContents, LIBXML_NONET | LIBXML_NOBLANKS);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousLibxmlErrorSetting);
        }

        $root = $document->documentElement;
        if (
            !$isValidSvg
            || !$root
            || $root->localName !== 'svg'
            || $root->namespaceURI !== 'http://www.w3.org/2000/svg'
        ) {
            throw new \InvalidArgumentException('Contents must be a static, valid SVG document.');
        }

        foreach ($document->getElementsByTagName('*') as $element) {
            if (in_array(strtolower($element->localName), ['script', 'foreignobject', 'style', 'image', 'iframe', 'object'], true)) {
                throw new \InvalidArgumentException('SVG contents cannot include active or external elements.');
            }

            foreach ($element->attributes as $attribute) {
                $attributeName = strtolower($attribute->localName);
                if (
                    strpos($attributeName, 'on') === 0
                    || $attributeName === 'style'
                    || $attributeName === 'src'
                    || ($attributeName === 'href' && strpos($attribute->value, '#') !== 0)
                ) {
                    throw new \InvalidArgumentException('SVG contents cannot include active or external attributes.');
                }
            }
        }

        $folder = 'photos/' . now()->format('o-\WW');
        $filename = Str::random(10) . '-' . Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.svg';
        $path = $folder . '/' . $filename;

        Storage::disk('public')->makeDirectory($folder);
        if (!Storage::disk('public')->put($path, $svgContents)) {
            throw new \RuntimeException('Failed to store generated SVG image.');
        }

        $paths = array_fill_keys(Photo::getSizeKeys(), $path);
        $paths['original'] = $path;
        $paths['original_filename'] = Str::limit($originalFilename, 255, '');

        return $paths;
    }

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
