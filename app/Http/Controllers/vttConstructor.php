<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class vttConstructor extends Controller
{
    public function constructVtt(Array $cues)
    {
        $implodedVtt = "WebVTT \n\n";
        foreach ($cues as $block) {
            $implodedVtt .= (gmdate("H:i:s", $block["start"]) . ' ' . '-->' . ' ');
            $implodedVtt .= (gmdate("H:i:s", $block["end"]) . "\n");
            $implodedVtt .= ('<v ' . $block["voice"] . '>');
            $implodedVtt .= ($block["text"]);
            $implodedVtt .= ("\n" . "\n");
        }
    }
}
