<?php

namespace App\Models\Core\Auth\Traits\Relationship;

use App\Models\Core\Auth\PasswordHistory;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\SocialAccount;
use App\Models\Core\Builder\Form\CustomFieldValue;
use App\Models\Core\File\File;
use App\Models\Core\Setting\Setting;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Alert\Alerts;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\RecurringAttendance;
use App\Models\Tenant\Attendance\RecurringAttendanceUser;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\DepartmentUser;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\DesignationUser;
use App\Models\Tenant\Employee\EmployeeAlerts;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Employee\GatePassUser;
use App\Models\Tenant\Employee\Helmet;
use App\Models\Tenant\Employee\HelmetUser;
use App\Models\Tenant\Employee\Profile;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\ProviderUser;
use App\Models\Tenant\Employee\Skill;
use App\Models\Tenant\Employee\UserContact;
use App\Models\Tenant\Employee\WorkersProviders;
use App\Models\Tenant\Employee\WorkersProvidersUser;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Leave\UserLeave;
use App\Models\Tenant\Payroll\BeneficiaryValue;
use App\Models\Tenant\Payroll\PayrunSetting;
use App\Models\Tenant\Payroll\Payslip;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectCoordinator;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\Project\ProjectWorkingShift;
use App\Models\Tenant\Salary\Salary;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    use CreatedByRelationship, StatusRelationship;

    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * @return mixed
     */
    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'skill_user', 'user_id', 'skill_id');
    }
    public function gatePasses()
    {
        return $this->belongsToMany(GatePass::class, 'gate_pass_user', 'user_id', 'gate_pass_id');
    }


    public function settings()
    {
        return $this->morphMany(
            Setting::class,
            'settingable'
        );
    }

    public function customFields()
    {
        return $this->morphMany(
            CustomFieldValue::class,
            'contextable'
        );
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function profiles()
    {
        return $this->hasOne(Profile::class, 'id');
    }


    public function profilePicture()
    {
        return $this->morphOne(File::class, 'fileable')
            ->whereType('profile_picture');
    }

    public function department()
    {
        return $this->departments()
            ->toOne()
            ->wherePivotNull('end_date')
            ->withPivot('start_date', 'end_date');
    }

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'department_user',
            'user_id',
            'department_id'
        )->withPivot('start_date', 'end_date')
            ->using(DepartmentUser::class);
    }

    public function hasDepartments(): HasMany
    {
        return $this->hasMany(
            Department::class,'manager_id', 'id'
        );
    }

    public function designation()
    {
        return $this->designations()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function provider()
    {
        return $this->wproviders()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function helmet()
    {
        return $this->helmets()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function recurringAttendance()
    {
        return $this->recurringAttendances()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function recurringAttendances()
    {
        return $this->belongsToMany(
            RecurringAttendance::class,
            'recur_at_user',
            'user_id',
            'recurring_attendance_id'
        )->withPivot('start_date', 'end_date')
            ->using(RecurringAttendanceUser::class);
    }

    public function gate_pass()
    {

        return $this->gate_passes()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function project()
    {
        return $this->projects()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function project_coordinator()
    {
        return $this->projects_coordinator()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }


    public function assi_project()
    {
        return $this->assi_projects()
            ->toOne()
            ->withPivot('start_date', 'end_date');
//            ->wherePivotNull('end_date');
    }
    public function past_project()
    {
        return $this->past_projects()
            ->toOne()
            ->withPivot('start_date', 'end_date');
    }

    public function visit()
    {
        return $this->visits()
            ->toOne()
            ->withPivot('start_date', 'end_date');
    }
    public function workerproviders()
    {
        return $this->workerprovider()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function designations()
    {
        return $this->belongsToMany(
            Designation::class,
            'designation_user',
            'user_id',
            'designation_id'
        )->withPivot('start_date', 'end_date')
            ->using(DesignationUser::class);
    }
    public function wproviders()
    {
        return $this->belongsToMany(
            Provider::class,
            'provider_user',
            'user_id',
            'provider_id'
        )->withPivot('start_date', 'end_date')
            ->using(ProviderUser::class);
    }
//    public function provider()
//    {
//        return $this->belongsTo(Provider::class);
//    }

    public function helmets()
    {
        return $this->belongsToMany(
            Helmet::class,
            'helmet_user',
            'user_id',
            'helmet_id'
        )->withPivot('start_date', 'end_date')
            ->using(HelmetUser::class);
    }
    public function projects()
    {

        return $this->belongsToMany(
            Project::class,
            'project_user',
            'user_id',
            'project_id'
        )->withPivot('start_date', 'end_date')
            ->where('end_date',null)
            ->using(ProjectUser::class);
    }

    public function projects_coordinator()
    {
        return $this->belongsToMany(
            Project::class,
            'project_coordinator',
            'user_id',
            'project_id'
        )->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date')
            ->using(ProjectCoordinator::class);
    }

    public function assi_projects()
    {
        Log::info('assi');

        return $this->belongsToMany(
            Project::class,
            'project_user',
            'user_id',
            'project_id'
        )->withPivot('start_date', 'end_date')
//            ->where('end_date',null)
            ->using(ProjectUser::class);
    }
    public function past_projects()
    {
        return $this->belongsToMany(
            Project::class,
            'project_user',
            'user_id',
            'project_id'
        )->withPivot('start_date', 'end_date')
            ->using(ProjectUser::class);
    }
    public function projectsCount()
    {
        return $this->belongsToMany(
            Project::class,
            'project_user',
            'user_id',
            'project_id'
        )->groupby('pme_id')->withPivot('start_date', 'end_date')
            ->where('end_date',null)
            ->using(ProjectUser::class);
    }
    public function visits()
    {
        return $this->belongsToMany(
            Project::class,
            'project_user',
            'user_id',
            'project_id'
        )->withPivot('start_date', 'end_date')
            ->using(ProjectUser::class);
    }

    public function gate_passes()
    {
        return $this->belongsToMany(
            GatePass::class,
            'gate_pass_user',
            'user_id',
            'gate_pass_id'
        )->withPivot('start_date', 'end_date')
            ->using(GatePassUser::class);

    }
    public function workerprovider()
    {
        return $this->belongsToMany(
            WorkersProviders::class,
            'work_provider_user',
            'user_id',
            'work_provider_id'
        )->withPivot('start_date', 'end_date')
            ->using(WorkersProvidersUser::class);
    }

    public function workingShift()
    {
        return $this->workingShifts()
            ->toOne()
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date')
            ->using(WorkingShiftUser::class);
    }

    public function workingShifts()
    {
        return $this->belongsToMany(
            WorkingShift::class,
            'working_shift_user',
            'user_id',
            'working_shift_id'
        )->withPivot('start_date', 'end_date')
            ->using(WorkingShiftUser::class);
    }
    public function employmentStatus()
    {
        return $this->employmentStatuses()
            ->toOne()
            ->withPivot('start_date', 'end_date', 'description')
            ->wherePivotNull('end_date');
    }

    public function employmentStatuses()
    {
        return $this->belongsToMany(EmploymentStatus::class, 'user_employment_status')
            ->withPivot('start_date', 'end_date', 'description');
    }

    public function alerts(){
        return $this->hasMany(EmployeeAlerts::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserContact::class)->whereIn('key', ['present_address', 'permanent_address']);
    }

    public function contacts()
    {
        return $this->hasMany(UserContact::class)->where('key', 'emergency_contacts');
    }

    public function socialLinks()
    {
        return $this->hasMany(UserContact::class)
            ->whereIn('key', config('settings.supported_social_links'));
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function yesterdaysAttendance()
    {
//        Log::info(Carbon::yesterday()->toDateString());

        return $this->hasMany(Attendance::class)->whereDate('in_date', Carbon::yesterday()->toDateString());
    }


    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function assignedLeaves(): HasMany
    {
        return $this->hasMany(UserLeave::class);
    }

    public function assignedPaidLeaves(): HasMany
    {
        return $this->assignedLeaves()
            ->whereHas('leaveType', fn($builder) => $builder->where('type', 'paid'));
    }

    public function assignedUnpaidLeaves(): HasMany
    {
        return $this->assignedLeaves()
            ->whereHas('leaveType', fn($builder) => $builder->where('type', 'unpaid'));
    }

    public function assignedSpecialLeaves(): HasMany
    {
        return $this->assignedLeaves()
            ->whereHas('leaveType', fn($builder) => $builder->where('type', 'special'));
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function salary($date = null)
    {
        $nowDate = $date ?: nowFromApp();

        return $this->hasOne(Salary::class)
            ->where(fn (Builder $b) => $b
                ->where(fn (Builder $builder) => $builder
                    ->whereDate('start_at', '<=', $nowDate)
                    ->whereNull('end_at')
                )->orWhere(fn (Builder $builder) => $builder
                    ->whereDate('start_at', '<=', $nowDate)
                    ->whereDate('end_at', '>', $nowDate)
                )->orWhere(fn (Builder $builder) => $builder
                    ->whereNull('start_at')
                    ->whereNull('end_at')
                )->orWhere(fn (Builder $builder) => $builder
                    ->whereNull('start_at')
                    ->whereDate('end_at', '>', $nowDate)
                )
            );
    }

    public function updatedSalary()
    {
        return $this->hasOne(Salary::class)
            ->whereNull('end_at');
    }

    public function payrunSetting()
    {
        return $this->morphOne(PayrunSetting::class, 'payrun_settingable');
    }

    public function payrunBeneficiaries()
    {
        return $this->morphMany(BeneficiaryValue::class, 'beneficiary_valuable');
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }

    public function upcomingWorkingShift()
    {
        return $this->hasMany(UpcomingUserWorkingShift::class, 'user_id', 'id');
    }
    public function managedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_manager', 'user_id', 'project_id')
            ->wherePivotNull('end_date');
    }
    public function managedProjectsWithinDate($date)
    {
        $date = Carbon::parse($date);

        return $this->belongsToMany(Project::class, 'project_manager', 'user_id', 'project_id')
            ->wherePivot('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('project_manager.end_date', '>=', $date)
                    ->orWhereNull('project_manager.end_date');
            });
    }

}
