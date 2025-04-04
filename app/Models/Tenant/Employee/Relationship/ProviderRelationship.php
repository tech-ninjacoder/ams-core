<?php


namespace App\Models\Tenant\Employee\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\ProviderUser;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use Illuminate\Support\Facades\Log;

trait ProviderRelationship
{
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'provider_user',
            'provider_id',
            'user_id'
        )->using(ProviderUser::class)
            ->withPivot('start_date', 'end_date');
    }
    public function users2()
    {
        return $this->belongsToMany(
            User::class,
            'provider_user',
            'provider_id',
            'user_id'
        )->using(ProviderUser::class)
            ->withPivot('start_date', 'end_date');
    }
    public function project()
    {
        return $this->hasManyThrough(Project::class, ProjectUser::class);
    }
    public function projects()
    {
//        return $this->hasManyThrough(Project::class, User::class);
        return $this->hasManyThrough(Project::class, User::class);
        }
    public function users_with_trash()
    {
        return $this->belongsToMany(
            User::class,
            'provider_user',
            'provider_id',
            'user_id'
        )->using(ProviderUser::class)
            ->withPivot('start_date', 'end_date')->withTrashed();
    }

}
