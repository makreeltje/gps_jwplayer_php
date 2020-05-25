<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Http;
use Jwplayer\JwplatformAPI;
use Illuminate\Support\Facades\File;
use App\Classes\vttConstructor;
use Podlove\Webvtt\Parser;



class VttController extends Controller
{
    public function UploadCaption(Request $request)
    {
         $validateData = $request->validate([
             'VttData' => 'required',
             'video_key' => 'required',
             'kind' => 'required',
             'label' => 'required',
         ]);
        $completeVttString = vttConstructor::constructVtt($validateData['VttData']);
        $id =  str_replace(".", "", uniqid( "", true));
        $fp = fopen(storage_path( "app/tempfiles/" . $id . ".vtt"), "wb");
        fwrite($fp, $completeVttString);
        fclose($fp);
        $jwplatform_api = new JwplatformAPI(env("JWPLAYER_API_KEY", ""), env("JWPLAYER_API_SECRET", ""));
        $params = [
                    'video_key' => $validateData['video_key'],
                    'kind' => $validateData['kind'],
                    'label' => $validateData['label']
                ];
        $url = "app/tempfiles/" . $id . ".vtt";
        $decoded = json_decode(trim(json_encode($jwplatform_api->call('/videos/tracks/create', $params))), TRUE);
        $upload_link = $decoded ['link'];
        $upload_response = $jwplatform_api->upload(storage_path($url), $upload_link);
        unlink(storage_path($url));
        return response(['message' => $upload_response["status"]], 200);
    }

    public function GetCaption(Request $request)
    {
        $validateData = $request->validate([
            'VttLink' => 'required',
        ]);
        $content = Http::get($validateData['VttLink']);
        $parser = new Parser();
        $content .= "\n";
        $result = $parser->parse($content);

        return $result != null ? response(['VttData' => $result], 200) : response(['error' => 'Internal server error'], 500);
    }
    

    public function SaveCaption(Request $request)
    {
        $validateData = $request->validate([
            'VttLink' => 'required',
        ]);

        //Vanaf de laatse '/'
        //tot het einde -> als .vtt bestaat tot aan .vtt
        
        $explodedLink = explode("/", $validateData['VttLink']);
        $trackKey = str_replace(".vtt", "", $explodedLink[count($explodedLink) - 1]);
        return $trackKey;

    }

    public function DeleteCaption(Request $request)
    {
    }
}
