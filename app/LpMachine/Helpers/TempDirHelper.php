<?php

namespace App\LpMachine\Helpers;

use Spatie\TemporaryDirectory\TemporaryDirectory;

class TempDirHelper
{
    public static function saveFile($fileName, $fileContents) {
        $tmpDir = new TemporaryDirectory('tmp');
        $tmpFilePath = $tmpDir->path($fileName);

        file_put_contents(base_path($tmpFilePath), $fileContents);

        return $tmpFilePath;
    }
}
