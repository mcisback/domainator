<?php

namespace App\Http\Controllers;

use App\LpMachine\Helpers\TemplatesPathsHelper;
use App\LpMachine\Helpers\TranslateHelper;
use App\LpMachine\Helpers\TranslationsHelper;
use App\LpMachine\Helpers\ZipHelper;
use App\Models\FtpServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GeneratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->get('data');

        try {
            $template = $data['template'];
            $fields = $data['fields'];

            $timestampNow = strtotime('now');

            $templatesPath = TemplatesPathsHelper::getTemplatesPath();
            $templatePath = TemplatesPathsHelper::getTemplatePath($template);
            $generatedTemplatePath = TemplatesPathsHelper::getGeneratedTemplatePath($timestampNow);

            if(file_exists($generatedTemplatePath) && is_dir($generatedTemplatePath)) {
                File::cleanDirectory($generatedTemplatePath);
            }

            $data['default_locale'] = $data['default_locale'] ?? 'en';

            $locales = TranslationsHelper::getLocalesByTemplate($template);
            $translations = TranslationsHelper::getTranslations($template, $data['default_locale']);

            $indexFilename = $data['index_filename'] ?? 'index.html';

            // Copy and create directory
            File::copyDirectory($templatePath, $generatedTemplatePath);

            $groupedFields = [];

            foreach ($fields as $field => $type) {
                if(is_string($type)) {
                    $groupedFields[$type][] = $field;
                } else {
                    $groupedFields[$type['type']][] = $field;
                }
            }

            foreach ($groupedFields as $type => $fields) {
                foreach ($fields as $field) {
                    if($type === 'image_url') {
                        $image = $data[$field];
                        $fileName = $image['name'];

                        file_put_contents(
                            "$generatedTemplatePath/images/$fileName",
                            file_get_contents($image['imageData'])
                        );

                        $data[$field] = "images/$fileName";
                    } elseif($type === 'one-per-line-array') {
                        $data[$field] = explode("\n", $data[$field]);
                    }
                }
            }

//            dd($data);

            $generatedTemplateHtml = view("templates.$template.index", [
                'locales' => $locales,
                'translations' => $translations,
                'tr' => new TranslateHelper($translations),
                ...$data
            ])->render();

            File::delete("$generatedTemplatePath/index.blade.php");
            File::delete("$generatedTemplatePath/fields.json");

            File::copy("$templatesPath/trans.php", "$generatedTemplatePath/trans.php");

            file_put_contents("$generatedTemplatePath/$indexFilename", $generatedTemplateHtml);

            $zipFilename = "generated-template-" .  md5(Auth::user()->username) . "-" . strtotime('now') . '.zip';

            ZipHelper::compressDirectory(
                $generatedTemplatePath,
                TemplatesPathsHelper::getZipPath( $zipFilename )
            );

            $data['zipUrl'] = TemplatesPathsHelper::getZipDownloadUrl($zipFilename);
            $data['previewUrl'] = TemplatesPathsHelper::getPreviewUrl($indexFilename, $timestampNow);

            if($data['publishToFtp']) {
                $data['generatedTemplatePath'] = $generatedTemplatePath;
            }

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => collect($data)->except('header_image'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
