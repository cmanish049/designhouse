<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use App\Models\Design;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048'],
        ]);

        // get image from the request
        $image = $request->file('image');
        $image_path = $image->getPathName();
        $fileName = time()."_". preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        $tmp = $image->storeAs('uploads/original', $fileName, 'tmp');

        $design = auth()->user()->designs()->create([
            'image' => $fileName,
            'disk' => config('site.upload_disk'),
        ]);

        // dispatch job to handle the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);
    }
}
