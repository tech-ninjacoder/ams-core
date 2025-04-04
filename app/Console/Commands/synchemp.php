<?php

namespace App\Console\Commands;

//use AWS\CRT\Log;
use App\Models\Core\Auth\Profile;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\DepartmentUser;
use App\Models\Tenant\Employee\UserEmploymentStatus;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;

class synchemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:emp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Employees for Olive HRMS';

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
        $localemps = Profile::all();
        $count = 0;
        $arrcount = 0;
        $emps = $this->GetEmployees();
        $emps = json_decode($emps,true);
//        $this->info('The command was successful!');
//        {category_id}
        $deps = Department::select('id','name')->get();

        $this->info('Available Departments');
        foreach ($deps as $dep) {
            $this->info($dep->id.' - '.$dep->name);
        }

        $dep_id = $this->ask('Choose department:');


//        $this->info('The command was successful!');

        if ($this->confirm('This will add new employees into the database into department id->'.$dep_id.', continue?')) {


            foreach ($emps as $key => $value) {
//            Log::info($value['ID']);
                $checkemp = Profile::where('employee_id', $value['ID'])->get();
                Log::info($checkemp);
                if ($checkemp->isEmpty()) {
                    $newuser = new User();
                    $newuser->first_name = $value['EMPNAME'];
                    $newuser->email = $value['ID'] . '@pmeams.com';
                    $newuser->status_id = 1;
                    $newuser->is_in_employee = 1;
                    $newuser->save();
                    $newuser->assignRole(4);

                    $newprofile = new Profile();
                    $newprofile->user_id = $newuser->id;
                    $newprofile->employee_id = $value['ID'];
                    $newprofile->save();

                    $user_dep = new DepartmentUser();
                    $user_dep->department_id = $dep_id;
                    $user_dep->user_id = $newuser->id;
                    $user_dep->start_date = Carbon::now();
                    $user_dep->save();

                    $new_employement_statu = new UserEmploymentStatus();
                    $new_employement_statu->user_id = $newuser->id;
                    $new_employement_statu->employment_status_id = 2;
                    $new_employement_statu->start_date = Carbon::createFromFormat('d/m/Y', $value['DOJ']);
                    $new_employement_statu->save();

                    Log::info('user create with id: ' . $newuser->id);
                } else {
                    Log::info('fount match');
                    $count++;

                }

            }

            Log::info('array count: ' . $arrcount);

            Log::info('matching employess: ' . $count);
            return 0;
        }

    }
    public function GetEmployees() {
        $url = 'protectauh.fortiddns.com:8082/Api/Employee/GetActiveEmployees?shortName=PME';
        $client = new Client();

        try {
            $response = $client->request(
                'GET', /*instead of POST, you can use GET, PUT, DELETE, etc*/
                $url,
                [
                    'auth' => ['olive', 'PmE@7oLv!'] /*if you don't need to use a password, just leave it null*/
                ]
            );
        } catch (GuzzleException $e) {
        }


        Log::info($response->getBody());
        return $response->getBody();

    }
}
