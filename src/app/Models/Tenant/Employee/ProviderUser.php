<?php


namespace App\Models\Tenant\Employee;


use Illuminate\Database\Eloquent\Relations\Pivot;

class ProviderUser extends Pivot
{
    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'provider_id', 'user_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedUsers(int $providerId, array $users): array
    {
        $existed = self::query()
            ->where('provider_id', $providerId)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn($user) => !in_array($user, $existed));
    }
}
