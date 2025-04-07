<?php

namespace App\Models\Tenant\Project;

use App\Models\Tenant\WorkingShift\WorkingShift;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectWorkingShift extends Pivot
{
    use HasFactory;
    protected $table = 'project_working_shifts';

    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'project_id', 'working_shift_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedWorkingShiftIds(int $projectId, array $working_shifts = []): array
    {
        $existed = self::query()
            ->where('project_id', $projectId)
            ->whereNull('end_date')
            ->pluck('working_shift_id')
            ->toArray();

        return array_filter($working_shifts, fn ($projectWorkingShift) => !in_array($projectWorkingShift, $existed));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
//    public function working_shift(): BelongsTo
//    {
//        return $this->belongsTo(WorkingShift::class);
//    }
}
