<?php


namespace App\Models\Tenant\Employee\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\HelmetUser;

trait HelmetRelationship
{
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'helmet_user',
            'helmet_id',
            'user_id'
        )->using(HelmetUser::class)
            ->withPivot('start_date', 'end_date');
    }
}
