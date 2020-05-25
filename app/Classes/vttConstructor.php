<?php
namespace App\Classes;

use DateTime;
use Jwplayer\JwplatformAPI;
class vttConstructor 
{
    public static function constructVtt(Array $cues)
    {
        $implodedVtt = "WEBVTT \n\n";
        foreach ($cues as $block) {
            $implodedVtt .= (gmdate("H:i:s", $block["start"]) . ' ' . '-->' . ' ');
            $implodedVtt .= (gmdate("H:i:s", $block["end"]) . "\n");
            $implodedVtt .= ('<v ' . $block["voice"] . '>');
            $implodedVtt .= ($block["text"]);
            $implodedVtt .= ("\n\n");
        }
        return $implodedVtt;
    }
    public static function uploadVtt($completeVttString,$videoKey,$kind,$label){
        $id =  str_replace(".", "", uniqid( "", true));
        $fp = fopen(storage_path( "app/tempfiles/" . $id . ".vtt"), "wb");
        fwrite($fp, $completeVttString);
        fclose($fp);
        $jwplatform_api = new JwplatformAPI(env("JWPLAYER_API_KEY", ""), env("JWPLAYER_API_SECRET", ""));
        $params = [
                    'video_key' => $videoKey,
                    'kind' => $kind,
                    'label' => $label
                ];
        $url = "app/tempfiles/" . $id . ".vtt";
        $decoded = json_decode(trim(json_encode($jwplatform_api->call('/videos/tracks/create', $params))), TRUE);
        $upload_link = $decoded['link'];
        $upload_response = $jwplatform_api->upload(storage_path($url), $upload_link);
        unlink(storage_path($url));
    }
}
