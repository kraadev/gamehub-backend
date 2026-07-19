<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Facades\File;

class ZipExtractorService
{
    public function extract(string $zipPath, string $slug)
    {
        $destination = storage_path(
            "app/public/games/html/$slug"
        );

        if (!File::exists($destination)) {
            File::makeDirectory(
                $destination,
                0755,
                true
            );
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath) === TRUE) {

            $zip->extractTo($destination);

            $zip->close();

        }

        return "storage/games/html/$slug/index.html";
    }
}