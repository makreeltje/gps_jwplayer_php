<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    function index(Request $request)
    {
        $filePath = $request->file('user_file')->store('uploads');

        //$file = File::make(public_path("storage/{$filePath}"));
        //$file-> saveindb();
        echo $filePath;
    }   
}
