<?php

namespace App\Http\Controllers;

use App\CommandVariables;
use App\Console\Commands\SplitFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Podlove\Webvtt\Parser;
use Podlove\Webvtt\ParserException;

class FileInterpretationController extends Controller
{
    public function translateVtt(Request $request)
    {
        $validateData = $request->validate([
            'filePath' => 'required',
            'targetLanguage' => 'required',
            'fileName' => 'required',
        ]);
        $splitFile = $this->splitFile($request); //split the vtt file in associative array //https://github.com/podlove/webvtt-parser
        $translatedSplitVtt = $this->translateStrings($splitFile['cues'], $validateData['targetLanguage']); 
        $implodedTranslatedVtt = "WebVTT \n\n";
        foreach ($translatedSplitVtt as $block) {
            $implodedTranslatedVtt .= (gmdate("H:i:s", $block["start"]) . ' ' . '-->' . ' ');
            $implodedTranslatedVtt .= (gmdate("H:i:s", $block["end"]) . "\n");
            $implodedTranslatedVtt .= ('<v ' . $block["voice"] . '>');
            $implodedTranslatedVtt .= ($block["text"]);
            $implodedTranslatedVtt .= ("\n" . "\n");
        }
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . $validateData['fileName'] . ".vtt", "wb");
        fwrite($fp, $implodedTranslatedVtt);
        fclose($fp);
        return $implodedTranslatedVtt;
    }

    public function splitFile($request)
    {
        $result = ($this->dispatch(new SplitFile($request)));
        return $result;
    }
    
    public function translateStrings(array $splitFile, String $targetLanguage)
    {
        $splitVttResultTranslated = array();

        foreach ($splitFile as $splitBlockResult) {
            $apiKey = env("API_KEY", "");
            $text = $splitBlockResult["text"];
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target=' . $targetLanguage;
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
