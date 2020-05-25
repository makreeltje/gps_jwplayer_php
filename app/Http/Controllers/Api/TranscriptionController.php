<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Jobs\TranscriptionJob;
use App\Http\Controllers\Controller;
use App\File;

class TranscriptionController extends Controller
{
    public function TranscribeAudio(Request $request){
        $validatedData = $request->validate([
            'filePath'=>'required',
            'languageCode'=>'required',
            'videoKey'=>'required',
            'label'=>'required'
        ]);
        
        $audioFile = $validatedData['filePath'];
        $languageCode = $validatedData['languageCode'];
        $videoKey = $validatedData['videoKey'];
        $kind = 'captions';
        $label = $validatedData['label'];
        
        TranscriptionJob::dispatchAfterResponse($audioFile,$languageCode,$videoKey,$kind,$label);
        return response("Transcription Started"); 
    }

}
