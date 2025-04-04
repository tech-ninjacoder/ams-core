<?php

namespace App\Console\Commands;

use App\Models\Tenant\Project\Project;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Request;


class hrms_projects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrms:projects';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws GuzzleException
     */
    public function handle()
    {
        $shortname = env('hrms_shortname');
        $password = env('hrms_pass');
        $username = env('hrms_user');

//        $projects = Project::all();
        $today = Carbon::today()->toDateString();

        $projects = Project::where('sync_date',null)
            ->get();
        Log::info('projects '.json_encode($projects));

//        $projects = Project::whereIn('id',$ids)->get();

        $projects_xml = [];

        foreach ($projects as $project) {
            $projects_xml[] = [
                'ProjectName' => htmlspecialchars($project->name),
                'ProjectCode' => htmlspecialchars($project->pme_id),
            ];
        }

        $xml = '<?xml version="1.0"?><Projects>';
        foreach ($projects_xml as $project) {
            $xml .= '<Project>';
            $xml .= '<ProjectName>' . $project['ProjectName'] . '</ProjectName>';
            $xml .= '<ProjectCode>' . $project['ProjectCode'] . '</ProjectCode>';
            $xml .= '</Project>';
        }

        $xml .= '</Projects>';

        Log::info($xml);

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/xml',
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password)
        ];
        $body = $xml;
        $request = new Request('POST', 'http://protectauh.fortiddns.com:8082/Api/Projects/UploadProjectDetails?shortName=PME', $headers, $body);
        $response = $client->send($request);
        if ($response->getStatusCode() == 200) {
            echo 'Project details uploaded successfully.';
            $projects->each(function ($project) {
                $project->sync_date = now();
                $project->save();
            });

        } else {
            echo 'An error occurred while uploading project details.';
        }
        Log::info('success '.json_encode($response->getBody()->getContents()));

        return 0;
    }
}
