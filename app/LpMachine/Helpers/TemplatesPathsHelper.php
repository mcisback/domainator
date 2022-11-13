<?php

namespace App\LpMachine\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class TemplatesPathsHelper
{
    public static function getTemplatesPath() {
        return resource_path('views/templates');
    }

    public static function getTemplatePath($template) {
        return resource_path("views/templates/$template");
    }

    public static function getTemplateFieldsPath($template) {
        return resource_path("views/templates/$template/fields.json");
    }

    public static function getTranslationsPath($template) {
        return resource_path("views/templates/$template/translations");
    }

    public static function getTranslationFilePath($template, $locale) {
        return resource_path("views/templates/$template/translations/$locale.json");
    }

    public static function translationFileExist($template, $locale) {
        return file_exists(static::getTranslationFilePath($template, $locale));
    }

    public static function getGeneratedTemplateDirName($timestamp) {
        return "generated-template-" .  md5(Auth::user()->username) . "-" . $timestamp;
    }

    public static function getGeneratedTemplatePath($timestamp) {
        return public_path("generated-templates/" . static::getGeneratedTemplateDirName($timestamp));
    }

    public static function getPreviewUrl($indexFilename, $timestamp) {
        return "/generated-templates/" . static::getGeneratedTemplateDirName($timestamp) . "/$indexFilename?v=$timestamp";
    }


    public static function getDownloadsPath() {
        return public_path("downloads");
    }

    public static function getZipPath($zipFilename) {
        return public_path("downloads/$zipFilename");
    }

    public static function getZipDownloadUrl($zipFilename) {
        return  url('/') . "/downloads/$zipFilename";
    }

}
