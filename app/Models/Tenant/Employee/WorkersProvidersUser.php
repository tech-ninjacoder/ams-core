<?php
namespace App\Models\Tenant\Employee;

use App\Models\Tenant\TenantModel;
class WorkersProvidersUser extends TenantModel
{

    protected $fillable = [
        'start_date', 'end_date', 'work_provider_id', 'user_id'
    ];
    protected $dates = [
        'start_date', 'end_date'
    ];
    public static function getNoneExistedUsers(int $work_provider_id, array $users): array
    {
        $existed = self::query()
            ->where('work_provider_id', $work_provider_id)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn ($user) => !in_array($user, $existed));
    }

}
