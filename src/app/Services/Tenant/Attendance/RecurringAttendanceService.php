<?php

namespace App\Services\Tenant\Attendance;

use App\Models\Tenant\Attendance\RecurringAttendance;
use App\Models\Tenant\Attendance\RecurringAttendanceUser;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Project\ProjectService;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class RecurringAttendanceService extends TenantService
{
    protected $raId;
    protected bool $isUpdating = false;


    public function __construct(RecurringAttendance $ra )
    {
        $this->model = $ra;
    }

    public function assignToUsers($users)
    {
        Log::info('users=> '.json_encode($users));
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousRaOfUsers($users);
        Log::info('===> '.json_encode(RecurringAttendanceUser::getNoneExistedUsers($this->getRaId(), $users)));

        RecurringAttendanceUser::insert(
            array_map(
                fn($user) => [
                    'user_id' => $user,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'recurring_attendance_id' => $this->getRaId()
                ],
                RecurringAttendanceUser::getNoneExistedUsers($this->getRaId(), $users)
            )
        );
    }
    public function mergeNonAccessibleUsers($ra, $users = []): array
    {
        $DeptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id());
        $recurringAttendanceUsers = $this->model->users->pluck('id')->toArray();

        $recurringAttendanceUsers = array_merge(UpcomingUserWorkingShift::query()
            ->where('recurring_attendance_id', $ra)
            ->pluck('user_id')
            ->toArray(), $recurringAttendanceUsers);

        $cantUpdateUsers = array_diff($recurringAttendanceUsers, $DeptUsers);

        $users = array_merge($cantUpdateUsers, $users);

        return $users;
    }
    public function moveEmployee()
    {
        $this->endPreviousRaOfUsers()
            ->moveToRa();

        return $this;
    }

//    public function endPreviousRaOfUsers($users = [])
//    {
//        $users = is_array($users) ? $users : func_get_args();
//
//        $a = RecurringAttendanceUser::whereIn('user_id', $users)
//            ->whereNull('end_date')
//            ->where('recurring_attendance_id', '!=', $this->getRaId())
//        ->get();
//        Log::info('$users '.json_encode($users));
//        Log::info('$a '.$a);
//
//        RecurringAttendanceUser::whereIn('user_id', $users)
//            ->whereNull('end_date')
//            ->where('recurring_attendance_id', '!=', $this->getRaId())
//            ->update([
//                'end_date' => todayFromApp()->format('Y-m-d')
//            ]);
//
//        return $this;
//    }
    public function endPreviousRaOfUsers($users = []): RecurringAttendanceService
    {
        $removeUser = array_diff($this->model->users->pluck('id')->toArray(), $users);
        Log::info('$removeUser '.json_encode($removeUser));
        if(count($removeUser)){
            RecurringAttendanceUser::whereNull('end_date')
                ->where('recurring_attendance_id', $this->getRaId())
                ->whereIn('user_id', $removeUser)
                ->update([
                    'end_date' => nowFromApp()->format('Y-m-d')
                ]);
            Log::info('yesss');

        }
        RecurringAttendanceUser::whereNull('end_date')
            ->when(
                !count($users),
                fn (Builder $b) => $b->where('recurring_attendance_id', $this->getRaId()),
                fn (Builder $b) => $b->where('recurring_attendance_id', '!=', $this->getRaId())->whereIn('user_id', $users)
            )->update([
                'end_date' => nowFromApp()->format('Y-m-d')
            ]);
        Log::info('$removeUser employee ended on a recurring attendance');

        return $this;
    }

    public function setRaId($raId): RecurringAttendanceService
    {
        $this->raId = $raId;
        return $this;
    }


    public function getRaId()
    {
        return $this->raId ?: $this->model->id;
    }
    public function validateUsers()
    {
        validator($this->getAttributes(), [
            'users' => 'required|array'
        ]);

        return $this;
    }

    public function moveToRa()
    {
        $ra_users = collect(RecurringAttendanceUser::getNoneExistedUsers(
            $this->getRaId(),
            $this->getAttribute('users')
        ))->map(fn ($user) => [
            'recurring_attendance_id' => $this->getRaId(),
            'user_id' => $user,
            'start_date' => nowFromApp()->format('Y-m-d')
        ])->toArray();

        RecurringAttendanceUser::insert($ra_users);

//        $departmentWorkingShiftId = DepartmentWorkingShift::getDepartmentWorkingShiftId($this->getDepartmentId()) ?:
//            WorkingShift::getDefault()->id;
//
//        $this->assignUserToDepartmentWorkingShift($departmentWorkingShiftId, $this->getAttribute('users'));

        return $this;
    }

}
