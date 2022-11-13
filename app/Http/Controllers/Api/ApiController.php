<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\LpMachine\Helpers\TemplatesHelper;
use App\LpMachine\Helpers\TranslationsHelper;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index(Request $request) {
        $action = $request->get('action');

        if(method_exists($this, $action)) {
            return $this->$action($request);
        }

        return response()->json([
            'success' => false,
            'error' => 'Action not allowed',
        ], 403);
    }

    public function getTemplates(Request $request) {
        $getContents = $request->get('getContents') ?? false;

        return response()->json([
            'success' => true,
            'templates' => TranslationsHelper::getTemplates($getContents),
        ]);
    }

    public function getTemplate(Request $request) {
        return response()->json([
            'success' => true,
            'templates' => TranslationsHelper::getTemplates(),
        ]);
    }

    public function getTemplateFields(Request $request) {
        $template = $request->get('template');

        try {
            $fields = TemplatesHelper::getTemplateFields($template);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'templates' => $template,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'fields' => $fields,
        ]);
    }

    public function getLocalesByTemplate(Request $request) {
        $template = $request->get('template');

        return response()->json([
            'success' => true,
            'template' => $template,
            'locales' => TranslationsHelper::getLocalesByTemplate($template),
        ]);
    }

    public function getTranslations(Request $request) {
        $template = $request->get('template');
        $locale = $request->get('locale');

        return response()->json([
            'success' => true,
            'template' => $template,
            'locales' => $locale,
            'translations' => TranslationsHelper::getTranslations($template, $locale),
        ]);
    }
}
