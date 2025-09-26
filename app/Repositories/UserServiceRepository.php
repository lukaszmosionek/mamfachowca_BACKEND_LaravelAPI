<?php

namespace App\Repositories;

use App\Models\Language;
use App\Models\Photo;
use App\Models\Service;
use App\Repositories\Contracts\UserServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class UserServiceRepository implements UserServiceRepositoryInterface
{
    public function getUserServices($user, $perPage = 10): LengthAwarePaginator
    {
        return $user->services()
                    ->with('photos','translations.language')
                    ->latest()
                    ->paginate($perPage);
    }

    public function createService(array $data)
    {
        return auth()->user()->services()->create($data);
    }

    public function addPhotos($service, array $photos)
    {
        $paths = [];
        foreach ($photos as $photo) {
            $paths[] = [
                'original' => Photo::storeFile($photo['file']),
                'original_filename' => Str::limit($photo['file']->getClientOriginalName(), 255, '')
            ];
        }
        $service->photos()->createMany($paths);
    }

    public function addTranslations($service, array $translations, $language)
    {
        $languages = $language::codeIdMap();
        $data = [];
        foreach ($translations as $translation) {
            $data[] = [
                'name' => $translation['name'],
                'description' => $translation['description'],
                'language_id' => $languages[$translation['language']['code']],
            ];
        }
        $service->translations()->createMany($data);
    }

    public function updateService(Service $service, array $data)
    {
        // Exclude photos, translations, provider_id
        $service->update($data);
        return $service;
    }

    public function updateTranslations(Service $service, array $translations, Language $language)
    {
        $languages = $language->pluck('id', 'code')->toArray();

        foreach ($translations as $translation) {
            $service->translations()->updateOrCreate([
                'language_id' => $languages[$translation['language']['code']],
            ], [
                'name' => $translation['name'],
                'description' => $translation['description'],
            ]);
        }
    }

    public function findByIdWithRelations(int $id)
    {
        return Service::with([
            'provider:id,name',
            'provider.availabilities',
            'photos',
            'translations.language:id,code'
        ])->findOrFail($id);
    }
}
