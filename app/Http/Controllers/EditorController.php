<?php

namespace App\Http\Controllers;

use App\LpMachine\Helpers\TemplatesHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EditorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Dashboard/FieldsEditor');
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $template = $request->get('template');
        $fields = $request->get('fields');

//        dd([$template, $fields]);

        try {

            TemplatesHelper::saveTemplateFields($template, $fields);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'template' => $template,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'template' => $template,
            'message' => 'Fields updated',
            'fields' => $fields,
        ]);
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
