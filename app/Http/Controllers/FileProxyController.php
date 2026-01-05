<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class FileProxyController extends Controller
{
    /**
     * Get file via proxy route
     * Generates fresh presigned URL and redirects to it
     *
     * Usage: /file/{path}
     * Example: /file/uploads/image.jpg
     */
    public function show(Request $request, $path)
    {
        try {
            // Check if file exists
            if (!Storage::disk('s3')->exists($path)) {
                abort(404, 'File not found');
            }

            // Generate fresh presigned URL (valid for 1 hour)
            $url = Storage::disk('s3')->temporaryUrl(
                $path,
                now()->addHour()
            );

            // Redirect to the actual file URL
            return Redirect::away($url);

        } catch (\Exception $e) {
            abort(500, 'Error loading file: ' . $e->getMessage());
        }
    }

    /**
     * Alternative: Stream file directly instead of redirecting
     * This way the URL never changes
     */
    public function stream(Request $request, $path)
    {
        try {
            // Check if file exists
            if (!Storage::disk('s3')->exists($path)) {
                abort(404, 'File not found');
            }

            // Get file content
            $file = Storage::disk('s3')->get($path);
            $mimeType = Storage::disk('s3')->mimeType($path);

            // Stream the file directly
            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"')
                ->header('Cache-Control', 'max-age=3600, public'); // Cache for 1 hour

        } catch (\Exception $e) {
            abort(500, 'Error loading file: ' . $e->getMessage());
        }
    }
}
