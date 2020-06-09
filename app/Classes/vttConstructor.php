<?php
namespace App\Classes;

use DateTime;
use Jwplayer\JwplatformAPI;
class vttConstructor 
{
    public static function constructVtt(Array $cues, String $kind, String $language)
    {
        $implodedVtt = "WEBVTT Kind: {$kind}; Language: {$language} \n\n";
        foreach ($cues as $block)                
        {
            $startTime = (float)$block["start"];
            $endTime = (float)$block["end"];
            
            $startMilSec = count(explode(".", $block["start"])) > 1 ? "." . explode(".", $block["start"])[1] . str_repeat("0", max(3 - strlen(explode(".", $block["start"])[1]), 0)) : ".000";           
            $endMilSec = count(explode(".", $block["end"])) > 1 ? "." . explode(".", $block["end"])[1] . str_repeat("0", max(3 - strlen(explode(".", $block["end"])[1]), 0)) : ".000";

            $implodedVtt .= (sprintf('%02d:%02d:%02d', ($startTime/3600),($startTime/60%60), $startTime%60) . $startMilSec . ' ' . '-->' . ' ');
            $implodedVtt .= (sprintf('%02d:%02d:%02d', ($endTime/3600),($endTime/60%60), $endTime%60) . $endMilSec . "\n");
            if($block["voice"]) $implodedVtt .=  ('<v ' . $block["voice"] . '>'); 
            $implodedVtt .= (str_replace("<v >", "", $block["text"]));
            $implodedVtt .= ("\n\n");
        }
        return $implodedVtt;
    }
    
    //Todo: MOVE DEZE SHIT NAAR DE JUISTE CLASS
    
    public static function uploadVtt($completeVttString,$videoKey,$kind,$label){
        $id =  str_replace(".", "", uniqid( "", true));
        $fp = fopen(storage_path( "app/tempfiles/" . $id . ".vtt"), "wb");
        fwrite($fp, $completeVttString);
        fclose($fp);
        $jwplatform_api = new JwplatformAPI(env("JWPLAYER_API_KEY", ""), env("JWPLAYER_API_SECRET", ""));
        $params = [
                    'video_key' => $videoKey,
                    'kind' => $kind,
                    'label' => $label,
                    'status' => 'ready'
                ];
        $url = "app/tempfiles/" . $id . ".vtt";
        $decoded = json_decode(trim(json_encode($jwplatform_api->call('/videos/tracks/create', $params))), TRUE);
        $upload_link = $decoded['link'];
        $upload_response = $jwplatform_api->upload(storage_path($url), $upload_link);
        unlink(storage_path($url));
    }
}
