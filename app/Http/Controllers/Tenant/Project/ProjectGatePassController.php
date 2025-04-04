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

class ProjectGatePassController extends Controller
{
    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function index(Project $project): array
    {
        $DeptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id());

        $project = $project->load(['gate_passes' => function ($builder) use ($DeptUsers) {
            $builder->when(request('access_behavior') == 'own_departments',
                function (Builder $builder) use ($DeptUsers) {
                    $builder->whereIn('id', $DeptUsers);
                })->select('id');
        }]);

//        $upcomingUser = UpcomingUserWorkingShift::query()
//            ->when(request('access_behavior') == 'own_departments',
//                function (Builder $builder) use($DeptUsers){
//                    $builder->whereIn('id', $DeptUsers);
//            })->where('working_shift_id', $workingShift->id)
//            ->pluck('user_id')
//            ->toArray();

        return $project->gate_passes->pluck('id')->toArray();
    }

    public function store(Project $project, Request $request)
    {
        Log::info('GatePAsses '.json_encode($request->get('gate_passes', [])));

        $this->service
            ->setIsUpdating(true)
            ->setAttribute('id',$request->get('gate_passes'))
//            ->setAttributes($request->get('managers'))


            ->setModel($project)
//            ->validateUsers()
//            ->assignUpdateToUserAsUpcoming(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($project, $request->get('users', [])) :
//                request()->get('users', []));

//            ->assignToGatesPasses(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($request->get('gate_passes', [])) :
//                request()->get('gate_passes', [])

            ->assignToGatesPasses(
                request()->get('gate_passes', [])
            );

        return [
            'status' => true,
            'message' => trans('default.added_response', [
                'subject' => trans('default.employees'),
                'object' => trans('default.project')
            ])
        ];
    }

    public function update(Request $request)
    {
        validator($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'gate_pass_id' => 'required|array'
        ])->validate();

        $project = Project::findOrFail($request->get('department_id'));

        DB::transaction(function() use($project, $request) {
            $this->service
                ->setModel($project)
                ->setAttributes($request->only('project_id', 'gates_passes'));
//                ->moveManager();

        });

        return [
            'status' => true,
            'message' => trans('default.move_response', [
                'subject' => __t('employee'),
                'location' => $project->name
            ])
        ];
    }
}
