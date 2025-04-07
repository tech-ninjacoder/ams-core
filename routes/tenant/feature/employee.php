<?php

use App\Http\Controllers\Tenant\Employee\DepartmentController;
use App\Http\Controllers\Tenant\Employee\DepartmentEmployeeController;
use App\Http\Controllers\Tenant\Employee\DepartmentStatusController;
use App\Http\Controllers\Tenant\Employee\EmployeeAddressController;
use App\Http\Controllers\Tenant\Employee\EmployeeAlertController;
use App\Http\Controllers\Tenant\Employee\EmployeeContactController;
use App\Http\Controllers\Tenant\Employee\EmployeeController;
use App\Http\Controllers\Tenant\Employee\EmployeeExportController;
use App\Http\Controllers\Tenant\Employee\EmployeeInviteController;
use App\Http\Controllers\Tenant\Employee\EmployeeJobHistoryController;
use App\Http\Controllers\Tenant\Employee\EmployeePayrunController;
use App\Http\Controllers\Tenant\Employee\EmployeeSalaryController;
use App\Http\Controllers\Tenant\Employee\EmployeeSocialLinkController;
use App\Http\Controllers\Tenant\Employee\EmployeeUpdateController;
use App\Http\Controllers\Tenant\Employee\EmploymentStatusController;
use App\Http\Controllers\Tenant\Employee\DesignationController;
use App\Http\Controllers\Tenant\Employee\GatePassAPIController;
use App\Http\Controllers\Tenant\Employee\GatePassController;
use App\Http\Controllers\Tenant\Employee\HelmetAPIController;
use App\Http\Controllers\Tenant\Employee\HelmetController;
use App\Http\Controllers\Tenant\Employee\OrganizationStructureController;
use App\Http\Controllers\Tenant\Employee\ProviderController;
use App\Http\Controllers\Tenant\Employee\SkillController;
use App\Http\Controllers\Tenant\Employee\WorkersProvidersControllers;
use App\Http\Controllers\Tenant\Holiday\HolidayController;
use App\Http\Controllers\Tenant\Employee\EmployeeEmploymentStatusController;
use App\Http\Controllers\Tenant\Employee\EmployeeProfileController;
use App\Http\Controllers\Tenant\Project\ProjectAPIController;
use App\Http\Controllers\Tenant\Request\TransferRequestController;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\WorkersProviders;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Artisan;

Route::group(['prefix' => 'app', 'middleware' => ['check_behavior', 'check_project_behavior']], function (Router $router) {
    $router->post('departments/move-employees', [DepartmentEmployeeController::class, 'update'])
        ->name('department-employees.move');

    $router->get('departments/{department}/employees', [DepartmentEmployeeController::class, 'getEmployees'])
        ->name('department-user.index');

    $router->post('departments/{department}/update-status', [DepartmentStatusController::class, 'update'])
        ->name('department-status.update');

    $router->get('organization-structure', [OrganizationStructureController::class, 'index'])
        ->name('organization-chart.index');

    $router->apiResource('departments', DepartmentController::class)
        ->middleware(['can_access:view_departments', 'check_behavior']);
    $router->apiResource('workers_providers', WorkersProvidersControllers::class);  //boudgeau

    $router->apiResource('employment-statuses', EmploymentStatusController::class);

    $router->apiResource('designations', DesignationController::class);

    $router->apiResource('providers', ProviderController::class); //boudgeau
    $router->apiResource('helmets', HelmetController::class); //boudgeau
    $router->apiResource('gate_passes', GatePassController::class); //boudgeau


    $router->apiResource('skills', SkillController::class); //boudgeau

    $router->get('release-helmet/{helmet}', [HelmetAPIController::class,'release'])->name('release_helmet'); //boudgeau
    $router->apiResource('alerts', EmployeeAlertController::class); //boudgeau
    $router->get('release-gate-pass/{gate_pass}', [GatePassAPIController::class,'release'])->name('release_gate_pass'); //boudgeau
    $router->get('release-parent-project/{project}',[ProjectAPIController::class,'release_parent'])->name('releae_project_parent');//boudgeau





    $router->apiResource('holidays', HolidayController::class)
        ->middleware(['can_access:view_all_departments_holidays', 'check_behavior']);

    $router->group(['prefix' => 'employees/{employee}'], function (Router $router) {
        $router->patch('terminate', [EmployeeEmploymentStatusController::class, 'terminate'])
            ->name('employees.terminate')
            ->middleware('check_behavior');

        $router->patch('rejoin', [EmployeeEmploymentStatusController::class, 'rejoin'])
            ->name('employees.rejoin')
            ->middleware('check_behavior');

        $router->patch('update-status/{status}', [EmployeeEmploymentStatusController::class, 'update'])
            ->name('employees.update-status');

        $router->delete('cancel-invitation', [EmployeeInviteController::class, 'cancel'])
            ->name('invitation.cancel-employee');

        $router->patch('addresses', [EmployeeAddressController::class, 'update'])
            ->name('employee-address.update');

        $router->delete('addresses/{type}', [EmployeeAddressController::class, 'delete'])
            ->name('employee-address.delete');

        $router->get('job-history', [EmployeeJobHistoryController::class, 'index'])
            ->name('job-history.index');

        $router->patch('{type}/update', [EmployeeUpdateController::class, 'update'])
            ->name('employee-job-history.update');

        $router->get('salaries', [EmployeeSalaryController::class, 'index'])
            ->name('salary.index');

        $router->get('payrun-setting/restore', [EmployeePayrunController::class, 'restore'])
            ->name('employee-payrun.restore');

        $router->post('payrun-setting/update-payrun', [EmployeePayrunController::class, 'updatePayrun'])
            ->name('employee-payrun.update');

        $router->post('payrun-setting/update-beneficiary', [EmployeePayrunController::class, 'updateBeneficiary'])
            ->name('employee-beneficiary.update');

        $router->apiResource('emergency-contacts', EmployeeContactController::class, [
            'names' => [
                'store' => 'employee-emergency-contacts.store',
                'destroy' => 'employee-emergency-contacts.destroy',
                'update' => 'employee-emergency-contacts.update',
                'show' => 'employee-emergency-contacts.show'
            ]
        ])->except('index');
    });

    $router->post('employees/invite', [EmployeeInviteController::class, 'invite'])
        ->name('employees.invite')
        ->middleware('check_behavior');

    $router->apiResource('employees', EmployeeController::class)
        ->except('store', 'show', 'index')
        ->middleware(['employee_access', 'can_access:view_employees', 'check_behavior']);

    $router->get('employees/data/export', [EmployeeExportController::class, 'export'])
        ->name('employees.export');

    $router->get('employees/distribution/today', [EmployeeExportController::class, 'print_distribution'])
        ->name('employees.distribution.today');

    $router->post('employees/{type}/update',[EmployeeProfileController::class,'updateEmployees'])
        ->name('employees.update');

});
