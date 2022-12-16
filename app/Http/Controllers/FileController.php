<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function getFile($fileName)
    {
        return response()->download(base_path('public/files/' . $fileName));
    }
}
