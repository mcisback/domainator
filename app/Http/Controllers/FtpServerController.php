<?php

namespace App\Http\Controllers;

use App\LpMachine\Helpers\TempDirHelper;
use App\LpMachine\Helpers\TemplatesPathsHelper;
use App\Models\FtpServer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FtpServerController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:web', 'role_or_permission:admin|admin.ftpserver|ftpserver.manage']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        $user = Auth::user();

        $servers = $user->isAdmin()
            ?
            FtpServer::orderBy('created_at', 'desc')->get()
            :
            FtpServer::orderBy('created_at', 'desc')->where('user_id', $user->id)->get();

        return Inertia('Dashboard/Admin/Servers', [
            'servers' => $servers,
            'users' => Auth::user()->isAdmin() ? User::all() : [],
            'currentUser' => Auth::user()
        ]);
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->all();

        if(!$user->isAdmin()) {
            $data['user_id'] = $user->id;
        }/* else {
            // Admin Logic
        }*/

        try {
            $ftpServer = FtpServer::create($data);

            return response()->json([
                'success' => false,
                'message' => 'FtpServer was successfully added',
                'ftpServer' => $ftpServer,
                'servers' => FtpServer::orderBy('created_at', 'desc')->get(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FtpServer  $ftpServer
     * @return \Illuminate\Http\Response
     */
    public function show(FtpServer $ftpServer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FtpServer  $ftpServer
     * @return \Illuminate\Http\Response
     */
    public function edit(FtpServer $ftpServer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FtpServer  $ftpServer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FtpServer $ftpServer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FtpServer  $ftpServer
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $ftpServerId)
    {
        try {
            $ftpServer = FtpServer::find($ftpServerId);
            $ftpServer->delete();

            return response()->json([
                'success' => true,
                'message' => "FtpServer {$ftpServer->name} has been removed",
                'servers' => FtpServer::orderBy('created_at', 'desc')->get(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function createDriver($data) {
        $data['timeout'] = 10;

        if(strtolower($data['driver']) === 'ftp') {
            unset($data['privateKey']);

            return Storage::createFtpDriver($data);
        } elseif(strtolower($data['driver']) === 'sftp') {
            if(isset($data['password']) && !empty($data['password'])) {
                unset($data['privateKey']);
            } else {
                unset($data['password']);

                $data['privateKey'] = base_path(
                    TempDirHelper::saveFile(
                        $data['username'] . "-" . strtotime("now"),
                        $data['privateKey']
                    )
                );
//                $data['password'] = '';
            }

            $data['ssl'] = true;

//            dd(['HERE: ', $data]);

            return Storage::createSftpDriver($data);
        }
    }

    /**
     * Test an FTP/SFTP/Remote Connection
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function testConnection(Request $request)
    {
        $data = (object) $request->all();

        try {
            $ftp = $this->createDriver([
                'driver' => $data->protocol,
                'host' => $data->host,
                'username' => $data->username ?? '',
                'password' => $data->password ?? '',
                'port'     => $data->port ?? 21,
                'ssl'      => $data->ssl ?? false,
                'privateKey' => $data->privateKey ?? null,
                'root' => $data->path ?? '/',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Connection to server is working',
                'files' => $ftp->files(),
                'directories' => $ftp->allDirectories('/'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function startPublishToFtp(Request $request) {
        $serverId = $request->get('server_id');
        $generatedTemplatePath = $request->get('generatedTemplatePath');

        $ftpServer = FtpServer::find($serverId);

        if(!file_exists($generatedTemplatePath) || !is_dir($generatedTemplatePath)) {
            return response()->json([
                'success' => false,
                'message' => "generatedTemplatePath is null, not a directory or doesn't exist",
            ], 403);
        }

        try {
            $files = collect(File::allFiles($generatedTemplatePath))->map(function($file) use($generatedTemplatePath) {
                return str_replace($generatedTemplatePath, '', $file->getPathname());
            });

            return response()->json([
                'success' => true,
                'message' => 'Retrieved files list successfully',
                'files' => $files,
                'generatedTemplatePath' => $generatedTemplatePath,
                'remotePath' => $ftpServer->path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function publishToFtp(Request $request) {
        $serverId = $request->get('server_id');
        $generatedTemplatePath = $request->get('generatedTemplatePath');
        $remotePath = $request->get('remotePath');
        $templateFile = $request->get('file');

        $ftpServer = FtpServer::find($serverId);

        if(!file_exists($generatedTemplatePath) || !is_dir($generatedTemplatePath)) {
            return response()->json([
                'success' => false,
                'message' => "generatedTemplatePath is null, not a directory or doesn't exist",
            ], 403);
        }

        try {
            $ftp = $this->createDriver([
                'driver' => $ftpServer->protocol,
                'host' => $ftpServer->host,
                'username' => $ftpServer->username ?? '',
                'password' => $ftpServer->password ?? '',
                'port'     => $ftpServer->port ?? 21,
                'ssl'      => $ftpServer->ssl ?? false,
                'privateKey' => $ftpServer->privateKey ?? null,
                'root' => $ftpServer->path ?? '/',
            ]);

            $ftp->put(
                $templateFile,
                file_get_contents(
                    "$generatedTemplatePath/$templateFile"
                )
            );

            return response()->json([
                'success' => true,
                'message' => "File $templateFile uploaded",
                'file' => $templateFile,
                'localPath' => "$generatedTemplatePath/$templateFile",
                'remoteFilePath' => $ftpServer->path . "/$templateFile"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * List Directories From A Server
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listDirectories(Request $request)
    {
        $data = (object) $request->all();

        try {
            $ftp = $this->createDriver([
                'driver' => $data->protocol,
                'host' => $data->host,
                'username' => $data->username ?? '',
                'password' => $data->password ?? '',
                'port'     => $data->port ?? 21,
                'ssl'      => $data->ssl ?? false,
                'privateKey' => $data->privateKey ?? null,
                'root' => $data->path ?? '/',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Directories listing successfull',
                'directories' => $ftp->allDirectories('/'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
