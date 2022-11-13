<?php

namespace App\LpMachine\Helpers;

class TemplatesHelper
{
    public static function getTemplateFields($template) {
        $templateFieldsPath = TemplatesPathsHelper::getTemplateFieldsPath($template);

        return json_decode(
            file_get_contents($templateFieldsPath),
            true
        );
    }

    public static function saveTemplateFields($template, $fields) {
        $templateFieldsPath = TemplatesPathsHelper::getTemplateFieldsPath($template);

        if(!file_exists($templateFieldsPath) || is_dir($templateFieldsPath)) {
            throw new \Exception('Error: fields.json not found or not a file, inside: ' . $template);
        }

        return file_put_contents(
            $templateFieldsPath,
            json_encode($fields, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        );
    }
}
