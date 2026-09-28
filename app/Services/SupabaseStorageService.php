<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct()
    {
        $this->url    = rtrim(config('services.supabase.url'), '/');
        $this->key    = config('services.supabase.secret');
        $this->bucket = config('services.supabase.bucket', 'media');
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = "{$folder}/{$filename}";

        $response = Http::withToken($this->key)
            ->withOptions(['verify' => false])
            ->attach('file', file_get_contents($file->getRealPath()), $filename)
            ->post("{$this->url}/storage/v1/object/{$this->bucket}/{$path}");

        if ($response->failed()) {
            throw new \Exception('Upload gagal: ' . $response->body());
        }

        return $path;
    }
}