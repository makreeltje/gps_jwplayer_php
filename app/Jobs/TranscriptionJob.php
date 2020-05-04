<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

require_once __DIR__ . '/vendor/autoload.php';

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
    private $vttArray = ["cues"=>[]];
    private $audioFile = null;
    private $config = null;

    public function __construct($audioFilePath, $languageCode)
    {
        $this->audioFile = file_get_contents($audioFilePath);
        $this->config = (new RecognitionConfig())
            ->setEncoding(AudioEncoding::ENCODING_UNSPECIFIED)
            ->setSampleRateHertz(32000)
            ->setLanguageCode($languageCode)
            ->setEnableWordTimeOffsets(true)
            ->setEnableAutomaticPunctuation(true);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() 
    {
        $client = new SpeechClient();
        $audio = (new RecognitionAudio())
            ->setContent($this->audioFile);

        $operation = $client->longRunningRecognize($this->config, $audio);
        print("Transcription started");
        $operation->pollUntilComplete();
        $cueTemplate = ["voice"=>"", "start"=>0, "end"=>0, "text" =>"", "identifier"=>""];
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

                        $cue["start"] = $wordInfo->getStartTime()->getSeconds();
                    } else if (substr($previousWord->getWord(), -1) == ".") {
                        //close previous cue
                        $cue["end"] = $previousWord->getEndTime()->getSeconds();
                        array_push($this->vttArray["cues"], $cue);
                        //start new cue
                        $cue = $cueTemplate;
                        $cue["start"] = $wordInfo->getStartTime()->getSeconds();
                    }
                    $cue["text"] .= $wordInfo->getWord() . " ";
                    $previousWord = $wordInfo;
                }
                var_dump($this->vttArray);
                
            }
        } else {
            print_r($operation->getError());
        }
        $client->close();
        //todo add to database
    }
}
