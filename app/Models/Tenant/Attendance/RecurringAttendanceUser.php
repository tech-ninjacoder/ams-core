<?php


namespace App\Models\Tenant\Attendance;


use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Log;

class RecurringAttendanceUser extends Pivot
{
    protected $table = 'recur_at_user'; // Set the table name

    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'recurring_attendance_id', 'user_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedUsers(int $raId, array $users): array
    {
        $existed = self::query()
            ->where('recurring_attendance_id', $raId)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();
        Log::info('$existed '.json_encode($existed));
        Log::info('$users '.json_encode($users));

        return array_filter($users, fn($user) => !in_array($user, $existed));
    }

}
