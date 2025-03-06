<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;


class fix_attendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix attendance for a certain day by fetching log (testing only)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $device_id = $this->ask('Device ID?');
        $date_from = $this->ask('Date From (yyyy-mm-dd): ');
        $date_to = $this->ask('Date To (yyyy-mm-dd): ');
//        $composerHome = substr(shell_exec('composer config home -g'), 0, -1).'/vendor/autoload.php';
        $client = new Client();
        $headers = [
            'Cookie' => 'laravel_session=eyJpdiI6IjVyZWVJZHlBOVQrbFY1K0hnRDRZZEE9PSIsInZhbHVlIjoiaHhEcVVENUR1Nm1kNEF5Qkl3OVcrSHNEampFdzFxbk9hMGVGU1BXTktXd3gzOWI1Z2w1dFR6cWEweUZkVnBtd2xIK0RYTEtEcGZBbWVzS0ZWZmVYSnJ3dVpHYll2K2Ridk14c2JsMWJOMXNaVFJZam5jU0xlRjFhXC9WeTVqRHR0IiwibWFjIjoiYWNhMzUwNTkwZmJjZjNhNWY2OGEwMjU3YjM2NTc1MmFlNjY1OTlkNGRmMmYyZWY3NWUxZmM3ODFlOWU2ZDljMCJ9'
        ];
        $request = new Request('GET', 'https://track.vt-lb.com//api/get_events?device_id='.$device_id.'&type=&date_from='.$date_from.'&date_to='.$date_to.'&search=&lang=en&user_api_hash=$2y$10$1iq/EfVn6jOyWPBhj2nuwOxgrEL5.SR8dS1HezInHJjRtFGNGa0M.', $headers);
        $res = $client->sendAsync($request)->wait();
        $this->line('result:==>  '.$res->getStatusCode());
        $content = $res->getBody();
        Log::info($content);



        //echo $res->getBody();


        return 0;
    }
}
