<?php


namespace App\Http\Controllers\Tenant\Project;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Setting\StatusController;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Tenant\Employee\HelmetUser;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectParent;
use http\Env\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectAPIController extends Controller
{
    public function index()
    {
        Log::info('index');
        // Get the user IDs of managers in those departments
        $user = auth()->user();

        $departmentIds = $user->hasDepartments()->pluck('id');
        Log::info('Department IDs: ', $departmentIds->toArray());

        // Get the user IDs of managers in those departments
        $managerIds = User::whereHas('departments', function ($query) use ($departmentIds) {
            $query->whereIn('departments.id', $departmentIds);
        })->pluck('id');

        Log::info('Manager IDs: ', $managerIds->toArray());

        // Filter projects where the manager's ID is in the list of manager IDs
//        $query->whereHas('managers', function ($query) use ($managerIds) {
//            $query->whereIn('user_id', $managerIds)
//                ->whereNull('project_manager.end_date');
//        });
        if  (auth()->user()->hasRole('App Admin')) {
    Log::info('App Admin');
        return Project::query()
            ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
            ->whereDoesntHave('childrens')
            ->get();
    } elseif(auth()->user()->hasRole('Project Manager') || auth()->user()->hasRole('Area Manager')) {
            Log::info('Area Manager');

            return Project::query()
                ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
//            ->where('type', '=', '0')
                ->whereDoesntHave('childrens')
                ->whereHas('managers', function ($query) use ($managerIds) {
                    $query->whereIn('user_id', $managerIds)
                        ->whereNull('project_manager.end_date');
                })
                ->get();
        }elseif (auth()->user()->hasRole('Project Engineer')) {
           Log::info('Project Engineer');
            return Project::query()
                ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
//            ->where('type', '=', '0')
                ->whereDoesntHave('childrens')
                ->whereHas('managers', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->whereNull('project_manager.end_date');
                })
                ->get();
        } else{
            return Project::query()
                ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
                ->whereDoesntHave('childrens')
                ->get();
        }

    }
    public function index_active()
    {
        Log::info('index');
        $user = auth()->user();

        // Get the department IDs of the user's departments
        $departmentIds = $user->hasDepartments()->pluck('id');
        Log::info('Department IDs: ', $departmentIds->toArray());

        // Get the user IDs of managers in those departments
        $managerIds = User::whereHas('departments', function ($query) use ($departmentIds) {
            $query->whereIn('departments.id', $departmentIds);
        })->pluck('id');

        Log::info('Manager IDs: ', $managerIds->toArray());

        // Dynamically fetch status IDs for 'status_completed' and 'status_onhold'
        $excludedStatuses = Status::where('type', 'project')
            ->whereIn('name', ['status_completed', 'status_onhold'])
            ->pluck('id')
            ->toArray();

        Log::info('Excluded Status IDs: ', $excludedStatuses);

        if (auth()->user()->hasRole('App Admin')) {
            Log::info('App Admin');
            return Project::query()
                ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
                ->whereDoesntHave('childrens')
                ->whereNotIn('status_id', $excludedStatuses)
                ->get();
        } elseif (auth()->user()->hasRole('Project Manager') || auth()->user()->hasRole('Area Manager')) {
            Log::info('Area Manager');
            return Project::query()
                ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
                ->whereDoesntHave('childrens')
                ->whereNotIn('status_id', $excludedStatuses)
                ->whereHas('managers', function ($query) use ($managerIds) {
                    $query->whereIn('user_id', $managerIds)
                        ->whereNull('project_manager.end_date');
                })
                ->get();
        } elseif (auth()->user()->hasRole('Project Engineer')) {
            Log::info('Project Engineer');
            return Project::query()
                ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
                ->whereDoesntHave('childrens')
                ->whereNotIn('status_id', $excludedStatuses)
                ->whereHas('managers', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->whereNull('project_manager.end_date');
                })
                ->get();
        } else {
            return Project::query()
                ->select(['id', 'pme_id', 'name', DB::raw("CONCAT(pme_id, ' - ', name) AS pme_name")])
                ->whereDoesntHave('childrens')
                ->whereNotIn('status_id', $excludedStatuses)
                ->get();
        }
    }


    public function parent()
    {
        Log::info('parent');
        return Project::query()->select(['id','pme_id','name'])
//            ->where('type','=','1')
            ->whereDoesntHave('parent')
            ->get();
    }
    public function addGeometry(Project $project, Request $request) {
        Log::info('Geometry attached for project:=> '.$request.' //boudgeau');
        return deleted_responses('projects');

    }
    public function statuses () {
        return Status::where('type','project')->get();
    }
    public function managers () {

    }

    public function release_parent($project){
        Log::info('release '.$project);
        $project_id = ProjectParent::where('project_id',$project)->where('end_date',null)->get();
        Log::info('Parent detached:=> '.$project_id.' //boudgeau');

        // HelmetUser::where('helmet_id',$helmet)->where('end_date',null)->update(['end_date',today()]);
//        $helmets->end_date = today();
//        $helmets->save();
        DB::table('project_parent')
            ->where('project_id', $project)
            ->where('end_date',null)
            ->update(['end_date' => today()]);



        return detached_response('project');

    }
}
