<?php


namespace App\Models\Tenant\Attendance\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Attendance\RecurringAttendanceUser;
use App\Models\Tenant\Employee\Profile;
use App\Models\Tenant\Employee\SkillUser;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\Traits\CommentableTrait;
use App\Models\Tenant\WorkingShift\WorkingShift;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait RecurringAttendanceRelationship
{
    use CommentableTrait;
    use StatusRelationship;

    public function maker(): BelongsTo
    {
        return $this->belongsTo(User::class,'added_by', 'id');
    }

    public function workingShift(): BelongsTo
    {
        return $this->belongsTo(WorkingShift::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'recur_at_user',
            'recurring_attendance_id',
            'user_id'
        )->using(RecurringAttendanceUser::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');

    }


}
