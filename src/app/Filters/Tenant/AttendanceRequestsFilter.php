<?php


namespace App\Filters\Tenant;


use App\Filters\FilterBuilder;
use App\Helpers\Traits\MakeArrayFromString;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceRequestsFilter extends FilterBuilder
{
    use MakeArrayFromString, UserAccessQueryHelper;

    public function departments($departments = null): void
    {
        $departments = $this->makeArray($departments);

        $this->builder->when(count($departments), function (Builder $builder) use ($departments){
            $builder->whereHas('user', function (Builder $builder) use ($departments){
                $builder->whereHas('department', function (Builder $builder) use ($departments){
                    $builder->whereIn('id', $departments);
                });
            });
        });
    }

    public function search($search = null): void
    {
        $this->builder->whereHas('user', function (Builder $builder) use ($search){
           $builder->where('first_name', 'LIKE', "%$search%")
               ->orWhere('last_name', 'LIKE', "%{$search}%")
               ->orWhere('email', 'LIKE', "%$search%")
               ->orWhereRaw(DB::raw('CONCAT(`first_name`, " ", `last_name`) LIKE ?'), ["%$search%"]);
        });
    }

    public function workingShifts($working_shifts = null)
    {
        $working_shifts = $this->makeArray($working_shifts);

        $this->builder->when(
            count($working_shifts),
            fn (Builder $builder) => $builder->whereIn('working_shift_id', $working_shifts)
        );
    }

    public function date($date = null)
    {
        $this->builder->when(
            $date,
            fn(Builder $builder) => $builder->whereDate('in_date', Carbon::parse($date)),
            fn(Builder $builder) => $builder->whereDate('in_date', todayFromApp())
        );
    }

    public function type($type = null)
    {
        $this->builder->when(
            $type,
            fn(Builder $builder) => $builder->whereHas(
                'details',
                fn(Builder $b) => $b->when(
                    $type === 'auto',
                    fn(Builder $auto) => $auto->whereNull('review_by'),
                    fn(Builder $auto) => $auto->whereNotNull('review_by')
                )
            )
        );
    }

    public function behavior($behavior = null)
    {
        $this->builder->when(
            $behavior,
            fn(Builder $builder) => $builder->where('behavior', $behavior)
        );
    }

    public function requestType($requestType = null)
    {
        $statusPending = resolve(StatusRepository::class)->attendancePending();

        $this->builder->when(
            $requestType,
            fn(Builder $builder) => $builder->whereHas(
                'details',
                fn(Builder $b) => $b->when(
                    $requestType === 'new',
                    fn(Builder $auto) => $auto->whereNull('attendance_details_id')
                        ->where('status_id', $statusPending),
                    fn(Builder $auto) => $auto->whereNotNull('attendance_details_id')
                        ->where('status_id', $statusPending)
                )
            )
        );
    }

    public function employmentStatuses($statuses = null)
    {
        $employmentStatuses = $this->makeArray($statuses);

        $this->builder->when(
            $employmentStatuses,
            fn (Builder $builder) => $builder->whereHas(
                'user', fn(Builder $builder) => $builder->whereHas(
                    'employmentStatus',
                    fn(Builder $b) => $b->whereIn('id', $employmentStatuses)
                )
            )
        );
    }

    public function requestDate($date = null)
    {
        $date = json_decode(htmlspecialchars_decode($date), true);

        $this->builder->when($date, function (Builder $builder) use ($date) {
            $builder->whereBetween(\DB::raw('DATE(in_date)'), array_values($date));
        });

    }

    public function entryDate($date = null)
    {
        $date = json_decode(htmlspecialchars_decode($date), true);

        $this->builder->when(
            $date,
            fn(Builder $builder) => $builder->whereHas(
                'details',
                fn(Builder $b) => $b->whereBetween(\DB::raw('DATE(created_at)'), array_values($date))
            )
        );
    }

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'no', function (Builder $builder) {
            $builder->where('user_id', auth()->id());
        },function (Builder $builder) {
            $builder->when(request()->get('access_behavior') == 'own_departments',
                fn(Builder $b) => $this->userAccessQuery($b)
            );
        });
    }

    public function users($users = null): void
    {
        $users = $this->makeArray($users);

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereHas(
                'user', fn(Builder $builder) => $builder->whereIn('id', $users)
            );
        });
    }
    public function skills($skills = null): void
    {
        $skills = $this->makeArray($skills);

        $this->builder->when(count($skills), function (Builder $builder) use ($skills) {
            $builder->whereHas(
                'user.skills',
                fn(Builder $b) => $b->whereIn('id', $skills)
            );
        });
    }

    public function recurringAttendance($ra = null): void
    {
        $ra = $this->makeArray($ra);

        $this->builder->when(count($ra), function (Builder $builder) use ($ra) {
            $builder->whereHas(
                'user.recurringAttendance',
                fn(Builder $b) => $b->whereIn('id', $ra)
            );
        });
    }
    public function gatePasses($gate_passes = null): void
    {
        $gate_passes = $this->makeArray($gate_passes);

        $this->builder->when(count($gate_passes), function (Builder $builder) use ($gate_passes) {
            $builder->whereHas(
                'user.gate_pass',
                fn(Builder $b) => $b->whereIn('id', $gate_passes)
            );
        });
    }
}
