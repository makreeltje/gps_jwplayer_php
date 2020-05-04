<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TranscriptionJob;

class TranscriptionController extends Controller
{
    public function TranscribeAudio(string $filePath){
        
        TranscriptionJob::dispatch($filePath,"nl-nl");
    }


}
