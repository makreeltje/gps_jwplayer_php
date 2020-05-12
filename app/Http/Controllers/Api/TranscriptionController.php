<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\TranscriptionJob;

class TranscriptionController extends Controller
{
    public function TranscribeAudio(Request $request){
        
        parse_str($request->getContent(),$data);
        
        TranscriptionJob::dispatch($data["filepath"],"nl-nl");
        return response("success");
    }

}
