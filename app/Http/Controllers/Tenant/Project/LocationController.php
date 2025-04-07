<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\LocationFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Project\LocationRequest;
use App\Models\Tenant\Project\Location;
use App\Services\Tenant\Project\LocationService;

class LocationController extends Controller
{
    public function __construct(LocationService $service, LocationFilter $filter)
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
        $location = Location::select('id','name')->get();
        return $location->toJson();
    }

    public function store(LocationRequest $request)
    {
        $this->service->save(
            $request->only('name', 'parent_id')
        );

        return created_responses('location');
    }

    public function show(Location $location)
    {
        return $location;
    }

    public function update(Location $location, LocationRequest $request)
    {
        $location->update(
            request()->only('name', 'parent_id')
        );

        return updated_responses('location');
    }

    public function destroy(Location $location)
    {

        $location->delete();

        return deleted_responses('location');
    }

    }
