<?php

namespace App\Services;

use Exception;

class ImportService
{
    /**
     * @throws Exception
     */
    public static function importXLSXFile($file)
    {
        $fileName = $file['name'];
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $uploadFile = $uploadDir . basename($fileName);
        $fileTmpName = $file['tmp_name'];

        if (move_uploaded_file($fileTmpName, $uploadFile)) {
            return $uploadFile;
        }

        throw new Exception('File upload failed. Please try again.');
    }
}