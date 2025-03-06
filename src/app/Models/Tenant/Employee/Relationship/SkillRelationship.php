<?php


namespace App\Models\Tenant\Employee\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\ProviderUser;
use App\Models\Tenant\Employee\SkillUser;

trait SkillRelationship
{
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'skill_user',
            'skill_id',
            'user_id'
        )->using(SkillUser::class)
            ->withPivot('start_date', 'end_date');
    }
}
