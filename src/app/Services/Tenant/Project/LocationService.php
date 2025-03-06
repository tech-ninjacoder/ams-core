<?php

namespace App\Services\Tenant\Project;

use App\Models\Tenant\Project\Location;
use App\Models\Tenant\Project\ProjectLocation;
use App\Services\Tenant\TenantService;

class LocationService extends TenantService
{
    protected $locationId;

    public function __construct(Location $location )
    {
        $this->model = $location;
    }

    public function assignToProjects($projects)
    {
        $projects = is_array($projects) ? $projects : func_get_args();

        $this->endPreviousLocationOfProjects($projects);

        ProjectLocation::insert(
            array_map(
                fn($project) => [
                    'user_id' => $project,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'location_id' => $this->getLocationId()
                ],
                ProjectLocation::getNoneExistedLocation($this->getLocationId(), $projects)
            )
        );
    }

    public function endPreviousLocationOfProjects($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        ProjectLocation::whereIn('user_id', $users)
            ->whereNull('end_date')
            ->where('location_id', '!=', $this->getLocationId())
            ->update([
                'end_date' => todayFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function setLocationId($locationId): LocationService
    {
        $this->locationId = $locationId;
        return $this;
    }


    public function getLocationId()
    {
        return $this->locationId ?: $this->model->id;
    }

}
