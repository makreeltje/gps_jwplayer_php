<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Classes\vttConstructor;
use Google\Cloud\Storage\StorageClient;
use Google\ApiCore\ApiException;

require_once __DIR__ . '../../../vendor/autoload.php';

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;


class TranscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $vttArray = [];
    private $audioFile = null;
    private $config = null;
    private $audioFilePath = "";
    private $languageCode = "";
    private $videoKey;
    private $kind;
    private $label;
    private $fileId;

    public function __construct($audioFilePath, $languageCode,$videoKey,$kind,$label)
    {
        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . base_path() . '/JWPlayer-565edd548e20.json');
        $this->audioFilePath = $audioFilePath;
        $this->languageCode = $languageCode;
        $this->videoKey = $videoKey;
        $this->kind = $kind;
        $this->label = $label;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $storage = new StorageClient();
        $storage->registerStreamWrapper();
        $localFile =$this->ConvertToMp3($this->audioFilePath);
        $bucket = $storage->bucket("auto_caption");

        $bucket->upload(
            fopen($localFile,'r')
        );
        
        //$this->audioFile = file_get_contents($localFile);
        $this->config = (new RecognitionConfig())
            ->setEncoding(AudioEncoding::ENCODING_UNSPECIFIED)
            ->setLanguageCode($this->languageCode)
            ->setSampleRateHertz(44100)
            ->setEnableWordTimeOffsets(true)
            ->setEnableAutomaticPunctuation(true);
            
        $client = new SpeechClient();
        $url = 'gs://auto_caption/'. $this->fileId;
        $audio = (new RecognitionAudio())
            ->setUri($url);
        $operation = $client->longRunningRecognize($this->config, $audio);
        $operation->pollUntilComplete();
        
        
        $cueTemplate = ["voice" => "", "start" => 0, "end" => 0, "text" => "", "identifier" => ""];
        if ($operation->operationSucceeded()) {
            $response = $operation->getResult();

            // each result is for a consecutive portion of the audio. iterate
            // through them to get the transcripts for the entire audio file.
            foreach ($response->getResults() as $result) {

                $alternatives = $result->getAlternatives();
                $mostLikely = $alternatives[0];
                $transcript = $mostLikely->getTranscript();
                $confidence = $mostLikely->getConfidence();
                $previousWord = null;
                $cue = $cueTemplate;
                foreach ($mostLikely->getWords() as $wordInfo) {


                    if ($previousWord == null) {
                        //$cue
                        $cue["start"] = $this->ParseTime($wordInfo);
                    } else if (substr($previousWord->getWord(), -1) == ".") {
                        //close previous cue
                        $cue["end"] = $this->ParseTime($wordInfo);
                        array_push($this->vttArray, $cue);

                        //start new cue
                        $cue = $cueTemplate;
                        $cue["start"] = $this->ParseTime($wordInfo);
                    }
                    $cue["text"] .= $wordInfo->getWord() . " ";
                    $previousWord = $wordInfo;
                }
            }
            #var_dump($this->vttArray);
            $vttString = vttConstructor::constructVtt($this->vttArray,$this->kind,$this->languageCode);
            
            vttConstructor::uploadVtt($vttString,$this->videoKey,$this->kind,$this->label);
            unlink($localFile);
            #var_dump($vttString);
        } else {
            print_r($operation->getError());
        }
        $client->close();
        //todo add to database
       
    }
    private function ConvertToMp3($sourceFilePath){
        $id =  str_replace(".", "", uniqid( "", true));
        $path = storage_path( "app/tempfiles/");
        if(copy($sourceFilePath,$path . $id . ".m4a")){
            $command ="ffmpeg -i \"" . $path . $id. ".m4a\" -acodec libmp3lame -q:a 4 \"".$path . $id. ".mp3\"";
            exec($command);
        }
        unlink($path.$id.".m4a");
        $this->fileId = $id . ".mp3";
        return $path . $id . ".mp3";
    }
    private function ParseTime($wordInfo){
        $jsonTime = $wordInfo->getStartTime()->serializeToJsonString();
        $jsonTime =str_replace("\"","",$jsonTime);
        $time = floatval($jsonTime);
        return $time;
    }
}
