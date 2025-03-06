<?php

namespace App\Console\Commands;

use App\Models\Tenant\Project\Project;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class update_geofence_title extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:geofence_title';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix Geofence title display pme id instead of project id';

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
        $response = Http::get(env('vts_api_path').'/get_geofences?lang=en&user_api_hash='.env('vts_api_token'));
//        Log::info($response);
        $response = $response->json();
//        Log::info($response);
        foreach ($response['items'] as $resp => $key) {
//            $this->info($resp['items']['items']->id);
            foreach ($key as $value => $e) {
                Log::info($e['id']);
                $geofence_id = $e['id'];
                $project = Project::where('geofence_id','=',$geofence_id )->first();
//                if ($project->count() > 0 ) {
//                    Log::info($project->pme_id);
//                }
                if (!is_null($project)) {
                    Log::info('not null');
                    Log::info($project);
                    Log::info($project->pme_id);

                    $params = [
                        "id" => Project::find($project->id)->geofence_id,
                        "group_id" => '0',
                        "active" => 1,
                        "name" => $project->pme_id,
                        "polygon_color" => "#d000df",
                        "type" => "polygon",
                        "polygon" => $project->geometry
                    ];
                    $data = json_encode($params);
                    Log::info('geofence update start on VTS ');
                    Log::info($data);


                    $client = new Client([
                        'headers' => ['Content-Type' => 'application/json']
                    ]);
                    $response = $client->post(env('vts_api_path').'/edit_geofence?lang=en&user_api_hash='.env('vts_api_token'),
                        ['body' => $data]
                    );
                    $response = json_decode($response->getBody(), true);
                    Log::info($response);
                    Log::info('geofence update END on VTS');



                }
            }
        }


//        //dump
//        $project = 0;
//        $project_name = Project::find($project->id)->id;
//        $project_geometry = $project->geometry;
//        Log::info('The project  ==>'.Project::find($project->id)->geofence_id);
//
//
//        $params = [
//            "id" => Project::find($project->id)->geofence_id,
//            "group_id" => '0',
//            "active" => 1,
//            "name" => $project_name,
//            "polygon_color" => "#d000df",
//            "type" => "polygon",
//            "polygon" => $project_geometry
//        ];
//        $data = json_encode($params);
//        Log::info('geofence update start on VTS');
//        Log::info($data);
//
//
//        $client = new Client([
//            'headers' => ['Content-Type' => 'application/json']
//        ]);
//        $response = $client->post(env('vts_api_path').'/edit_geofence?lang=en&user_api_hash='.env('vts_api_token'),
//            ['body' => $data]
//        );
//        $response = json_decode($response->getBody(), true);
//        Log::info($response);
//        Log::info('geofence update END on VTS');
//        Artisan::call('update:alert');
        return 0;
    }
}
