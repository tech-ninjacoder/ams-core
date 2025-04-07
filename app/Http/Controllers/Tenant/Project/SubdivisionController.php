<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\SubdivisionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Project\SubdivisionRequest;
use App\Models\Tenant\Project\Subdivision;
use App\Services\Tenant\Project\SubdivisionService;

class SubdivisionController extends Controller
{
    public function __construct(SubdivisionService $service, SubdivisionFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );
    }

    public function index_api(){
        $subdivision = Subdivision::select('id','name')->get();
        return $subdivision->toJson();
    }

    public function store(SubdivisionRequest $request)
    {
        $this->service->save(
            $request->only('name', 'parent_id')
        );

        return created_responses('subdivision');
    }

    public function show(Subdivision $subdivision)
    {
        return $subdivision;
    }

    public function update(Subdivision $subdivision, SubdivisionRequest $request)
    {
        $subdivision->update(
            request()->only('name', 'parent_id')
        );

        return updated_responses('subdivision');
    }

    public function destroy(Subdivision $subdivision)
    {

        $subdivision->delete();

        return deleted_responses('subdivision');
    }

    }
