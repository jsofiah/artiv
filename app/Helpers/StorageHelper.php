<?php

namespace App\Helpers;

class StorageHelper
{
    public static function url(?string $path, string $bucket = 'media'): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');
        $base = rtrim(config('services.supabase.url'), '/');

        return "{$base}/storage/v1/object/public/{$bucket}/{$path}";
    }
}