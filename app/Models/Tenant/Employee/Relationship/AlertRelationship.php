<?php


namespace App\Models\Tenant\Employee\Relationship;


use App\Models\Core\Auth\User;

trait AlertRelationship
{
    public function users()
    {
        return $this->belongsTo(User::class,'user_id');

    }
}
