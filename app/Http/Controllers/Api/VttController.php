<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Jwplayer\JwplatformAPI;
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
            'language' => 'required',
        ]);
        $completeVttString = vttConstructor::constructVtt($validateData['VttData'], $validateData['kind'], $validateData['language']);
        $id =  str_replace(".", "", uniqid("", true));
        $fp = fopen(storage_path("app/tempfiles/" . $id . ".vtt"), "wb");
        fwrite($fp, $completeVttString);
        fclose($fp);
        $jwplatform_api = new JwplatformAPI(env("JWPLAYER_API_KEY", ""), env("JWPLAYER_API_SECRET", ""));
        $params = [
            'video_key' => $validateData['video_key'],
            'kind' => $validateData['kind'],
            'label' => $validateData['label'],
            'language' => $validateData['language']
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
            'VttData' => 'required',
            'kind' => 'required',
            'label' => 'required',
            'language' => 'required',
        ]);
        $explodedLink = explode("/", $validateData['VttLink']);
        $trackKey = str_replace(".tmp", "", str_replace(".vtt", "", $explodedLink[count($explodedLink) - 1]));
        $completeVttString = vttConstructor::constructVtt($validateData['VttData'], $validateData['kind'], $validateData['language']);
        $id =  str_replace(".", "", uniqid("", true));
        $fp = fopen(storage_path("app/tempfiles/" . $id . ".vtt"), "wb");
        fwrite($fp, $completeVttString);
        fclose($fp);
        $jwplatform_api = new JwplatformAPI(env("JWPLAYER_API_KEY", ""), env("JWPLAYER_API_SECRET", ""));
        $params = [
            'track_key' => $trackKey,
            'label' => $validateData['label'],
            'update_file' => 'true',
        ];
        $url = "app/tempfiles/" . $id . ".vtt";
        $decoded = json_decode(trim(json_encode($jwplatform_api->call('/videos/tracks/update', $params))), TRUE);
        if (!array_key_exists('message', $decoded)) {
            $upload_link = $decoded['link'];
            $upload_response = $jwplatform_api->upload(storage_path($url), $upload_link);
            unlink(storage_path($url));
            return response(['message' => $upload_response["status"]], 200);
        }

        return response(['error' => $decoded["message"]], 500);
    }

    public function DeleteCaption(Request $request)
    {
        $validateData = $request->validate([
            'VttLink' => 'required',
        ]);
        $explodedLink = explode("/", $validateData['VttLink']);
        $trackKey = str_replace(".tmp", "", str_replace(".vtt", "", $explodedLink[count($explodedLink) - 1]));
        $jwplatform_api = new JwplatformAPI(env("JWPLAYER_API_KEY", ""), env("JWPLAYER_API_SECRET", ""));
        $params = [
            'track_key' => $trackKey,
        ];
        $decoded = json_decode(trim(json_encode($jwplatform_api->call('/videos/tracks/delete', $params))), TRUE);
        
        if($decoded['status'] == 'ok'){
            return response(['status' => $decoded['status']], 200);
        }
        else if(array_key_exists('message', $decoded)){
            return response(['message' => $decoded['message']], 500);
        }
        
    }
}
