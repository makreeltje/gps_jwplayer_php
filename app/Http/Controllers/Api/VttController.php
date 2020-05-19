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
        $upload_link = $decoded['link'];
        $upload_response = $jwplatform_api->upload(storage_path($url), $upload_link);
        unlink(storage_path($url));
        return response(['message' => $upload_response["status"]], 200);
    }

    public function GetCaption(Request $request)
    {
    }

    public function SaveCaption(Request $request)
    {
    }

    public function DeleteCaption(Request $request)
    {
    }
}
