<?php

namespace App\Http\Controllers\Tenant;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Repositories\Tenant\Employee\EmployeeRepository;
use Illuminate\Support\Facades\Log;

class NavigationController extends Controller
{
    public function dashboard()
    {
        return view('tenant.dashboard');
    }

    public function settings()
    {
        $permission = [
            'general' => authorize_any(['view_settings', 'update_settings']),
            'notification_template' => authorize_any(['view_notification_templates', 'create_notification_templates']),
            'notification' => authorize_any(['view_notification_settings', 'update_notification_settings']),
            'update' => authorize_any(['check_for_updates', 'update_app'])
        ];

        $authorized = array_reduce(array_values($permission), function ($sum, $carry) {
            return $sum + $carry;
        });

        if ($authorized)
            return view('tenant.settings.index', ['permissions' => $permission]);

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function notifications()
    {
        return view('tenant.notification.notifications',[
            'unread' => request()->get('unread')
        ]);
    }

    public function profile()
    {
        return view('tenant.user.profile');
    }

    public function users()
    {
        if (authorize_any(['view_users', 'view_roles'])) {
            return view('tenant.user.user-roles');
        }
        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function departments()
    {
        if (authorize_any(['view_departments'])) {
            return view('tenant.employee.departments');
        }
        throw new GeneralException(trans('default.action_not_allowed'));
    }
    public function workers_providers()
    {
        if (authorize_any(['view_departments'])) {
            return view('tenant.employee.workers_providers');
        }
        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function orgStructure()
    {
        if (authorize_any(['view_departments'])) {
            return view('tenant.employee.organizations');
        }
        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function shifts()
    {
        if (authorize_any(['view_working_shifts'])) {
            return view('tenant.employee.work_shifts');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function projects()
    {
        if (authorize_any(['access_own_projects','view_projects'])) {
            return view('tenant.employee.projects');
        }


        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function contractors()
    {
        if (authorize_any(['access_own_projects','view_contractors'])) {
            return view('tenant.project.contractors');
        }


        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function locations()
    {
        if (authorize_any(['access_own_projects','view_locations'])) {
            return view('tenant.project.locations');
        }


        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function subdivisions()
    {
        if (authorize_any(['access_own_projects','view_subdivisions'])) {
            return view('tenant.project.subdivisions');
        }


        throw new GeneralException(trans('default.action_not_allowed'));
    }
    //boudgeau
    public function project($project_id){
        if (authorize_any(['view_employees'])) {
            return view('tenant.employee.project_details', [
                'project_id' => $project_id,
            ]);
        }
    }

    public function transfer_requests()
    {
        if (authorize_any(['view_designations'])) {
            return view('tenant.transfer_request.transfer_request');
        }


        throw new GeneralException(trans('default.action_not_allowed'));
    }
    public function transfer_request($transfer_request_id){
        if (authorize_any(['view_employees'])) {
            return view('tenant.transfer_request.transfer_request', [
                'transfer_request_id' => $transfer_request_id,
            ]);
        }
    }

    public function leaveSettings()
    {
        if (authorize_any(['view_leave_settings'])) {
            return view('tenant.settings.leave_settings');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function attendanceSettings()
    {
        if (authorize_any(['view_attendance_settings'])) {
            return view('tenant.settings.attendance_settings');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function payrollSettings()
    {
        if (authorize_any(['view_payroll_settings'])) {
            return view('tenant.settings.payroll_settings');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function import()
    {
        if (authorize_any(['import_employees'])) {
            return view('tenant.settings.import');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function payroll()
    {
        if (authorize_any(['view_pay_role'])) {
            return view('tenant.payroll.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function attendances()
    {
        if (authorize_any(['attendances_daily_log'])) {
            return view('tenant.attendance.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function attendancesRequest()
    {
        if (authorize_any(['view_attendance_requests'])) {
            return view('tenant.attendance.attendance_request');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function attendancesDetails()
    {
        if (authorize_any(['view_attendances_details'])) {
            return view('tenant.attendance.attendance_details');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function attendancesSummaries()
    {
        if (authorize_any(['view_attendance_summary'])) {
            return view('tenant.attendance.attendance_summaries', [
                'user' => resolve(EmployeeRepository::class)->getFirstEmployee()
            ]);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function recurringAttendances()
    {
        if (authorize_any(['view_attendance_summary'])) {
            return view('tenant.attendance.recurring_attendances', [
                'user' => resolve(EmployeeRepository::class)->getFirstEmployee()
            ]);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function leaves()
    {
        if (authorize_any(['view_leaves'])) {
            return view('tenant.leave.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function leaveStatus()
    {
        if (authorize_any(['view_leave_status'])) {
            return view('tenant.leave.leave_status');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function leaveRequests()
    {
        $manager_dept = [];

        if (request()->get('access_behavior') == 'own_departments') {
            $manager_dept = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
        }

        if (authorize_any(['view_leave_requests'])) {
            return view('tenant.leave.leave_requests')
                ->with('manager_dept', $manager_dept);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function leaveCalendar()
    {
        $manager_dept = [];

        if (request()->get('access_behavior') == 'own_departments') {
            $manager_dept = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
        }

        if (authorize_any(['view_leave_calendar'])) {
            return view('tenant.leave.leave_calendar', [
                'manager_dept' => $manager_dept
            ]);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function leaveSummaries()
    {
        $manager_dept = [];

        if (request()->get('access_behavior') == 'own_departments') {
            $manager_dept = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
        }

        if (authorize_any(['view_leave_summaries'])) {
            return view('tenant.leave.leave_summaries', [
                'user' => resolve(EmployeeRepository::class)->getFirstEmployee(),
                'manager_dept' => $manager_dept
            ]);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function leavePeriods()
    {
        if (authorize_any(['view_leave_periods'])) {
            return view('tenant.leave.leave_periods');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function leaveTypes()
    {
        if (authorize_any(['view_leave_types'])) {
            return view('tenant.leave.leave_types');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function employees()
    {
        if (authorize_any(['view_employees'])){
            return view('tenant.employee.index');

        }
    }

    public function designations()
    {
        if (authorize_any(['view_designations'])) {
            return view('tenant.employee.designations');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function providers()
    {
        if (authorize_any(['view_designations'])) {
            return view('tenant.employee.providers');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function skills()
    {
        if (authorize_any(['view_designations'])) {
            return view('tenant.employee.skills');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function helmets()
    {
        if (authorize_any(['view_designations'])) {
            return view('tenant.employee.helmets');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function gate_passes()
    {
        if (authorize_any(['view_designations'])) {
            return view('tenant.employee.gate_passes');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }
    public function employmentStatus()
    {
        if (authorize_any(['view_employment_statuses'])) {
            return view('tenant.employee.employment_statuses');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }
    public function employeeAlerts()
    {
        if (authorize_any(['view_employment_statuses'])) {
            return view('tenant.employee.alerts');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }


    public function holidays()
    {
        if (authorize_any(['view_holidays'])) {
            return view('tenant.employee.holiday');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function employee($employee_id)
    {
        $manager_dept = [];

        if (request()->get('access_behavior') == 'own_departments') {
            ['users' => $users, 'departments' => $manager_dept] = resolve(DepartmentRepository::class)
                ->getDepartmentsAndUsers(auth()->id());

            throw_if(
                $employee_id != auth()->id() && !in_array($employee_id, $users),
                new GeneralException(trans('default.action_not_allowed'))
            );
        }

        if (authorize_any(['view_employees']) || auth()->id() == $employee_id) {
            return view('tenant.employee.employee_details', [
                'employee_id' => $employee_id,
                'manager_dept' => $manager_dept
            ]);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }


    public function beneficiaryBadges()
    {
        if (authorize_any(['view_beneficiaries'])) {
            return view('tenant.payroll.beneficiary_badge');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function payrun()
    {
        if (authorize_any(['view_payruns'])) {
            return view('tenant.payroll.payrun');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function payslip()
    {
        if (authorize_any(['view_payslips'])) {
            return view('tenant.payroll.payslip');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function payrollSummery()
    {
        if (authorize_any(['view_payroll_summery'])) {
            return view('tenant.payroll.payroll_summery',[
            'user' => resolve(EmployeeRepository::class)->getFirstEmployee(),
            ]);
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }
}
