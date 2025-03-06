<?php


namespace App\Models\Tenant\Project\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Employee\Skill;
use App\Models\Tenant\Project\Contractor;
use App\Models\Tenant\Project\Location;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectCoordinator;
use App\Models\Tenant\Project\ProjectGatePass;
use App\Models\Tenant\Project\ProjectLocation;
use App\Models\Tenant\Project\ProjectManager;
use App\Models\Tenant\Project\ProjectParent;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\Project\ProjectWorkingShift;
use App\Models\Tenant\Project\Subdivision;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use Illuminate\Support\Facades\Log;

trait   ProjectRelationShip
{
    use StatusRelationship;


    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'project_user',
            'project_id',
            'user_id'
        )->using(ProjectUser::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function managers()
    {
        return $this->belongsToMany(
            User::class,
            'project_manager',
            'project_id',
            'user_id'
        )->using(ProjectManager::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function working_shifts()
    {
//        Log::info('rellll');
        return $this->belongsToMany(
            WorkingShift::class,
            'project_working_shifts',
            'project_id',
            'working_shift_id'
        )->using(ProjectWorkingShift::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function WorkingShifts()
    {
//        Log::info('rellll');
        return $this->belongsToMany(
            WorkingShift::class,
            'project_working_shifts',
            'project_id',
            'working_shift_id'
        )->using(ProjectWorkingShift::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function coordinators()
    {
        return $this->belongsToMany(
            User::class,
            'project_coordinator',
            'project_id',
            'user_id'
        )->using(ProjectCoordinator::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function contractors() {
        return $this->belongsTo(
            Contractor::class,
            'contractor_id',
            'id'
        );
    }

    public function locations() {
        return $this->belongsTo(
            Location::class,
            'location_id',
            'id'
        );
    }
    public function subdivisions() {
        return $this->belongsTo(
            Subdivision::class,
            'subdivision_id',
            'id'
        );
    }
    public function parent()
    {
        return $this->belongsToMany(
            Project::class,
            'project_parent',
            'project_id',
            'parent_id'
        )->using(ProjectParent::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function childrens()
    {
        return $this->belongsToMany(
            Project::class,
            'project_parent',
            'parent_id',
            'project_id'
        )->using(ProjectParent::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function gate_passes()
    {
//        Log::info('rellll');
        return $this->belongsToMany(
            GatePass::class,
            'project_gate_passes',
            'project_id',
            'gate_passe_id'
        )->using(ProjectGatePass::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }
    public function GatePasses()
    {
//        Log::info('rellll');
        return $this->belongsToMany(
            GatePass::class,
            'project_gate_passes',
            'project_id',
            'gate_passe_id'
        )->using(ProjectGatePass::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

//    public function gate_passes()
//    {
//        return $this->belongsToMany(
//            GatePass::class,
//            'project_gate_passes',
//            'gate_passe_id',
//            'project_id'
//        )->using(ProjectGatePass::class)
//            ->withPivot('start_date', 'end_date')
//            ->wherePivotNull('end_date');
//    }
}
