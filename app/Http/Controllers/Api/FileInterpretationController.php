<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Podlove\Webvtt\Parser;
use App\Classes\vttConstructor;

class FileInterpretationController extends Controller
{
    public function translateVtt(Request $request)
    {
        $validateData = $request->validate([
            'filePath' => 'required',
            'targetLanguage' => 'required',
            'fileName' => 'required',
            'kind' => 'required'
        ]);
        $splitFile = $this->splitFile($validateData['filePath']); //split the vtt file in associative array //https://github.com/podlove/webvtt-parser
        $translatedSplitVtt = $this->translateStrings($splitFile, $validateData['targetLanguage']); //translate string in api logic
        $implodedTranslatedVtt = vttConstructor::constructVtt($translatedSplitVtt, $validateData['kind'],$validateData['targetLanguage']);
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $validateData['fileName'] . ".vtt", "wb");
        fwrite($fp, $implodedTranslatedVtt);
        fclose($fp);
        return $implodedTranslatedVtt;
    }

    public function splitFile(String $filePath)
    {
        $parser = new Parser();
        $content = File::get(storage_path($filePath));
        $result = $parser->parse($content);
        return $result;
    }    
    
    public function translateStrings($splitFile, $targetLanguage)
    {
        $splitVttResultTranslated = array();

        foreach ($splitFile["cues"] as $splitBlockResult) 
        {
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . env("GOOGLE_API_KEY", "") . '&q=' . rawurlencode($splitBlockResult["text"]) . '&source=en&target=' . $targetLanguage;
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);
            $responseDecoded = json_decode($response, true);
            curl_close($handle);
            $splitBlockResult["text"] = $responseDecoded["data"]["translations"][0]["translatedText"];
            array_push($splitVttResultTranslated, $splitBlockResult);
        }
        return $splitVttResultTranslated;
    }
}
