<?php


namespace App\Models\Tenant\Employee;


use Illuminate\Database\Eloquent\Relations\Pivot;

class SkillUser extends Pivot
{
    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'skill_id', 'user_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedUsers(int $skillId, array $users): array
    {
        $existed = self::query()
            ->where('skill_id', $skillId)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn($user) => !in_array($user, $existed));
    }
}
