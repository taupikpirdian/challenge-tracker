<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MinioTestController extends Controller
{
    /**
     * Display upload form
     */
    public function index()
    {
        return view('minio-test');
    }

    /**
     * Handle file upload to Minio
     */
    public function upload(Request $request)
    {
        // Validate request
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        try {
            // Get the uploaded file
            $file = $request->file('file');

            // Generate unique filename (without path prefix, putFileAs will add it)
            $filename = uniqid() . '_' . $file->getClientOriginalName();

            // Store file to Minio (S3 disk) with public visibility
            $path = Storage::disk('s3')->putFileAs(
                'uploads',
                $file,
                $filename
            );

            // Set visibility to public (will work once bucket policy is updated)
            Storage::disk('s3')->setVisibility($path, 'public');

            // Generate presigned URL (valid for 1 hour) to ensure access works
            $presignedUrl = Storage::disk('s3')->temporaryUrl($path, now()->addHour());

            // Also generate permanent proxy URL (never expires)
            $proxyUrl = route('file.proxy', ['path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully to Minio',
                'path' => $path,
                'presigned_url' => $presignedUrl,
                'presigned_url_type' => 'expires in 1 hour',
                'proxy_url' => $proxyUrl,
                'proxy_url_type' => 'permanent (never expires)',
                'file_info' => [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all files in Minio bucket
     */
    public function listFiles()
    {
        try {
            $files = Storage::disk('s3')->files('uploads');

            $fileList = [];
            foreach ($files as $file) {
                // Generate both presigned and proxy URLs
                $presignedUrl = Storage::disk('s3')->temporaryUrl($file, now()->addHour());
                $proxyUrl = route('file.proxy', ['path' => $file]);

                $fileList[] = [
                    'path' => $file,
                    'presigned_url' => $presignedUrl,
                    'proxy_url' => $proxyUrl,
                    'size' => Storage::disk('s3')->size($file),
                    'last_modified' => Storage::disk('s3')->lastModified($file),
                ];
            }

            return response()->json([
                'success' => true,
                'files' => $fileList,
                'total' => count($fileList),
                'note' => 'Presigned URLs expire in 1 hour. Proxy URLs are permanent.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list files',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file from Minio
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $deleted = Storage::disk('s3')->delete($request->path);

            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'File deleted successfully' : 'Failed to delete file'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Minio connection
     */
    public function testConnection()
    {
        try {
            // Test by attempting to list files first
            try {
                $files = Storage::disk('s3')->files();
                $fileCount = count($files);
                $canList = true;
            } catch (\Exception $listError) {
                $files = [];
                $fileCount = 0;
                $canList = false;
                $listError = $listError->getMessage();
            }

            // Test by checking if bucket exists
            $exists = Storage::disk('s3')->exists('.test');

            return response()->json([
                'success' => true,
                'message' => 'Connected to Minio',
                'bucket' => env('AWS_BUCKET'),
                'endpoint' => env('AWS_ENDPOINT'),
                'access_key' => env('AWS_ACCESS_KEY_ID'),
                'can_list' => $canList,
                'list_error' => $listError ?? null,
                'total_files' => $fileCount,
                'bucket_exists' => $exists
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to Minio',
                'error' => $e->getMessage(),
                'error_code' => $e->getCode()
            ], 500);
        }
    }
}
