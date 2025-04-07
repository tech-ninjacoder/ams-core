<?php

namespace App\Models\Tenant\Project;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectParent extends Pivot
{
    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'project_id', 'parent_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedParentIds(int $projectId, array $parents = []): array
    {
        $existed = self::query()
            ->where('project_id', $projectId)
            ->whereNull('end_date')
            ->pluck('parent_id')
            ->toArray();

        return array_filter($parents, fn ($projectParent) => !in_array($projectParent, $existed));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

}
