<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function saveImage(string $path, string $base64Image): string
    {

        // Decode the base64 string
        $image = base64_decode($base64Image);

        // Generate a unique file name
        $fileName = Str::random(10) . '.png';

        // Save the image to storage
        Storage::disk('public')->put($path . $fileName, $image);
        return config('app.url'). '/storage/' . $path . $fileName;
    }
}
