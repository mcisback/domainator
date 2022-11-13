<?php

namespace App\LpMachine\Helpers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use ZipArchive;

class ZipHelper
{
    public static function compressDirectory($source, $zipfilename){
        $dir = opendir($source);
        $result = ($dir === false ? false : true);

        if ($result !== false) {

            $rootPath = realpath($source);

            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open($zipfilename, ZipArchive::CREATE | ZipArchive::OVERWRITE );

            // Create recursive directory iterator
            /** @var SplFileInfo[] $files */
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();

            return true;
        } else {
            return false;
        }
    }
}
