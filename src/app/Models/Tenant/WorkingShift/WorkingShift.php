<?php

namespace App\Models\Tenant\WorkingShift;

use App\Models\Tenant\TenantModel;
use App\Models\Tenant\WorkingShift\Relationship\WorkingShiftRelationShip;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


class WorkingShift extends TenantModel
{
    use WorkingShiftRelationShip, SoftDeletes;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'description', 'tenant_id', 'is_default', 'type','base','lunch_break'
    ];

    public function setStartDateAttribute($date = null)
    {
        if ($date) {
            $this->attributes['start_date'] = Carbon::parse($date)->format('Y-m-d');
        }else {
            $this->attributes['start_date'] = $date;
        }
    }

    public function setEndDateAttribute($date = null)
    {
        if ($date) {
            $this->attributes['end_date'] = Carbon::parse($date)->format('Y-m-d');
        }else {
            $this->attributes['end_date'] = $date;
        }
    }

    public static function getDefault($columns = '*'): WorkingShift
    {
        return self::where('is_default', 1)->first($columns);
    }

}
