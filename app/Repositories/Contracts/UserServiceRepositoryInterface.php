<?php

namespace App\Repositories\Contracts;

use App\Models\Language;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceRepositoryInterface {
    public function getUserServices($user, $perPage = 10): LengthAwarePaginator;
    public function createService(array $data);
    public function addPhotos($service, array $photos);
    public function addTranslations($service, array $translations, Language $language);

    public function updateService(Service $service, array $data);
    public function updateTranslations(Service $service, array $translations, Language $language);

    public function findByIdWithRelations(int $id);
}
