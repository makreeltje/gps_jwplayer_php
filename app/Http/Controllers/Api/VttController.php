<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Http;
use Jwplayer\JwplatformAPI;
use Illuminate\Support\Facades\File;


class VttController extends Controller
{
    public function UploadCaption(Request $request)
    {
        $validateData = $request->validate([
            'VttData' => 'required',
        ]);
        $SplitVtt = $validateData['VttData'];
        $completeVttString = app('App\Http\Controllers\vttConstructor')->constructVtt($SplitVtt);
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . "testieeee" . ".vtt", "wb");
        fwrite($fp, $completeVttString);
        fclose($fp);
        $jwplatform_api = new JwplatformAPI('lGTgPtcH', '0qZ5R1VmpjllnpPEmWFfYgg3');
        $params = array();
        $params['video_key'] = 'ZhYGeM5e';
        $params['kind'] = 'captions';
        $params['label'] = 'Testtttt';
        $response = json_encode($jwplatform_api->call('/videos/tracks/create', $params));
        $url = "/app/files/test.vtt";
        $content = File::get(storage_path($url));

        $decoded = json_decode(trim($response), TRUE);
        $upload_link = $decoded['link'];
        $upload_response = $jwplatform_api->upload(storage_path($url), $upload_link);
        return $upload_response;
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
