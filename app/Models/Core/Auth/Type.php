<?php

namespace App\Models\Core\Auth;

use App\Models\Core\BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Type extends BaseModel
{
    use LogsActivity;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected $fillable = [
        'name', 'alias'
    ];
    protected static $logAttributes = [
        'name', 'alias'
    ];

    public static function findByAlias(string $alias)
    {
        return self::query()->whereAlias($alias)->first();
    }
}
