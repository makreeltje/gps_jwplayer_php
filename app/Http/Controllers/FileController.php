<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    function index(Request $request)
    {
        $filePath = $request->file('vtt')->store('uploads');

        $file = new File();
        $file->filePath = $filePath;
        $file->save();
        echo $filePath;
    }   
}
