<?php

//use App\Http\Controllers\Tenant\WorkingShift\WorkingShiftController;
//use App\Http\Controllers\Tenant\WorkingShift\WorkingShiftUserController;
use App\Http\Controllers\Tenant\Attendance\RecurringAttendanceController;
use App\Http\Controllers\Tenant\Attendance\RecurringAttendanceUserController;
use App\Http\Controllers\Tenant\Project\ContractorController;
use App\Http\Controllers\Tenant\Project\LocationController;
use App\Http\Controllers\Tenant\Project\ProjectAPIController;
use App\Http\Controllers\Tenant\Project\ProjectController;
use App\Http\Controllers\Tenant\Project\ProjectCoordinatorController;
use App\Http\Controllers\Tenant\Project\ProjectGatePassController;
use App\Http\Controllers\Tenant\Project\ProjectManagerController;
use App\Http\Controllers\Tenant\Project\ProjectUserController;
use App\Http\Controllers\Tenant\Project\ProjectWorkingShiftController;
use App\Http\Controllers\Tenant\Project\SubdivisionController;
use App\Models\Tenant\Attendance\RecurringAttendance;
use App\Models\Tenant\Project\Project;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'app', 'middleware' => ['check_behavior', 'check_project_behavior']], function (Router $router) {
    $router->post('projects/{project}/add-employees', [ProjectUserController::class, 'store'])
        ->name('project.add-employees-to');
    $router->post('projects/{project}/add-managers', [ProjectManagerController::class, 'store'])
        ->name('project.add-employees-to');
    $router->post('projects/{project}/add-working-shifts', [ProjectWorkingShiftController::class, 'store'])
        ->name('project.add-employees-to');
    $router->post('projects/{project}/add-gate-passes', [ProjectGatePassController::class, 'store'])
        ->name('project.add-gate-pass');
    $router->post('projects/{project}/add-coordinators', [ProjectCoordinatorController::class, 'store'])
        ->name('project.add-employees-to');

    $router->apiResource('contractors', ContractorController::class);
    $router->apiResource('locations', LocationController::class); //boudgeau
    $router->apiResource('subdivisions', SubdivisionController::class); //boudgeau


//    $router->post('projects/update', [ProjectUserController::class, 'update'])
//        ->name('project-employees.move');


    $router->post('projects/{project}/add-geometry', [ProjectUserController::class, 'addGeometry'])
        ->name('project.add-employees-to');
    $router->get('projects/{project}/get-geometry', [ProjectUserController::class, 'getGeometry'])
        ->name('working-project.add-employees-to');
    $router->get('projects/{project}/get-background-polygons', [ProjectUserController::class, 'getBackgroundPolygons'])
        ->name('working-project.get-background-polygons');


    $router->get('projects/{project}/users', [ProjectUserController::class, 'index'])
        ->name('working-project.add-employees-to');
    $router->get('projects/employees/{project}', [ProjectUserController::class, 'ProjectEmployees'])
        ->name('working-project.get-employees-project');


    $router->get('projects/{project}/managers', [ProjectManagerController::class, 'index'])
        ->name('working-project.add-employees-to');
    $router->get('projects/{project}/working-shifts', [ProjectWorkingShiftController::class, 'index'])
        ->name('working-project.add-employees-to');
    $router->get('projects/{project}/gate-passes', [ProjectGatePassController::class, 'index'])
        ->name('working-project.add-employees-to');
    $router->get('projects/{project}/coordinators', [ProjectCoordinatorController::class, 'index'])
        ->name('working-project.add-employees-to');



    $router->get('projects/{project}', [ProjectController::class, 'show'])
        ->name('working-project.add-employees-to');

//    $router->get('project/{project}', [ProjectController::class, 'projectDetails'])
//        ->name('working-project.add-employees-to');
    $router->get('project/report/{project}', [ProjectController::class, 'ExcelExport'])
        ->name('working-project.add-employees-to');
    $router->get('project/pdf/report/{project}', [ProjectController::class, 'PDFExport'])
        ->name('working-project.add-employees-to');
    $router->get('project/excel/visit-report/{project}', [ProjectUserController::class, 'projectVisits'])
        ->name('working-project.add-employees-to');

    $router->get('recurring-attendance/{ra}/users', [RecurringAttendanceUserController::class, 'index'])
        ->name('recurring-attendance.add-employees-to');
    $router->post('recurring-attendance/{ra}/add-employees', [RecurringAttendanceUserController::class, 'store'])
        ->name('project.add-employees-to');





    $router->apiResource('projects', ProjectController::class)->except('show');


});
