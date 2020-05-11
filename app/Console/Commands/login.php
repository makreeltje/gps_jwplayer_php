<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class login extends Command
{
    protected $loginData;
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
    public function __construct(\Illuminate\Foundation\Application $loginData)
    {
        $this->loginData = $loginData;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!Auth::attempt(loginData)) {
            return Redirect::back()->withErrors(['errorMsg', 'Invalid Credentials']);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(), 'accessToken' => $accessToken]);
    }
}
