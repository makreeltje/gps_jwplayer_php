<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use App\CommandVariables;
use App\Console\Commands\SplitFile;
use App\Console\Commands\TranslateStrings;
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
        $data = array(
        'splitFile' => $splitFile,
        'targetLanguage' => $validateData['targetLanguage']
        );
        $translatedSplitVtt = $this->translateStrings($data); //translate string in api logic
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

    public function splitFile(string $filePath)
    {
        return ($this->dispatch(new SplitFile($filePath)));
    }
    
    public function translateStrings(array $array)
    {
        return ($this->dispatch(new TranslateStrings($array)));
    }
}
