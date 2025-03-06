<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Exports\ExportProjectReport;
use App\Exports\ExportProjectVisitsReport;
use App\Http\Controllers\Controller;
use App\Models\Core\Status;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Project\ProjectService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class ProjectUserController extends Controller
{
    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    public function index(Project $project): array
    {
        $DeptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id());

        $project = $project->load(['users' => function ($builder) use ($DeptUsers) {
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

        return $project->users->pluck('id')->toArray();
    }

    public function projectVisits(Project $project)
    {

        $response = (new ExportProjectVisitsReport($project->id))->download('visits.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();

        return $response;

    }
    public function ProjectEmployees(Project $project): array
    {
        $paginated = $this->service
//            ->with('details')
            ->with('users')
            ->where('id',$project->id)
            ->paginate(request()->get('per_page', 10));

//        $paginated->each(function (Project $project) {
//            $details = $project->details->firstWhere('is_weekend', 0);
//            $project->setAttribute('start_at', $details->start_at);
//            $project->setAttribute('end_at', $details->end_at);
//            unset($shift->details);
//            return $shift;
//        });

        $response = $paginated->toArray();

        $response['data'] = $paginated->items();

        return $response;    }

    public function store(Project $project, Request $request)
    {
        $this->service
            ->setIsUpdating(true)
            ->setAttributes($request->get('users'))
            ->setModel($project)
            ->validateUsers()
//            ->assignUpdateToUserAsUpcoming(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($project, $request->get('users', [])) :
//                request()->get('users', []));


//            ->assignToUsers(request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($request->get('users', [])) :
//                request()->get('users', [])
//            );
            ->assignToUsers(
                request()->get('users', [])
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
            'users' => 'required|array'
        ])->validate();

        $project = Project::findOrFail($request->get('department_id'));

        DB::transaction(function() use($project, $request) {
            $this->service
                ->setModel($project)
                ->setAttributes($request->only('project_id', 'users'))
                ->moveEmployee();

        });

        return [
            'status' => true,
            'message' => trans('default.move_response', [
                'subject' => __t('employee'),
                'location' => $project->name
            ])
        ];
    }

    public function addGeometry(Project $project, Request $request)
    {
//        $geo  = $request->json()->all();
        $cc = response()->json($request)->getContent();
        $arr = json_decode($cc, true);

        if (sizeof($arr['Geometry'])) { //if size of geometry is different from the existed one

            $project = Project::find($project->id);
            $project->geometry = $arr['Geometry']; //add the geomerty to db
            $project->center = $this->getCentroidOfPolygon($arr['Geometry']); // calculate the and save the center of geometry
            $project->save();


            //TODO: make the update check if the geofence id exist on viewtech if no create it.
            // update geofence
            if (!is_null($project->geofence_id)) { // if this project does have a geofence id means it needs update and we need to send api request to update the geofence
                $project->geofence_id = $this->GetGeofenceOfProject($project->pme_id); // get update the geofence_id in local db with geofence on tracking server by matching names
                $this->GeometryUpdateGeofence($project);
                $project->save();
                Log::info('project geofence updated');
                Artisan::call('update:alert');
            }
            $project->save();
//            Log::info('Request ' . $this->GetGeofenceOfProject($project->name)); //get the geofence name matching project name

            //create geofence
            if (is_null($project->geofence_id)) { //if this project geofence id is empty means geometry created for the first time
                Log::info('Request '.$this->GeometryToGeofence($project)); //create the geofence for the project after adding geometry in local db
                Log::info('Placed a new geofence and got the id ' . $this->GetGeofenceOfProject($project->pme_id)); //get the geofence name matching project name
                $project->geofence_id = $this->GetGeofenceOfProject($project->pme_id); // set the newly created geofence id to geofence_id field in local db
                $project->save();
                Artisan::call('update:alert');
            }


            return updated_responses('projects');
//            Log::info(' updated //boudgeau');

//        $this->GeometryToGeofence($project);
//        $this->GetGeofenceOfProject();


        } else {
            return updated_responses('projects');
//            Log::info(' no update :=>  //boudgeau');
//            Log::info($this->getCentroidOfPolygon( $arr['Geometry']));


        }
//        Log::info(' project ID :=> '.$project.' //boudgeau');

//        Log::info('Geometry attached for project:=> '.print_r($arr['Geometry'],1).' //boudgeau');

    }

    public function getGeometry(Project $project)
    {
        $project = Project::find($project->id);
        $arr = Project::find($project->id)->geometry;
        $arr1 = json_decode($arr, true);

//        Log::info($this->getCentroidOfPolygon($arr1));

        return $project->geometry;

    }

    public function getBackgroundPolygons(Project $project)
    {
        $statusCompleted = Status::where('name', 'status_completed')->where('type','project')->value('id');

        $projects = Project::where('id', '!=', $project->id)
            ->whereNotNull('geometry')
            ->where('status_id', '!=', $statusCompleted)
            ->get();

        $geometries = [];

        foreach ($projects as $project) {
            $geometry = json_decode($project->geometry);
            $name = $project->name;

            $geometries[] = [
                'name' => $name,
                'geometry' => $geometry
            ];
        }

        return response()->json($geometries);


    }

    //helper function to get the area of a certain polygon
    public function getAreaOfPolygon($geometry)
    {
        $area = 0;

        for ($vi = 0, $vl = sizeof($geometry); $vi < $vl; $vi++) {
            $thisx = $geometry[$vi]['lat'];
            $thisy = $geometry[$vi]['lng'];
            $nextx = $geometry[($vi + 1) % $vl]['lat'];
            $nexty = $geometry[($vi + 1) % $vl]['lng'];
            $area += ($thisx * $nexty) - ($thisy * $nextx);

        }

        // done with the rings: "sign" the area and return it
        $area = abs(($area / 2));
        return $area;
    }

    /*
     * calculate the centroid of a polygon
     * return a 2-element list: array($x,$y)
     */
    public function getCentroidOfPolygon($geometry)
    {
        $cx = 0;
        $cy = 0;

        for ($vi = 0, $vl = sizeof($geometry); $vi < $vl; $vi++) {
            $thisx = $geometry[$vi]['lat'];
            $thisy = $geometry[$vi]['lng'];
            $nextx = $geometry[($vi + 1) % $vl]['lat'];
            $nexty = $geometry[($vi + 1) % $vl]['lng'];

            $p = ($thisx * $nexty) - ($thisy * $nextx);
            $cx += ($thisx + $nextx) * $p;
            $cy += ($thisy + $nexty) * $p;

        }

        // last step of centroid: divide by 6*A
        $area = $this->getAreaOfPolygon($geometry);
        $cx = -$cx / (6 * $area);
        $cy = -$cy / (6 * $area);

        // done!
        return array('lat' => $cx, 'lng' => $cy);
    }

    public function GeometryToGeofence($project)
    {
        $project_name = Project::find($project->id)->pme_id;
        Log::info('this is project_name: '.$project_name);
        $project_geometry = Project::find($project->id)->geometry;


        $params = [
            "group_id" => '0',
            "active" => 1,
            "name" => $project_name,
            "polygon_color" => "#d000df",
            "type" => "polygon",
            "polygon" => $project_geometry
        ];
        $data = json_encode($params);

        $client = new Client([
            'headers' => ['Content-Type' => 'application/json'],
            'verify' => env('HTTP_VERIFY_SSL', true), // Controlled via .env

        ]);
        $response = $client->post(env('vts_api_path').'/add_geofence?lang=en&user_api_hash='.env('vts_api_token'),
            ['body' => $data]
        );
        $response = json_decode($response->getBody(), true);
//        Log::info($response);
    }

    //get list of geofences and match it with project id to assign the project id to project first time only
    public function GetGeofenceOfProject($project_id)
    {


//        $response = Http::get(env('vts_api_path').'/get_geofences?lang=en&user_api_hash='.env('vts_api_token'));
//        Log::info($response);
        $response = Http::withOptions([
            'verify' => env('HTTP_VERIFY_SSL', true), // Controlled via .env
        ])->get(env('vts_api_path') . '/get_geofences', [
            'lang' => 'en',
            'user_api_hash' => env('vts_api_token'),
        ]);

        $response = $response->json();
//        Log::info($response);

        //check response and try to find a geofence with same project name or id
        if (!empty($response)) {
            foreach ($response as $key => $value) {
                Log::info($key);
                if ($key == 'items') {
//                    Log::info('this items array');
                    if (is_array($value)){
                        foreach ($value as $items => $item) {
                            if ($items == 'geofences') {
//                                Log::info('this is geofences array');
                                if (is_array($item)) {
                                    foreach ($item as $geofences => $geofence) {
//                                        Log::info('this is geofences');
                                        if (is_array($geofence)) {
                                            foreach ($geofence as $params => $param) {
                                                if ($params == 'name') {
                                                    if ($param == $project_id) {
                                                        Log::info('found it  '.$param);
                                                        Log::info('the matching geofence id is '.$geofence['id']);
                                                        return $geofence['id'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }

        }else return null;
    }

    public function GeometryUpdateGeofence($project)
    {
        $project_name = Project::find($project->id)->pme_id;
        $project_geometry = $project->geometry;
        Log::info('The project  ==>'.Project::find($project->id)->geofence_id);


        $params = [
            "id" => Project::find($project->id)->geofence_id,
            "group_id" => '0',
            "active" => 1,
            "name" => $project_name,
            "polygon_color" => "#d000df",
            "type" => "polygon",
            "polygon" => $project_geometry
        ];
        $data = json_encode($params);
        Log::info('geofence update start on VTS');
        Log::info($data);


        $client = new Client([
            'headers' => ['Content-Type' => 'application/json'],
            'verify' => env('HTTP_VERIFY_SSL', true), // Controlled via .env

        ]);
        $response = $client->post(env('vts_api_path').'/edit_geofence?lang=en&user_api_hash='.env('vts_api_token'),
            ['body' => $data]
        );
        $response = json_decode($response->getBody(), true);
        Log::info($response);
        Log::info('geofence update END on VTS');
        Artisan::call('update:alert');
    }


}
