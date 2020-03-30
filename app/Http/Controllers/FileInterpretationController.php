<?php

namespace App\Http\Controllers;

use Carbon\Traits\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileInterpretationController extends Controller
{
    public function EchoFile()
    {
        $result = array();
        $counter = 0;
        $string = File::get(storage_path('app\files\text.vtt'));
        $SplitTextBlock = explode((PHP_EOL.PHP_EOL), $string);  //split text into blocks
        foreach ($SplitTextBlock as $block)                      
        {
            $result[$counter] = $this->splitStrings($block);    //split text blocks
            $counter++;
        }
        var_dump($result);
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
}
