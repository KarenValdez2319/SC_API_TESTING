<?php

namespace App\Http\Controllers\WebHook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestingST extends Controller
{

    public function st_datos(Request $request)
    {
        return response([$request->input()]);
    } 
}
