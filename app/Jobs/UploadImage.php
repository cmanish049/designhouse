<?php

namespace App\Jobs;

use App\Models\Design;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        $originalFile = storage_path(). '/uploads/original/'. $this->design->image;
        try {
            Image::make($originalFile)
                ->fit(800, 600, function($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($large = storage_path('uploads/large/'. $this->design->image));

            Image::make($originalFile)
                ->fit(250, 200, function($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($thumbnail = storage_path('uploads/thumbnail/'. $this->design->image));

            // store images to permanent disk
            // original image
            if (Storage::disk($disk)->put('uploads/designs/original/'. $this->design->image, fopen($originalFile, 'r+'))) {
                File::delete($originalFile);
                // Storage::disk('tmp')->delete('/uploads/original/'. $this->design->image);
            }

            // large image
            if (Storage::disk($disk)->put('uploads/designs/large/'. $this->design->image, fopen($large, 'r+'))) {
                File::delete($large);
                // Storage::disk('tmp')->delete('uploads/large/'. $this->design->image);
            }

            // thumbnail image
            if (Storage::disk($disk)->put('uploads/designs/thumbnail/'. $this->design->image, fopen($thumbnail, 'r+'))) {
                File::delete($thumbnail);
                // Storage::disk('tmp')->delete('uploads/thumbnail/'. $this->design->image);
            }

            $this->design->update([
                'upload_successful' => true,
            ]);
        } catch(Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
