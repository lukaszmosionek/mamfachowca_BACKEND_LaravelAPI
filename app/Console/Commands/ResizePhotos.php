<?php

namespace App\Console\Commands;

use App\Models\Photo;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ResizePhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photos:resize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resize original photos to thumbnail, medium, and large versions for missing medium sizes.';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ImageService $imageService)
    {
        $this->info("rezising photos...");

        $sizes = Photo::getSizes();

        $photo = Photo::whereNull('medium')->limit(10)->get()->each(function ($photo) use($imageService, $sizes) {
            $photo->thumbnail = $imageService->resizeImage($photo->original, $sizes['thumbnail']['width'], $sizes['thumbnail']['height']);
            $photo->large = $imageService->resizeImage($photo->original, $sizes['large']['width'], $sizes['large']['height']);
            $photo->medium = $imageService->resizeImage($photo->original, $sizes['medium']['width'], $sizes['medium']['height']);
            $photo->save();
        });

        $this->info("Succesfully updated the photos. ".$photo->count().' photos resized.');
    }

}
