<?php

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

function generatePlaceholder(int $width = 300, int $height = 300, string $text = '', string $folder='', string $filename='' ): string
{
    $randomColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    $image = Image::canvas($width, $height, $randomColor);
    $text = $text ? $text : 'Photo '.rand(10,99);

    $image->text($text, $width / 2, $height / 2, function ($font) use($randomColor) {
        $font->file(public_path('fonts/arial.ttf'));
        $font->size(50);
        $font->color( invertColor($randomColor) );
        $font->align('center');
        $font->valign('middle');
    });

    $filename = $filename ?: "placeholder".Str::random(30)."_{$width}x{$height}.png";
    $folder = $folder ?: 'photos/'.now()->format('o-\WW');

    $image->save( storage_path("app/public/").$folder.'/'.$filename );

    return 'storage/'.$folder.'/'.$filename;
}

function invertColor($hexColor) {
    // Remove '#' if present
    $hexColor = ltrim($hexColor, '#');

    // If shorthand (e.g. 'abc'), expand it to 'aabbcc'
    if (strlen($hexColor) === 3) {
        $hexColor = $hexColor[0].$hexColor[0] . $hexColor[1].$hexColor[1] . $hexColor[2].$hexColor[2];
    }

    // Convert to decimal RGB and invert each channel
    $r = 255 - hexdec(substr($hexColor, 0, 2));
    $g = 255 - hexdec(substr($hexColor, 2, 2));
    $b = 255 - hexdec(substr($hexColor, 4, 2));

    // Convert back to hex and return
    return sprintf("#%02X%02X%02X", $r, $g, $b);
}

function arrayToObject(array $array): object
{
    return json_decode(json_encode($array));
}
