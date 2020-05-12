<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\ArrayItem;

class TranslateStrings extends Command
{
    protected $splitFile;
    protected $targetLanguage;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->splitFile = $data['splitFile'];
        $this->targetLanguage = $data['targetLanguage'];
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $splitVttResultTranslated = array();

        foreach ($this->splitFile['cues'] as $splitBlockResult) 
        {
            $apiKey = env("API_KEY", "");
            $text = $splitBlockResult['text'];
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target=' . $this->targetLanguage;
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);
            $responseDecoded = json_decode($response, true);
            curl_close($handle);
            $splitBlockResult["text"] = $responseDecoded["data"]["translations"][0]["translatedText"];
            array_push($splitVttResultTranslated, $splitBlockResult);
        }
        return $splitVttResultTranslated;
    }
}
