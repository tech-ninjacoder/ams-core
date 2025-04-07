<?php

namespace App\Console\Commands;

use App\Models\Tenant\Employee\Helmet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class fixhelemetbarcode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixhelmet:barcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this is a temp command that will add the helmet bar code correctly';

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
        $all_helmets = Helmet::all()->count();
        $helmets = Helmet::where('pme_barcode', null)->pluck('id')->toArray();
        $devices = $this->GetDevices();
        $devices_count = count($devices);
        $helmet_count = count($helmets);
        $process_count = 0;
        foreach ($devices as $id => $imei) {
//            echo 'IMPORT:HELMETS: ID => '.$id.' With IMEI => '.$imei."\n";
//            echo 'device id '.$id.' has this barcode: '.$imei['plate_number']."\n";
            try {
                $helmet = Helmet::find($id);
                if($helmet) {
                    if (!empty($imei['plate_number'])) {
                        if (empty($helmet->pme_barcode)) {
                            $helmet->pme_barcode = $imei['plate_number'];
                            $helmet->imei = $imei['imei'];

                            $helmet->save();
//                    echo $id.' found'."\n";
                            $this->info('Device id: <<' . $id . '>> found without barcode, The Newly added barcode is : << ' . json_decode($imei['plate_number']) . ' >>');
                            $process_count++;

                        } else {
                            $helmet->description = $imei['name'];
                            $helmet->pme_barcode = $imei['plate_number'];
                            $helmet->imei = $imei['imei'];

                            $helmet->save();
                            $this->info('Device id: <<' . $id . '>> name updated : << ' . $imei['name'] . ' >>');


                        }
                    }
                }

            } catch (\Exception $e) {
                Log::info($e);

            }

        }

        $this->info($devices_count . '  Devices found to Tracking Server');
        $this->info($all_helmets . ' Helmet Found in Database');
        $this->info($helmet_count . ' Helmet Without Barcode Found in Database');
        $this->info( 'Finished ==>> Barcodes added to Helmets ('.$process_count.')');



        return 0;
    }

    public function GetDevices()
    {
        $imei = [];
        $count = 0;


//        $response = Http::get(env('vts_api_path') . '/get_devices?lang=en&user_api_hash=' . env('vts_api_token'));
        $response = Http::withoutVerifying()->get(env('vts_api_path') . '/get_devices', [
            'lang' => 'en',
            'user_api_hash' => env('vts_api_token')
        ]);
        $response = $response->json();

        //check response and return each device id with it's imei
        if (!empty($response)) {
            foreach ($response as $key => $value) {
//                Log::info($key);
                if ($key == 'items') {
                    if (is_array($value)) {
                        foreach ($value as $items => $item) {
                            if (is_array($item)) {
                                foreach ($item as $devices => $device) {
                                    if (is_array($device)) {
                                        foreach ($device as $params => $param) {
                                            if ($params == 'device_data') {
                                                if (is_array($param)) {
//                                                    Log::info('IMEI => '.$param['imei']);
                                                    $imei[$param['id']] = array(
                                                        "imei" => $param['imei'],
                                                        "plate_number" => $param['plate_number'],
                                                        "name" => $param['name']
                                                    );


                                                    $count = $count + 1;
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

        } else return null;
        Log::info("DEVICES ARRAY===> ".json_encode($imei)."END OF DEVICES ARRAY");
        return $imei;
    }
}
