<?php

namespace App\Console\Commands;

use App\CommandVariables;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Types\Mixed_;
use Podlove\Webvtt\Parser;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Http\Request;

class SplitFile extends Command
{
    protected $filePath;
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
    public function __construct(Request $request) 
    {
        $this->filePath = $request['filePath'];
        parent::__construct();
     }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $parser = new Parser();
        $content = File::get(storage_path($this->filePath));
        $result = $parser->parse($content);
        return $result;
    }
}
