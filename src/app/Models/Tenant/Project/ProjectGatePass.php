<?php

namespace App\Models\Tenant\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectGatePass extends Pivot
{
    use HasFactory;
    protected $table = 'project_gate_passes';

    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'project_id', 'gate_passe_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedGatepass(int $projectId, array $gate_passes): array
    {
        $existed = self::query()
            ->where('project_id', $projectId)
            ->whereNull('end_date')
            ->pluck('gate_passe_id')
            ->toArray();

        return array_filter($gate_passes, fn($gate_passe) => !in_array($gate_passe, $existed));
    }
}
