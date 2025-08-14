<?php

namespace App\Jobs;

use App\Models\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessServicePhotos implements ShouldQueue
{
    use Queueable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photos;
    protected $service;
    public function __construct($service, $photos)
    {
        $this->service = $service;
        $this->photos = $photos;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $paths = [];

        foreach ($this->photos as $photo) {
            $paths[] = [
                'original' => Photo::storeFile($photo),
                'original_filename' => Str::limit($photo->getClientOriginalName(), 255, '')
            ];
        }

        $this->service->photos()->createMany($paths);
    }
}
