<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{

    public function upload(UploadedFile $file, string $directory, string $name = ""): string
    {
        //création de son nom, ternaire : est ce qu'il y un name ? si oui met le name, sinon met rien
        $newFilename = ($name ? $name . '-' : '') . uniqid() . '.' . $file->guessExtension();

        $file->move($directory, $newFilename);

        return $newFilename;
    }

    public function delete(string $directory, string $filename): void
    {
        if(file_exists($directory . '/' . $filename)) {
            unlink($directory . '/' . $filename);
        }
    }

}