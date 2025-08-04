<?php

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

function generatePlaceholder(int $width = 300, int $height = 300, string $text = 'Placeholder ', string $folder='', string $filename='' ): string
{
    $image = Image::canvas($width, $height, '#cccccc');

    $image->text($text.Str::random(20), $width / 2, $height / 2, function ($font) {
        $fontPath = public_path('fonts/OpenSans-Regular.ttf'); // optional
        if (file_exists($fontPath)) {
            $font->file($fontPath);
        }
        $font->size(44);
        $randomColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        $font->color($randomColor);
        $font->align('center');
        $font->valign('center');
    });

    $filename = $filename ?: "placeholder".Str::random(30)."_{$width}x{$height}.png";
    $folder = $folder ?: 'photos/'.now()->format('o-\WW');

    $image->save( storage_path("app/public/").$folder.'/'.$filename );

    return 'storage/'.$folder.'/'.$filename;
}
