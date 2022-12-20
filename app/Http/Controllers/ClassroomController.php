<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function api_getRoomAmount()
    {
        try {
            return [14, 14, 13, 11, 11, 11];
        } catch (Exception $err) {
            return response($err, 403);
        }
    }
}
