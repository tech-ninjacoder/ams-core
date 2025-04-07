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

class ProjectManagerController extends Controller
{
    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function index(Project $project): array
    {
        $DeptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id());

        $project = $project->load(['managers' => function ($builder) use ($DeptUsers) {
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

        return $project->managers->pluck('id')->toArray();
    }

    public function store(Project $project, Request $request)
    {
        $this->service
            ->setIsUpdating(true)
            ->setAttribute('id',$request->get('managers'))
//            ->setAttributes($request->get('managers'))


            ->setModel($project)
            ->validateUsers()
//            ->assignUpdateToUserAsUpcoming(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($project, $request->get('users', [])) :
//                request()->get('users', []));


//            ->assignManagerToUsers(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($request->get('managers', [])) :
//                request()->get('managers', [])
            ->assignManagerToUsers(
                request()->get('managers', [])
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
            'managers' => 'required|array'
        ])->validate();

        $project = Project::findOrFail($request->get('department_id'));

        DB::transaction(function() use($project, $request) {
            $this->service
                ->setModel($project)
                ->setAttributes($request->only('project_id', 'managers'))
                ->moveManager();

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
