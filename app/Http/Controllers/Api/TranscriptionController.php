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
            "filePath"=>'required',
            "languageCode"=>'required',
            "targetFilePath"=>'nullable'
        ]);
        $audioFile = $request->input('filePath');
        $languageCode = $request->input('languageCode');
        if($request->input('targetFilePath')==null){

            $fileModel= File::create();
            TranscriptionJob::dispatchAfterResponse($audioFile,$languageCode,$fileModel);
            return response()->json(["fileId"=>$fileModel->fileId]);
        }
        else{
            TranscriptionJob::dispatchAfterResponse($audioFile,$languageCode,$request->input('targetFilePath'));
            return response()->json("Job Started");
        }
        
    }

}
