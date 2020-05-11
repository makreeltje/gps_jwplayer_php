<?php

namespace App\Http\Controllers;

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
        $splitFile = $this->splitFile($validateData['filePath']); //split the vtt file in associative array //https://github.com/podlove/webvtt-parser
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

    public function splitFile(String $filePath)
    {
        $parser = new Parser();
        $content = File::get(storage_path($filePath));
        $result = $parser->parse($content);
        return $result;
    }

    /*
    public function splitFile(String $filePath)
    {
        $splitFile = array();
        $counter = 0;
        $string = File::get(storage_path($filePath));
        $SplitTextBlock = explode((PHP_EOL . PHP_EOL), $string);  //split text into blocks
        foreach ($SplitTextBlock as $block) {
            $splitFile[$counter] = $this->splitStrings($block);    //split text blocks
            $counter++;
        }
        return $splitFile;
    }
    */
    
    /*
    public function splitStrings(String $textBlock)
    {
        $split[0] = trim(explode(("\n"), $textBlock)[0]); //timestamp
        $naamZin = explode(("\n"), $textBlock)[1]; //naam + zin
        $split[1] = trim(explode((">"), $naamZin)[0] . ">"); //naam
        $split[2] = trim(explode((">"), $naamZin)[1]); //zin + formatting

        $splitBlockResult = array(
            "Timestamp" => $split[0],
            "Naam" => $split[1],
            "Text" => $split[2],
            "EmptyLine" => "\n"
        );
        return $splitBlockResult;
    }
    */
    
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
        dd($splitVttResultTranslated);
        return $splitVttResultTranslated;
    }
}
