<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranslationController extends Controller
{
    public function translateVtt(Request $request)
    {
        $validateData = $request->validate([
            'VttData' => 'required',
            'targetLanguage' => 'required',
            'kind' => 'required',
            'sourceLanguage' => 'required',
        ]);

        if (Auth::check()) {
            $splitFile = $validateData['VttData']; //split the vtt file in associative array //https://github.com/podlove/webvtt-parser
            $translatedSplitVtt = $this->translateStrings($splitFile, $validateData['targetLanguage'], $validateData['sourceLanguage']); //translate string in api logic
            return $translatedSplitVtt;
        }
        return response(['message' => 'Session Expired'], 405);
    }

    public function translateStrings($splitFile, $targetLanguage, $sourceLanguage)
    {
        $splitVttResultTranslated = array();

        foreach ($splitFile as $splitBlockResult) {
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . env("GOOGLE_API_KEY", "") . '&q=' . rawurlencode($splitBlockResult["text"]) . '&source=' . $sourceLanguage . '&target=' . $targetLanguage;
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);
            $responseDecoded = json_decode($response, true);
            dd($responseDecoded);
            curl_close($handle);
            $splitBlockResult["text"] = $responseDecoded["data"]["translations"][0]["translatedText"];
            array_push($splitVttResultTranslated, $splitBlockResult);
        }
        return $splitVttResultTranslated;
    }
}
