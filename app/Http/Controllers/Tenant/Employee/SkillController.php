<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\ProvidersFilter;
use App\Filters\Tenant\SkillsFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\ProviderRequest;
use App\Http\Requests\Tenant\Employee\SkillRequest;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\Skill;
use App\Services\Tenant\Employee\ProviderService;
use App\Services\Tenant\Employee\SkillService;

class SkillController extends Controller
{
    public function __construct(SkillService $service, SkillsFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->withCount(['users' => function($query){
                $query
                    ->where('end_date',null)
                    ->where('is_in_employee',1);//get only number of active related user to the provider and is an employee
            } ])
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );
    }

    public function store(SkillRequest $request)
    {
        $this->service->save(
            $request->only('name', 'description')
        );

        return created_responses('skill');
    }

    public function show(Skill $skill)
    {
        return $skill;
    }

    public function update(Skill $skill, SkillRequest $request)
    {
        $skill->update(
            request()->only('name', 'description')
        );

        return updated_responses('skill');
    }

    public function destroy(Skill $skill)
    {
        if ($skill->is_default) {
            throw new GeneralException(__t('action_not_allowed'));
        }
        elseif ($skill->users->count()) {
            throw new GeneralException(__t('cant_delete_skill'));
        }

        $skill->delete();

        return deleted_responses('skill');
    }

}
