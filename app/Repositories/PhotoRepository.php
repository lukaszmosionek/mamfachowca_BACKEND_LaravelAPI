<?php

namespace App\Repositories;

use App\Models\Photo;
use App\Models\Service;
use Illuminate\Support\Str;
use App\Repositories\Contracts\PhotoRepositoryInterface;

class PhotoRepository implements PhotoRepositoryInterface
{
    public function storeForService(Service $service, array $photos): array
    {
        $paths = [];

        foreach ($photos as $photo) {
            $paths[] = [
                'original' => Photo::storeFile($photo),
                'original_filename' => Str::limit($photo->getClientOriginalName(), 255, '')
            ];
        }

        return $service->photos()->createMany($paths)->toArray();
    }

    public function findById(int $id): Photo
    {
        return Photo::findOrFail($id);
    }

    public function delete(Photo $photo): bool
    {
        return $photo->delete();
    }
}
