<?php

use App\Exports\ExportAttendaceTodayLog;
use App\Http\Controllers\Tenant\Attendance\AttendanceCommentController;
use App\Http\Controllers\Tenant\Attendance\AttendanceDailyLogController;
use App\Http\Controllers\Tenant\Attendance\AttendanceDetailsController;
use App\Http\Controllers\Tenant\Attendance\AttendanceSettingController;
use App\Http\Controllers\Tenant\Attendance\AttendanceRequestController;
use App\Http\Controllers\Tenant\Attendance\AttendanceUpdateController;
use App\Http\Controllers\Tenant\Attendance\AttendanceStatusController;
use App\Http\Controllers\Tenant\Attendance\RecurringAttendanceController;
use App\Models\Tenant\Attendance\RecurringAttendance;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'app', 'middleware' => ['can_access:view_all_attendance', 'check_behavior','check_project_behavior']], function (Router $route) {
    $route->get('settings/attendances', [AttendanceSettingController::class, 'index'])
        ->name('attendance-settings.index');
    $route->get('attendance/log/today', [AttendanceDailyLogController::class, 'ExcelExport'])
        ->name('attendance-settings.index');
    $route->post('attendance/log/download', [AttendanceDailyLogController::class, 'ExcelDateExport'])
        ->name('attendance-download.excel');
    $route->post('absence/log/download', [AttendanceDailyLogController::class, 'AbsenceExcelDateExport'])
        ->name('absence-download.excel');
    //boudeagu
    $route->post('attendances/approve/update',[AttendanceDailyLogController::class,'bulk_approve'])
        ->name('attendance.bulk-approve');

    $route->post('attendances/correct/update',[AttendanceDailyLogController::class,'bulk_correct'])
        ->name('attendance.bulk-correct');

    $route->post('attendances/clear_correction/update',[AttendanceDailyLogController::class,'clear_corrections'])
        ->name('attendance.bulk-clear-correction');

    //hrms_upload
    $route->get('attendances/upload',[AttendanceDailyLogController::class,'hrms_upload'])
        ->name('attendance.hrms_upload');


    $route->post('settings/attendances', [AttendanceSettingController::class, 'update'])
        ->name('attendance-settings.update');
    $route->post('settings/attendances/ra', [AttendanceSettingController::class, 'recurring'])
        ->name('attendance-settings.recurring_attendance.update');

    $route->apiResource('recurring-attendance', RecurringAttendanceController::class)->except('show');
    $route->get('recurring-attendance/{ra}', [RecurringAttendanceController::class, 'show'])
        ->name('recurring.show');


    $route->group(['prefix' => 'attendances'], function (Router $router) {
        $router->get('details', [AttendanceDetailsController::class, 'index'])
            ->name('attendances-details.index');

        $router->get('details/{attendance_details}', [AttendanceUpdateController::class, 'index'])
            ->name('attendance-request.send');

        $router->get('daily-log', [AttendanceDailyLogController::class, 'index'])
            ->name('daily-log.attendances');

        $router->get('request', [AttendanceRequestController::class, 'index'])
            ->name('attendance-requests.index');

        $router->patch('comments/{comment}', [AttendanceCommentController::class, 'update'])
            ->name('attendance-notes.update');

        $router->post('{details}/status/approve', [AttendanceStatusController::class, 'update'])
            ->name('attendance.approve');

        $router->post('{details}/status/reject', [AttendanceStatusController::class, 'update'])
            ->name('attendance.reject');

        $router->post('{details}/status/cancel', [AttendanceStatusController::class, 'update'])
            ->name('attendance.cancel');

        $router->patch('{attendance_details}/request', [AttendanceUpdateController::class, 'request'])
            ->name('attendance-request.send');
        $router->patch('{attendance_details}/correct', [AttendanceUpdateController::class, 'correct'])
            ->name('attendance-request.send');
        $router->patch('{attendance_details}/checkout', [AttendanceUpdateController::class, 'checkout'])
            ->name('attendance-request.send');
    });
});
