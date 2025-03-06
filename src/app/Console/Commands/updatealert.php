<?php

namespace App\Console\Commands;

use App\Models\Tenant\Employee\Helmet;
use App\Models\Tenant\Project\Project;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class updatealert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update alerts with geofences on vts';

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
        Log::info('Project Alert Update on VTS Started');
        $geofences_id = $this->GetGeofences();
        Log::info($geofences_id);
        $this->info(count($geofences_id).' Geofence Found in VTS');
        $devices_array = $this->GetDevices();
        Log::info($devices_array);
        $this->info(count($devices_array).' Devices Found in VTS');
        $alert_data = $this->GetAlertData();
        $this->info(' Alert ID => '.$alert_data[0]);
        $this->info(' Alert Name => '.$alert_data[1]);
        $this->info(' Alert Type => '.$alert_data[2]);
        $response = $this->UpdateAlert($geofences_id, $devices_array, $alert_data);
        Log::info($response);
        $this->info(' Response => '.json_encode($response));



        return $geofences_id;
    }
    public function GetGeofences()
    {
        $geofences = [];
        $count = 0;
//        $this->info($helmet_count.' Helmet Found in Database');


//        $response = Http::get(env('vts_api_path').'/get_geofences?lang=en&user_api_hash='.env('vts_api_token'));
        $response = Http::withOptions([
            'verify' => env('HTTP_VERIFY_SSL', true), // Controlled via .env
        ])->get(env('vts_api_path') . '/get_geofences', [
            'lang' => 'en',
            'user_api_hash' => env('vts_api_token'),
        ]);

        $response = $response->json();
//        Log::info($response);

        //check response and return each geofence id
        if (!empty($response)) {
            foreach ($response as $key => $value) {
//                Log::info($key);
                if ($key == 'items') {
                    if (is_array($value)){
                        foreach ($value as $items => $item) {
                            if (is_array($item)) {
                                foreach ($item as $devices => $device) {
                                    if (is_array($device)) {
                                        foreach ($device as $params => $param) {
                                            if ($params == 'id') {
                                                array_push($geofences,$param);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }

        }else return null;

        Log::info('VT geofences '.json_encode($geofences));
        $ams_geofences = Project::whereDoesntHave('parent')->pluck('geofence_id');
        Log::info('AMS geofences '.json_encode($ams_geofences));

        // Convert the arrays into Laravel collections
        $amsCollection = collect($ams_geofences);
        $vtCollection = collect($geofences);

        // Use intersect to find the common elements
        $commonGeofences = $amsCollection->intersect($vtCollection)->toArray();
        Log::info('Only the below common geofences ids will be active on VTS alert ');
        Log::info('common=> '.json_encode(array_values($commonGeofences)));

        // $commonGeofences now contains the geofence IDs that exist in both arrays
        return array_values($commonGeofences);
    }

    public function GetDevices()
    {
        $devices_array = [];
        $count = 0;
//        $this->info($helmet_count.' Helmet Found in Database');


//        $response = Http::get(env('vts_api_path').'/get_devices?lang=en&user_api_hash='.env('vts_api_token'));

        $response = Http::withOptions([
            'verify' => env('HTTP_VERIFY_SSL', true), // Controlled via .env
        ])->get(env('vts_api_path') . '/get_devices', [
            'lang' => 'en',
            'user_api_hash' => env('vts_api_token'),
        ]);

        $response = $response->json();
//        Log::info($response);

        //check response and return each device id
        if (!empty($response)) {
            foreach ($response as $key => $value) {
//                Log::info($key);
                if ($key == 'items') {
                    if (is_array($value)){
                        foreach ($value as $items => $item) {
                            if (is_array($item)) {
                                foreach ($item as $devices => $device) {
                                    if (is_array($device)) {
                                        foreach ($device as $params => $param) {
                                            if ($params == 'id') {
                                                array_push($devices_array,$param);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }

        }else return null;
        return $devices_array;
    }

    public function GetAlertData()
    {
        $alert_data = [];

//        $response = Http::get(env('vts_api_path').'/edit_alert_data?lang=en&user_api_hash='.env('vts_api_token').'&alert_id='.env('vts_alert_id'));

        $response = Http::withOptions([
            'verify' => env('HTTP_VERIFY_SSL', true), // Controlled via .env
        ])->get(env('vts_api_path') . '/edit_alert_data', [
            'lang' => 'en',
            'user_api_hash' => env('vts_api_token'),
            'alert_id' => env('vts_alert_id'),
        ]);

        $response = $response->json();

        //check response and return alert data
        if (!empty($response)) {
            foreach ($response as $key => $value) {
                if ($key == 'item') {
//                    Log::info('this items array');
                    if (is_array($value)){
                        foreach ($value as $items => $item) {
                            if ($items == 'id') {
                                $alert_data[0] = $item;
                            }
                            if ($items == 'name') {
                                $alert_data[1] = $item;
                            }
                            if ($items == 'type') {
                                $alert_data[2] = $item;
                            }

                        }
                    }
                }

            }

        }else return null;
        Log::info('Alert Data '.json_encode($alert_data));

        return $alert_data;
    }

    public function UpdateAlert ($geofences_id, $devices_array, $alert_data) {
        $params = [
            "id" => $alert_data[0],
            "name" => $alert_data[1],
            "type" => $alert_data[2],
            "devices" => $devices_array,
            "geofences" => $geofences_id
        ];
        $data = json_encode($params);

        $client = new Client([
            'headers' => ['Content-Type' => 'application/json'],
            'verify' => env('HTTP_VERIFY_SSL', true), // Controlled via .env

        ]);
        $response = $client->post(env('vts_api_path').'/edit_alert?lang=en&user_api_hash='.env('vts_api_token'),
            ['body' => $data]
        );
        Log::info('Project Alert Update on VTS Ended');

        return $response = json_decode($response->getBody(), true);

    }
}
