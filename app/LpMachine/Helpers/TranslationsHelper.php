<?php

namespace App\LpMachine\Helpers;

class TranslationsHelper
{
    public static function getTemplates($getContents = false) {
        return collect(glob(resource_path('views/templates/*')))->map(function ($template) use($getContents) {
            if($getContents === true) {
                return [
                    'name' => basename($template),
                    'contents' => file_get_contents("$template/index.blade.php"),
                ];
            }

            return basename($template);
        })->filter(function ($map) {
            return $map !== 'trans.php';
        });
    }

    public static function getLocalesByTemplate($template) {
        return collect(glob(resource_path("views/templates/$template/translations/*.json")))->map(function ($locale) {
            return explode('.', basename($locale))[0];
        });
    }

    public static function getTranslations($template, $locale) {
        return json_decode(
            file_get_contents(
                resource_path("views/templates/$template/translations/$locale.json")
            ),
            true
        );
    }

    public static function saveTranslations($template, $locale, $translations) {
        return file_put_contents(
            TemplatesPathsHelper::getTranslationFilePath($template, $locale),
            json_encode($translations)
        );
    }
}
