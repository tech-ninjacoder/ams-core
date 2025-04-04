<?php

namespace App\Models\Tenant\Attendance;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Relationship\AttendanceDetailsRelationship;
use App\Models\Tenant\TenantModel;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class AttendanceDetails extends TenantModel
{
    use AttendanceDetailsRelationship;

    protected $fillable = [
        'in_time', 'out_time', 'attendance_id', 'status_id', 'review_by','added_by', 'attendance_details_id','project_id'
    ];

    public static function getUnPunchedOut($user_id)
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        return self::query()
            ->whereNull('out_time')
            ->whereHas('attendance', fn(Builder $builder) => $builder->where('user_id', $user_id))
            ->where('status_id', $attendanceApprove)
            ->first();
    }
    public function users(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Attendance::class);
    }

}
