<?php

namespace App\Models\Tenant\Project;

use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectUser extends Pivot
{
    protected $primaryKey = false;



    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'project_id', 'user_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedUserIds(int $projectId, array $users = []): array
    {
        $existed = self::query()
            ->where('project_id', $projectId)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn ($projectUser) => !in_array($projectUser, $existed));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
