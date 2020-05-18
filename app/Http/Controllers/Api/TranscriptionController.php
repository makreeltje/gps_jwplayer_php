<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Jobs\TranscriptionJob;
use App\Http\Controllers\Controller;

class TranscriptionController extends Controller
{
    public function TranscribeAudio(Request $request){

        $data = $request->input('filePath');
        TranscriptionJob::dispatch($data,"nl-nl");
        return response("success");
    }

}
