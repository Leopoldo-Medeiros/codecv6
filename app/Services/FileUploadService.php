<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function upload(UploadedFile $file, string $directory = 'uploads'): ?string
    {
        $filename = Str::random(40).'.'.$file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, 'public');
    }

    public function delete(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    public function replace(?string $oldPath, UploadedFile $file, string $directory = 'uploads'): ?string
    {
        $this->delete($oldPath);

        return $this->upload($file, $directory);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    public function url(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
}
