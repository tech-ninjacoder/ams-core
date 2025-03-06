<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Project\Project;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Project\ProjectService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class ProjectWorkingShiftController extends Controller
{
    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function index(Project $project): array
    {
//        $DeptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id());
//
//        $project = $project->load(['working_shifts' => function ($builder) use ($DeptUsers) {
//            $builder->when(request('access_behavior') == 'own_departments',
//                function (Builder $builder) use ($DeptUsers) {
//                    $builder->whereIn('id', $DeptUsers);
//                })->select('id');
//        }]);
        $working_shift_id[0] = $project->working_shifts()
            ->pluck('id')
            ->first();
//        Log::info($working_shift_id);
//        $project_id = [];
//        $project_id[0] = $working_shift_id;
        return $working_shift_id;
    }

    public function store(Project $project, Request $request)
    {
        $this->service
            ->setIsUpdating(true)
            ->setAttribute('id',$request->get('working_shifts'))
//            ->setAttributes($request->get('managers'))


            ->setModel($project)
//            ->validateUsers()
//            ->assignUpdateToUserAsUpcoming(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($project, $request->get('users', [])) :
//                request()->get('users', []));


//            ->assignToWorkingShift(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($request->get('working_shifts', [])) :
//                request()->get('working_shifts', [])
            ->assignToWorkingShift(
                request()->get('working_shifts', [])
            );

        return [
            'status' => true,
            'message' => trans('default.added_response', [
                'subject' => trans('default.working_shifts'),
                'object' => trans('default.project')
            ])
        ];
    }

    public function update(Request $request)
    {
        validator($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'working_shifts' => 'required|array'
        ])->validate();

        $project = Project::findOrFail($request->get('department_id'));

        DB::transaction(function() use($project, $request) {
            $this->service
                ->setModel($project)
                ->setAttributes($request->only('project_id', 'working_shifts'))
                ->moveWorkingShift();

        });

        return [
            'status' => true,
            'message' => trans('default.move_response', [
                'subject' => __t('working_shift'),
                'location' => $project->name
            ])
        ];
    }
}
