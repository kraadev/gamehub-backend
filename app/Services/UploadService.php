<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    public function uploadImage(
        UploadedFile $file,
        string $folder
    ): string {

        $filename =
            Str::uuid() .
            '.' .
            $file->getClientOriginalExtension();

        return $file->storeAs(
            $folder,
            $filename,
            'public'
        );
    }

    public function uploadGame(
        UploadedFile $file,
        string $folder
    ): string {

        $filename =
            Str::slug(pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            ))
            .'-'.time()
            .'.'.$file->getClientOriginalExtension();

        return $file->storeAs(
            $folder,
            $filename,
            'public'
        );
    }
}