<?php

namespace App\Http\Controllers;

use Carbon\Traits\Timestamp;
use Dotenv\Result\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Google\Cloud\Translate\V2\TranslateClient;

class FileInterpretationController extends Controller
{
    public function splitFile(Request $request)
    {
        $validateData = $request->validate([
            'fileLocation' => 'required',
            'targetLanguage' => 'required',
        ]);

        $splitFile = array();
        $counter = 0;
        $string = File::get(storage_path($validateData['fileLocation']));
        $SplitTextBlock = explode((PHP_EOL . PHP_EOL), $string);  //split text into blocks
        foreach ($SplitTextBlock as $block) {
            $splitFile[$counter] = $this->splitStrings($block);    //split text blocks
            $counter++;
        }
        //return $splitFile;
        $result = $this->translateStrings($splitFile, $validateData['targetLanguage']);
        return $result;
    }

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
            //"EmptyLine" => " \n"
        );
        return $splitBlockResult;
    }

    public function translateStrings(array $splitFile, String $targetLanguage)
    {
        $Onlytext = array();
        foreach ($splitFile as $splitBlockResult) {
            $apiKey = 'AIzaSyDsuGQFqyB1948NB_ZyHt6w8W_ccX6AkBE';
            $text = $splitBlockResult["Text"];
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target=' . $targetLanguage;
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);                 
            $responseDecoded = json_decode($response, true);
            curl_close($handle);
            array_push($Onlytext, $responseDecoded["data"]["translations"]);
        }
        return $Onlytext;
    }
}
