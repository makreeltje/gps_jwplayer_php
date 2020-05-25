<?php
namespace App\Classes;
class vttConstructor 
{
    public static function constructVtt(Array $cues)
    {
        $implodedVtt = "WEBVTT \n\n";
        foreach ($cues as $block)                
        {
            $startTime = (float)$block["start"];
            $endTime = (float)$block["end"];
            
            $startMilSec = count(explode(",", $block["start"])) > 1 ? "." . explode(",", $block["start"])[1] . str_repeat("0", max(3 - strlen(explode(",", $block["start"])[1]), 0)) : ".000";           
            $endMilSec = count(explode(",", $block["end"])) > 1 ? "." . explode(",", $block["end"])[1] . str_repeat("0", max(3 - strlen(explode(",", $block["end"])[1]), 0)) : ".000";

            $implodedVtt .= (sprintf('%02d:%02d:%02d', ($startTime/3600),($startTime/60%60), $startTime%60) . $startMilSec . ' ' . '-->' . ' ');
            $implodedVtt .= (sprintf('%02d:%02d:%02d', ($endTime/3600),($endTime/60%60), $endTime%60) . $endMilSec . "\n");
            $implodedVtt .= ('<v ' . $block["voice"] . '>');
            $implodedVtt .= ($block["text"]);
            $implodedVtt .= ("\n");
        }
        return $implodedVtt;
    }
}
