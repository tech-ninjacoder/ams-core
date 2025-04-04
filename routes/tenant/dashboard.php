<?php


use App\Http\Controllers\Restore\RestoreController;
use App\Http\Controllers\Tenant\Dashboard\AdminDashboardController;
use App\Http\Controllers\Tenant\Dashboard\EmployeeDashboardController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'app/dashboard'], function (Router $router) {
    Route::group(['middleware' => ['check_behavior', 'request_show_all']], function (Router $router){
        $router->get('summery', [AdminDashboardController::class, 'summery'])
            ->name('summery.dashboard');

        $router->get('on-working', [AdminDashboardController::class, 'onWorking'])
            ->name('on-working.dashboard');

        $router->get('employee-statistics', [AdminDashboardController::class, 'employeeStatistics'])
            ->name('employee-statistics.dashboard');

//        $router->get('labor-supply-statistics', [AdminDashboardController::class, 'LaborSupply'])
//            ->name('employee-statistics.dashboard');
        $router->get('labor-supply-statistics', [AdminDashboardController::class, 'LaborSupply'])
            ->name('employee-statistics.dashboard');
        $router->get('project-pivot-statistics', [AdminDashboardController::class, 'ProjectPivot'])
            ->name('project-pivot-statistics.dashboard');



        $router->get('projects-statistics', [AdminDashboardController::class, 'ProjectStatistics'])
            ->name('employee-statistics.dashboard');
    });

    Route::group(['prefix' => 'employee'], function (Router $router) {
        $router->get('attendance', [EmployeeDashboardController::class, 'employeeAttendance'])
            ->name('attendance.dashboard');

        $router->get('attendance-log', [EmployeeDashboardController::class, 'employeeMonthlyAttendanceLog'])
            ->name('attendance-log.dashboard');

        $router->get('leave-summaries', [EmployeeDashboardController::class, 'employeeLeaveSummaries'])
            ->name('leave-summaries.dashboard');
    });

});

Route::post('app/restore-data', [RestoreController::class, 'index'])
    ->name('restore.data');

Route::get('app/database-2/credentials', [RestoreController::class, 'settings'])
    ->name('restore.data');

Route::patch('app/database-2/credentials', [RestoreController::class, 'updateSettings'])
    ->name('restore.data');

Route::post('app/restore-data/attendance', [RestoreController::class, 'attendanceImport'])
    ->name('restore.data');
