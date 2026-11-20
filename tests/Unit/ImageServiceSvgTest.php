<?php

namespace Tests\Unit;

use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageServiceSvgTest extends TestCase
{
    public function test_it_stores_safe_svg_contents_for_every_photo_size(): void
    {
        Storage::fake('public');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10" fill="#fff"/></svg>';

        $paths = (new ImageService())->storeSvgContents($svg, 'service.svg');

        $this->assertSame($paths['original'], $paths['thumbnail']);
        $this->assertSame($paths['original'], $paths['medium']);
        $this->assertSame($paths['original'], $paths['large']);
        Storage::disk('public')->assertExists($paths['original']);
        $this->assertSame($svg, Storage::disk('public')->get($paths['original']));
    }

    public function test_it_rejects_svg_with_embedded_scripts(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ImageService())->storeSvgContents(
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>',
            'unsafe.svg'
        );
    }
}
