<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class MinioHelper
{
    /**
     * Get presigned URL for a file
     * Generates a fresh URL every time it's called
     */
    public static function getFileUrl(string $path, int $minutes = 60): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $path,
            now()->addMinutes($minutes)
        );
    }

    /**
     * Get permanent proxy URL for a file (recommended)
     * This URL never expires
     */
    public static function getProxyUrl(string $path): string
    {
        return route('file.proxy', ['path' => $path]);
    }

    /**
     * Upload file and return path only (not URL)
     * Store path in database, generate URL when needed
     */
    public static function uploadFile($file, string $directory = 'uploads'): string
    {
        $filename = uniqid() . '_' . $file->getClientOriginalName();

        $path = Storage::disk('s3')->putFileAs(
            $directory,
            $file,
            $filename
        );

        // Set visibility to public
        Storage::disk('s3')->setVisibility($path, 'public');

        return $path;
    }

    /**
     * Upload file with custom filename
     */
    public static function uploadFileAs($file, string $directory, string $filename): string
    {
        $path = Storage::disk('s3')->putFileAs(
            $directory,
            $file,
            $filename
        );

        // Set visibility to public
        Storage::disk('s3')->setVisibility($path, 'public');

        return $path;
    }

    /**
     * Check if file exists
     */
    public static function fileExists(string $path): bool
    {
        return Storage::disk('s3')->exists($path);
    }

    /**
     * Delete file
     */
    public static function deleteFile(string $path): bool
    {
        return Storage::disk('s3')->delete($path);
    }

    /**
     * Delete multiple files
     */
    public static function deleteFiles(array $paths): bool
    {
        return Storage::disk('s3')->delete($paths);
    }

    /**
     * Get file size
     */
    public static function getFileSize(string $path): int
    {
        return Storage::disk('s3')->size($path);
    }

    /**
     * Get file mime type
     */
    public static function getMimeType(string $path): string
    {
        return Storage::disk('s3')->mimeType($path);
    }

    /**
     * Get file info
     */
    public static function getFileInfo(string $path): array
    {
        return [
            'exists' => Storage::disk('s3')->exists($path),
            'size' => Storage::disk('s3')->size($path),
            'mime_type' => Storage::disk('s3')->mimeType($path),
            'url' => self::getProxyUrl($path),
            'presigned_url' => self::getFileUrl($path),
        ];
    }

    /**
     * Get public URL (works only if bucket is set to public)
     * Falls back to presigned URL if not public
     */
    public static function getPublicUrl(string $path): string
    {
        try {
            return Storage::disk('s3')->url($path);
        } catch (\Exception $e) {
            return self::getProxyUrl($path);
        }
    }

    /**
     * Copy file
     */
    public static function copyFile(string $from, string $to): bool
    {
        return Storage::disk('s3')->copy($from, $to);
    }

    /**
     * Move file
     */
    public static function moveFile(string $from, string $to): bool
    {
        return Storage::disk('s3')->move($from, $to);
    }

    /**
     * Get all files in a directory
     */
    public static function files(string $directory = ''): array
    {
        return Storage::disk('s3')->files($directory);
    }

    /**
     * Get all directories in a directory
     */
    public static function directories(string $directory = ''): array
    {
        return Storage::disk('s3')->directories($directory);
    }

    /**
     * Create directory
     */
    public static function makeDirectory(string $directory): bool
    {
        return Storage::disk('s3')->makeDirectory($directory);
    }

    /**
     * Delete directory
     */
    public static function deleteDirectory(string $directory): bool
    {
        return Storage::disk('s3')->deleteDirectory($directory);
    }
}
