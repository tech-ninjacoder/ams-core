<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\ProvidersFilter;
use App\Filters\Tenant\RecurringAttendanceFilter;
use App\Filters\Tenant\SkillsFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Attendance\RecurringAttendanceRequest;
use App\Http\Requests\Tenant\Employee\ProviderRequest;
use App\Http\Requests\Tenant\Employee\SkillRequest;
use App\Models\Tenant\Attendance\RecurringAttendance;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\Skill;
use App\Services\Tenant\Attendance\RecurringAttendanceService;
use App\Services\Tenant\Employee\ProviderService;
use App\Services\Tenant\Employee\SkillService;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Support\Facades\Log;

class RecurringAttendanceController extends Controller
{
    public function __construct(RecurringAttendanceService $service, RecurringAttendanceFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->withCount(['users' => function($query){
                $query
                    ->where('end_date',null)
                    ->where('is_in_employee',1);//get only number of active related user to the provider and is an employee
            } ])
            ->withAggregate('project','name')
            ->withAggregate('project','pme_id')
            ->withAggregate('workingshift','name')
            ->with('status')

            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );
    }

    public function store(RecurringAttendanceRequest $request)
    {
        $in_time = Carbon::createFromFormat('h:i A', $request->in_time)->format('H:i:s');
        $out_time = Carbon::createFromFormat('h:i A', $request->out_time)->format('H:i:s');
        $request->merge([
            'in_time' => $in_time,
            'out_time' => $out_time
        ]);


        $this->service->save(
            $request->only( 'status_id','working_shift_id','in_time','out_time','added_by','project_id')
        );

        return created_responses('recurring_attendance');
    }

    public function show(RecurringAttendance $ra)
    {
        $in_time = RecurringAttendance::getYourTimeColumnAttribute($ra->in_time);
        $out_time = RecurringAttendance::getYourTimeColumnAttribute($ra->out_time);
        $raArray = $ra->toArray();
        $raArray['in_time'] = $in_time;
        $raArray['out_time'] = $out_time;

        return response()->json($raArray);
    }

    public function update(RecurringAttendance $ra, RecurringAttendanceRequest $request)
    {
        $in_time = Carbon::createFromFormat('h:i A', $request->in_time)->format('H:i:s');
        $out_time = Carbon::createFromFormat('h:i A', $request->out_time)->format('H:i:s');
        $request->merge([
            'in_time' => $in_time,
            'out_time' => $out_time
        ]);
        $ra_record = RecurringAttendance::find($request->id);
        $ra_record->status_id = $request->status_id;
        $ra_record->working_shift_id = $request->working_shift_id;
        $ra_record->in_time = $request->in_time;
        $ra_record->out_time = $request->out_time;
        $ra_record->project_id = $request->project_id;
        $ra_record->added_by = $request->added_by;

        $ra_record->save();
//        $ra->update(
//            $request->only( 'status_id','working_shift_id','in_time','out_time','added_by','project_id')
//        );

        return updated_responses('recurring_attendance');
    }

    public function destroy(RecurringAttendance $ra, $e)
    {
        $ra = RecurringAttendance::find($e);
        Log::info('$ra delete '.$ra);
        Log::info('$e $e '.$e);

        if ($ra->users->count()) {
            throw new GeneralException(__t('cant_delete_recurring_attendance'));
        }

        $ra->delete();

        return deleted_responses('recurring_attendance');
    }


}
