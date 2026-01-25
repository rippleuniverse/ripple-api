<?php

namespace App\Traits;

use Error;

trait Files
{
    public function uploadFiles($documentFiles, $folder, $max = 4, $storage = 'public'): array
    {
        $documents = [];

        if (count($documentFiles) > $max) {
            throw new Error("Maximum of $max documents are allowed for upload");
        }

        foreach ($documentFiles as $file) {
            $path = $file->store($folder, $storage);
            array_push($documents, $path);
        }

        return $documents;
    }

    public function uploadFile($file, $folder, $storage = 'public')
    {
        $path = $file->store($folder, $storage);
        return $path;
    }

    public function getFilePath($path): ?string
    {
        if (!$path) {
            return null;
        }
        $filePath = asset('storage/' . $path);

        return $filePath;
    }

    public function getFilePaths($paths): ?array
    {
        if (!$paths) {
            return null;
        }

        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $paths);
    }

    public function deleteFile($path): void
    {
        if (!$path) return;

        $filePath = public_path('storage/' . $path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

}
