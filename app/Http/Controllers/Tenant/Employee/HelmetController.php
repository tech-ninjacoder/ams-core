<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\HelmetFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\HelmetRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\EmployeeAlerts;
use App\Models\Tenant\Employee\Helmet;
use App\Notifications\Core\User\UserInvitationNotification;
use App\Notifications\Tenant\AlertNotification;
use App\Notifications\Tenant\HelmetNotification;
use App\Services\Tenant\Employee\HelmetService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HelmetController extends Controller
{
    public function __construct(HelmetService $service, HelmetFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->withCount(['users' => function($query){
                $query->where('end_date',null);
    } ])//get only number of active assigned user to the helmet
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );

    }

    public function store(HelmetRequest $request)
    {
        $this->service->save(
            $request->only('imei','pme_barcode', 'description', 'tenant_id')
        );

        return created_responses('helmet');
    }

    public function show(Helmet $helmet)
    {
        return $helmet;
    }

    public function update(Helmet $helmet, HelmetRequest $request)
    {
        $helmet->update(
            request()->only('imei','pme_barcode', 'description', 'tenant_id')
        );

        return updated_responses('helmet');
    }

    public function destroy(Helmet $helmet)
    {
        if ($helmet->is_default) {
            throw new GeneralException(__t('action_not_allowed'));
        } elseif ($helmet->users->count()) {
            throw new GeneralException(__t('cant_delete_helmet'));
        }

        $helmet->delete();

        return deleted_responses('helmet');
    }
    public function release(Helmet $request)
    {
        return "hi";
    }
    public function sync (){
        Artisan::call('import:helmets');
        return sync_responses('helmet');
    }

    public function vtsalert(Request $request){
//        Log::info("VTS alert");
//        Log::info($request->user_id);
        DB::transaction(function () use ($request) {

            $dev_id = $request->device_id;
        $employee = User::whereHas('helmet', function ($query) use ($dev_id) {
                $query->where('id','=',$dev_id);
            })
            ->with('profile')
            ->get();
        $emp = json_decode($employee,true);
        $user =  User::find($emp[0]['id']);

            $helmet = Helmet::find($dev_id);
            $alert = new EmployeeAlerts();
            $alert->type = $request->type;
            $alert->user_id = $user->id;
            $alert->date = $request->time;
            $alert->note = $request->message;
            $alert->save();
        notify()
            ->on('offline_duration')
            ->with($user)
            ->send(HelmetNotification::class);
//            notify()
//                ->on('vts_alert')
//                ->with($alert)
//                ->send(AlertNotification::class);
//        notify(new HelmetNotification($employee,'database',1));
        log_to_database('helmet alert', [
            'old' => [],
            'attributes' => $alert
        ],'default',$user,$alert);
//        Log::info($helmet);


            if($request->type=='offline_duration'){
                Log::info('offline_duration');
            }elseif ($request->type=='sos'){
                Log::info('sos');
            }elseif ($request->type == 'custom'){
                Log::info('custom');
            }
        });



        return response('VTS alert received');
    }



    }
