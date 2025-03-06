<?php

namespace App\Http\Controllers\Core\Log;

use App\Filters\Core\ActivityLogFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Core\Log\ActivityLog;
use App\Models\Tenant\Employee\EmployeeAlerts;
use App\Models\Tenant\Project\Project;
use http\Env\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function __construct(ActivityLogFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return ActivityLog::query()
            ->with('subject', 'causer')
            ->filters($this->filter)
            ->paginate(request()->get('per_page', 15));
    }

    public function show()
    {
        return $this->getActivity(auth()->user());
    }

    public function activities(User $user)
    {
        return $this->getActivity($user);
    }
    public function ProjectActivities(Project $project)
    {
        $user = auth()->user();
        return $this->getProjectActivity($project, $user);
    }

    public function getActivity($user)
    {
        return $user->actions()
        ->with('subject')
        ->when(request()->search, function (Builder $query){
            $query->where('description', 'LIKE', "%".request()->search."%");
        })
        ->orderBy('id', 'DESC')
        ->select('description', 'properties', 'created_at', 'subject_type', 'subject_id')
        ->paginate(request('per_page', 10));
    }
    public function getProjectActivity($project, $user)
    {
        return $user->actions()
            ->with('subject')
            ->when(request()->search, function (Builder $query){
                $query->where('description', 'LIKE', "%".request()->search."%");
            })->where('subject_id',$project->id)
            ->orderBy('id', 'DESC')
            ->select('description', 'properties', 'created_at', 'subject_type', 'subject_id')
            ->paginate(request('per_page', 10));
    }
    public function alerts($user)
    {
//        Log::info('$user => '.$user);
//        $alert = EmployeeAlerts::where('user_id','=',$user)->get();
//        Log::info('$alert => '.$alert);

        return $this->getAlert($user);
    }
    public function getAlert($user)
    {
        return EmployeeAlerts::where('user_id','=',$user)
            ->when(request()->search, function (Builder $query){
                $query->where('type', 'LIKE', "%".request()->search."%");
            })
            ->orderBy('id', 'DESC')
            ->select('type', 'date', 'note', 'created_at')
            ->paginate(request('per_page', 10));
    }

}
