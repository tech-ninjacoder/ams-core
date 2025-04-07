<?php

namespace App\Console\Commands;

use App\Models\Tenant\Employee\Helmet;
use App\Notifications\Core\User\UserInvitationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\New_;

class importHelmets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:helmets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import newly added helmets on VTS';

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
        $helmets = Helmet::where('id' ,'>' ,0)->pluck('id')->toArray();
        $devices = $this->GetDevices();
        $devices_count = count($devices);
        $helmet_count = count($helmets);
        foreach ($devices as $id=>$imei) {
//            echo 'IMPORT:HELMETS: ID => '.$id.' With IMEI => '.$imei."\n";
            if (!in_array($id,$helmets)) {
                echo 'Device IMEI:'.$imei['imei'].' added to local db as HELMET with id <<'.$id.'>> successfully '."\n";
                $helmet = new Helmet;
                $helmet->id = $id;
                $helmet->pme_barcode = json_decode($imei['plate_number']);
                $helmet->department_id = 1;
                $helmet->description = $imei['name'];
                $helmet->tenant_id = 1;
                $helmet->is_default = 0;
                $helmet->imei = json_decode($imei['imei']);
                $helmet->save();
            }else {
//                echo 'Helmet IMEI '.$imei.' skipped, already exist'."\n";
            }
//            Log::info($helmets);

        }

        $this->info($devices_count.'  Devices found to Tracking Server');
//        notify()
//            ->on('user_joined')
//            ->with(1)
//            ->send(UserInvitationNotification::class);

        $this->info($helmet_count.' Helmet Found in Database');

    }
    public function GetDevices()
    {
        $imei = [];
        $count = 0;


        $response = Http::get(env('vts_api_path').'/get_devices?lang=en&user_api_hash='.env('vts_api_token'));
        $response = $response->json();

        //check response and return each device id with it's imei
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
                                                if ($params == 'device_data') {
                                                    if(is_array($param)) {
                                                        Log::info('IMEI => '.$param['imei']);
                                                        $imei[$param['id']] = array(
                                                            "imei" => $param['imei'],
                                                            "plate_number" => $param['plate_number'],
                                                            "name" => $param['name']
                                                    );


                                                        $count = $count+1;
                                                    }
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
//        Log::info("DEVICES ARRAY===> ".json_encode($imei)."END OF DEVICES ARRAY");
        return $imei;
    }

}
