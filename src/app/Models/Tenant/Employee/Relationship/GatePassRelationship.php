<?php


namespace App\Models\Tenant\Employee\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\GatePassUser;

trait GatePassRelationship
{
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'gate_pass_user',
            'gate_pass_id',
            'user_id'
        )->using(GatePassUser::class)
            ->withPivot('start_date', 'end_date');
    }
}
