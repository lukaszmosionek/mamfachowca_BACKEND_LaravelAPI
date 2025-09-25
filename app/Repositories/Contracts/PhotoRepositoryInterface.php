<?php

namespace App\Repositories\Contracts;

use App\Models\Photo;
use App\Models\Service;
use Illuminate\Http\UploadedFile;

interface PhotoRepositoryInterface
{
    public function storeForService(Service $service, array $photos): array;
    public function findById(int $id): Photo;
    public function delete(Photo $photo): bool;
}
