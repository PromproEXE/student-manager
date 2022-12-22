<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function getFile($fileName)
    {
        return response()->download(base_path('public/files/' . $fileName));
    }

    public function api_uploadFile(Request $request)
    {
        try {
            return Storage::disk('public_upload')->put('/files', $request['file']);
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
