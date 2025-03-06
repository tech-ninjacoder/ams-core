import Vue from 'vue'
import UrlMixin from "./Config/UrlMixin";
import TenantMixin from './Config/TenantMixin'
Vue.mixin(UrlMixin);
Vue.mixin(TenantMixin);

//Support
Vue.component('app-form-group-selectable', require('./Components/Helper/AppFromGroupSelectable').default)
Vue.component('app-note-editor', require('./Components/Helper/AppNoteEditor').default)
Vue.component('app-month-calendar', require('./Components/Helper/MonthCalendar').default);
Vue.component('app-period-calendar', require('./Components/Helper/PeriodCalendar').default)

// Dashboard
Vue.component('app-dashboard', require('./Components/View/Dashboard/Dashboard').default)
Vue.component('app-admin-dashboard', require('./Components/View/Dashboard/components/AdminDashboard').default)
Vue.component('app-working-today', require('./Components/View/Dashboard/components/OnWorkingToday').default)
Vue.component('app-employee-statistics', require('./Components/View/Dashboard/components/EmployeeStatistics').default)
Vue.component('app-project-statistics', require('./Components/View/Dashboard/components/ProjectsProgress').default)
Vue.component('app-labor-supply', require('./Components/View/Dashboard/components/LaborSupply').default)
Vue.component('app-project-supply', require('./Components/View/Dashboard/components/ProjectSupply').default)



Vue.component('app-employee-dashboard', require('./Components/View/Dashboard/components/EmployeeDashboard').default)

//Auth
Vue.component('app-password-reset', require('../common/Components/Auth/Password/RequestResetPassword').default)
Vue.component('app-reset-password', require('../common/Components/Auth/Password/ResetPassword').default)

//Department
Vue.component('app-departments', require('./Components/View/Employee/Department/Departments').default)
Vue.component('app-department-modal', require('./Components/View/Employee/Department/DepartmentCreateEditModal').default)
Vue.component('app-employee-move-modal', require('./Components/View/Employee/Department/EmployeeMoveModal').default)
Vue.component('app-organization-structure', require('./Components/View/Employee/Department/OrganizationStructure').default)

//Workers Providers
//Vue.component('app-workers-providers',require('./Components/View/Employee/Workers_Providers/Workers_Providers').default)
//Vue.component('app-workers-providers-modal',require('./Components/View/Employee/Workers_Providers/WorkersProvidersCreateEditModal').default)

//providers
Vue.component('app-providers', require('./Components/View/Employee/Provider/Providers').default);
Vue.component('app-provider-modal', require('./Components/View/Employee/Provider/ProviderCreateEditModal').default);

//helmets
Vue.component('app-helmets', require('./Components/View/Employee/Helmet/Helmets').default);
Vue.component('app-helmet-modal', require('./Components/View/Employee/Helmet/HelmetCreateEditModal').default);

//Gate Passes
Vue.component('app-gate-passes', require('./Components/View/Employee/GatePass/GatePasses').default);
Vue.component('app-gate-pass-modal', require('./Components/View/Employee/GatePass/GatePassCreateEditModal').default);

//Employee Alert
Vue.component('app-alerts', require('./Components/View/Employee/Components/Alerts/Alerts').default);


//skills
Vue.component('app-skills', require('./Components/View/Employee/Skill/skills').default);
Vue.component('app-skill-modal', require('./Components/View/Employee/Skill/SkillCreateEditModal').default);

//Settings
Vue.component('app-tenant-settings-layout', require('./Components/View/Setting/TenantSettingLayout').default)
Vue.component('app-tenant-general-settings', require('./Components/View/Setting/Component/TenantGeneralSetting').default)
Vue.component('app-tenant-notification-settings', require('./Components/View/Setting/Component/TenantNotificationSetting').default)
Vue.component('app-attendance-settings', require('./Components/View/Setting/AttendanceSettingLayout').default)
Vue.component('app-attendance-preference-settings', require('./Components/View/Setting/Component/AttendancePreferenceSettings').default)
Vue.component('app-attendance-definition-settings', require('./Components/View/Setting/Component/AttendanceDefinitionSettings').default)
Vue.component('app-attendance-recurring-settings', require('./Components/View/Setting/Component/AttendanceRecurringSettings').default)

Vue.component('app-tenant-manager', require('./Components/View/TenantManager/TenantManager').default)
Vue.component('app-punch-in', require('./Components/View/Attendance/Component/AppPunchInOut').default)
Vue.component('app-leave-settings-layout', require('./Components/View/Setting/LeaveSettingLayout').default)
Vue.component('app-payroll-settings-layout', require('./Components/View/Setting/PayrollSettingLayout').default)

//Employment Statuses
Vue.component('app-employment-statuses', require('./Components/View/Employee/EmploymentStatus/EmploymentStatuses').default)
Vue.component('app-employment-status-create-edit-modal', require('./Components/View/Employee/EmploymentStatus/EmploymentStatusCreateEditModal').default)


//Employee designation
Vue.component('app-designations', require('./Components/View/Employee/Designation/Designations').default);
Vue.component('app-designation-modal', require('./Components/View/Employee/Designation/DesignationCreateEditModal').default);


//Employee transfer Request
Vue.component('app-transfer-request', require('./Components/View/TransferRequest/TransferRequests').default);
Vue.component('app-transfer-request-modal', require('./Components/View/TransferRequest/TransferRequestCreateEditModal').default);
Vue.component('app-transfer-request-status-modal', require('./Components/View/TransferRequest/component/TransfertRequestChangeStatusModal').default);

//Working Shift
Vue.component('app-working-shift', require('./Components/View/Adminstration/WorkingShift/WorkingShift').default);
Vue.component('app-working-shift-modal', require('./Components/View/Adminstration/WorkingShift/WorkingShiftCreateEditModal').default);
Vue.component('app-employee-to-work-shift', require('./Components/View/Adminstration/WorkingShift/AddEmployeeToWorkShift').default);

//Project
Vue.component('app-project', require('./Components/View/Project/Project').default);
Vue.component('app-project-modal', require('./Components/View/Project/ProjectCreateEditModal').default);
Vue.component('app-project-activity', require('./Components/View/Project/ProjectActivity').default)
Vue.component('app-project-active-employees', require('./Components/View/Project/ProjectActiveEmployees').default)
Vue.component('app-project-visits-employees', require('./Components/View/Project/ProjectVisitsEmployees').default)
Vue.component('app-contractor', require('./Components/View/Project/ProjectContractor').default);
Vue.component('app-contractor-modal', require('./Components/View/Project/ContractorCreateEditModal').default);
Vue.component('app-locations', require('./Components/View/Project/ProjectLocation').default);
Vue.component('app-location-modal', require('./Components/View/Project/LocationCreateEditModal').default);
Vue.component('app-subdivisions', require('./Components/View/Project/ProjectSubdivision').default);
Vue.component('app-subdivision-modal', require('./Components/View/Project/SubdivisionCreateEditModal').default);






Vue.component('app-project-object', require('./Components/View/Project/Components/ProjectObject').default);
Vue.component('app-project-details', require('./Components/View/Project/ProjectDetails').default);

Vue.component('app-wizard-project-modal', require('./Components/View/Project/ProjectWizard').default);
Vue.component('form-wizard-basic-info', require('./Components/View/Project/BasicInfo').default);
Vue.component('form-wizard-contact-info', require('./Components/View/Project/ContactInfo').default);
// Vue.component('form-wizard-others', require('./Components/View/Project/Others').default);


Vue.component('app-employee-to-project', require('./Components/View/Project/AddEmployeeToProject').default);
Vue.component('app-manager-to-project', require('./Components/View/Project/AddManagerToProject').default);
Vue.component('app-working-shift-to-project', require('./Components/View/Project/AddWorkingShiftToProject').default);
Vue.component('app-gate-passe-to-project', require('./Components/View/Project/AddGatePassToProject').default);


Vue.component('app-coordinator-to-project', require('./Components/View/Project/AddCoordinatorToProject').default);

Vue.component('app-geometry-to-project', require('./Components/View/Project/AddGeometryToProject').default);
Vue.component('app-project-map', require('./Components/View/Project/ProjectMap').default);



//Holiday
Vue.component('app-holiday', require('./Components/View/Adminstration/Holiday/Holiday').default);
Vue.component('app-holiday-modal', require('./Components/View/Adminstration/Holiday/HolidayCreateEditModal').default);
Vue.component('app-holiday-calendar', require('./Components/View/Adminstration/Holiday/HolidayCalendar').default);

//Employee
Vue.component('app-employee-card-view', require('./Components/View/Employee/EmployeeCardView').default);
Vue.component('app-employee-preview-card', require('./Components/View/Employee/Components/EmployeePreviewCard').default);
Vue.component('app-employee-list', require('./Components/View/Employee/Employees').default);
Vue.component('app-employee-invite', require('./Components/View/Employee/EmployeeInviteEditModal').default);
Vue.component('app-employee-media-object', require('./Components/View/Employee/Components/EmployeeMediaObject').default);
Vue.component('app-employee-status', require('./Components/View/Employee/Components/EmployeeStatus').default);
Vue.component('app-employee-project', require('./Components/View/Employee/Components/EmployeeProject').default);
Vue.component('app-employee-termination-reason-modal', require('./Components/View/Employee/EmployeeTerminationReasonModal').default);

//Employee Details
Vue.component('app-employee-details', require('./Components/View/Employee/Employee').default);
Vue.component('app-employee-personal-details',require('./Components/View/Employee/Components/PersonalDetails/PersonalDetails').default);
Vue.component('app-employee-password-change',require('./Components/View/Employee/Components/ChangePassword/ChangePassword').default);
Vue.component('app-employee-address-details',require('./Components/View/Employee/Components/AddressDetails/AddressDetails').default);
Vue.component('app-employee-emergency-contact',require('./Components/View/Employee/Components/EmergencyContact/EmergencyContacts').default);
Vue.component('app-employee-social-link',require('./Components/View/Employee/Components/SocialLinks/SocialLinks').default);
Vue.component('app-employee-job-history',require('./Components/View/Employee/Components/JobHistory/JobHistory').default);
Vue.component('app-employee-salary-reviews',require('./Components/View/Employee/Components/SalaryReviews/SalaryReviews').default);
Vue.component('app-employee-attendance',require('./Components/View/Employee/Components/Attendance/Attendance').default);
Vue.component('app-employee-leave',require('./Components/View/Employee/Components/Leave/Leave').default);
Vue.component('app-employee-address-details-model',require('./Components/View/Employee/Components/AddressDetails/AddressDetailsEditModal').default);
Vue.component('app-employment-status-modal',require('./Components/View/Employee/EmploymentStatusModal').default);
Vue.component('app-employee-emergency-contact-model',require('./Components/View/Employee/Components/EmergencyContact/EmergencyContactEditModal').default);
Vue.component('app-employee-allowance',require('./Components/View/Employee/Components/Allowance/Allowance').default);
Vue.component('app-allowance-update-modal',require('./Components/View/Employee/Components/Allowance/AllowanceUpdateModal').default);
Vue.component('app-employee-payslip',require('./Components/View/Employee/Components/Payrun/EmployeePayslip').default);


//Attendance
Vue.component('app-attendance-create-edit-modal', require('./Components/View/Attendance/Component/AttendanceCreateEditModal').default);
Vue.component('app-attendance-download-modal', require('./Components/View/Attendance/Component/AttendanceDownloadModal').default);
Vue.component('app-absence-download-modal', require('./Components/View/Attendance/Component/AbsenceDownloadModal').default);

Vue.component('app-recurring-attendance', require('./Components/View/Attendance/RecurringAttendance').default);
Vue.component('app-recurring-attendance-modal', require('./Components/View/Attendance/RecurringAttendanceCreateEditModal').default);
Vue.component('app-employee-to-recurring-attendance', require('./Components/View/Attendance/AddEmployeeToRecurringAttendance').default);


Vue.component('app-attendance-details', require('./Components/View/Attendance/AttendanceDetails').default);
Vue.component('app-attendance-summery', require('./Components/View/Attendance/AttendanceSummaries').default);
Vue.component('app-attendance-request', require('./Components/View/Attendance/AttendanceRequests').default);
Vue.component('app-attendance-employee-media-object', require('./Components/View/Attendance/Component/AttendanceRequest/MediaObject').default);
Vue.component('app-attendance-expandable-column', require('./Components/View/Attendance/Component/AttendanceRequest/ExpandableColumn').default);
Vue.component('app-attendance-request-type', require('./Components/View/Attendance/Component/AttendanceRequest/RequestType').default)
Vue.component('app-daily-log', require('./Components/View/Attendance/AttendanceDailyLogs').default);
Vue.component('app-punch-in-date-time', require('./Components/View/Attendance/Component/PunchInDateTime').default);
Vue.component('app-punch-out-date-time', require('./Components/View/Attendance/Component/PunchOutDateTime').default);
Vue.component('app-attendance-type', require('./Components/View/Attendance/Component/AttendanceRequest/AttendanceType').default);
Vue.component('app-attendance-edit-request-modal', require('./Components/View/Attendance/Component/AttendanceEditRequestModal').default);
Vue.component('app-attendance-correct-request-modal', require('./Components/View/Attendance/Component/AttendanceCorrectRequestModal').default);

Vue.component('app-attendance-checkout-request-modal', require('./Components/View/Attendance/Component/AttendanceCheckoutRequestModal').default);
Vue.component('app-attendance-log-modal', require('./Components/View/Attendance/Component/AttendanceLogModal').default)
Vue.component('app-attendance-summary-table', require('./Components/View/Attendance/Component/AttendanceSummaryTable').default)
Vue.component('app-attendance-projects', require('./Components/View/Attendance/Component/AttendanceProjects').default);


//Leave
Vue.component('app-leave-status', require('./Components/View/Leave/LeaveStatus').default);
Vue.component('app-leave-create-edit-modal', require('./Components/View/Leave/Components/LeaveCreateEditModal').default);
Vue.component('app-leave-response-log-modal', require('./Components/View/Leave/Components/ResponseLogModal').default);
Vue.component('app-leave-types', require('./Components/View/Leave/LeaveTypes').default);
Vue.component('app-leave-type-create-edit', require('./Components/View/Leave/Components/LeaveTypeCreateEditModal').default);
Vue.component('app-leave-periods', require('./Components/View/Leave/LeavePeriods').default);
Vue.component('app-leave-period-create-edit', require('./Components/View/Leave/Components/LeavePeriodCreateEditModal').default);
Vue.component('app-leave-requests', require('./Components/View/Leave/LeaveRequests').default);
Vue.component('app-leave-calendar', require('./Components/View/Leave/LeaveCalendar').default);
Vue.component('app-leave-summary', require('./Components/View/Leave/LeaveSummaries').default);
Vue.component('app-leave-date-time',require('./Components/View/Leave/Components/LeaveDateAndTime').default);
Vue.component('app-attachments-column',require('./Components/View/Leave/Components/AttachmentsColumn').default);
Vue.component('app-activity-column',require('./Components/View/Leave/Components/LeaveActivity').default);
Vue.component('app-leave-allowance-policy', require('./Components/View/Setting/Component/LeaveAllowancePolicy').default);
Vue.component('app-leave-approval-setting', require('./Components/View/Setting/Component/LeaveApprovalSetting').default);

//Leave type
Vue.component('app-leave-types-setting', require('./Components/View/Leave/LeaveTypes').default)
Vue.component('app-leave-allow-earning-button', require('./Components/View/Leave/Components/AllowEarningToggleButton').default)

//Leave period
Vue.component('app-leave-periods', require('./Components/View/Leave/LeavePeriods').default)
Vue.component('app-leave-period-create-edit', require('./Components/View/Leave/Components/LeavePeriodCreateEditModal').default)

//ImportDatabase
Vue.component('app-import-database', require('./Components/View/Setting/Import/ImportDatabase').default);
Vue.component('app-import-loading-modal', require('./Components/View/Setting/Import/ImportLoadingModal').default);

//update
Vue.component('app-update', require('./Components/View/Setting/Update/Update').default)
Vue.component('app-update-confirmation-modal', require('./Components/View/Setting/Update/UpdateConfirmationModal').default)

//Payroll
Vue.component('app-default-payrun-setting', require('./Components/View/Setting/Component/Payroll/DefaultPayrunSetting').default)
Vue.component('app-payrun-audience-setting', require('./Components/View/Setting/Component/Payroll/PayrunAudienceSetting').default)
Vue.component('app-badge-value-setting', require('./Components/View/Setting/Component/Payroll/BadgeValueSetting').default)
Vue.component('app-beneficiary-badges', require('./Components/View/Payroll/BeneficiaryBadge').default)
Vue.component('app-beneficiary-badges-create-edit-modal', require('./Components/View/Payroll/BeneficiaryBadgeCreateEditModal').default)
Vue.component('app-beneficiary-status-toggle-button', require('./Components/View/Payroll/Components/BeneficiaryStatusToggleButton').default)
Vue.component('app-payrun', require('./Components/View/Payroll/Payrun').default)
Vue.component('app-manual-payrun', require('./Components/View/Payroll/ManualPayrun').default)
Vue.component('app-audience-wizard', require('./Components/View/Payroll/Components/AudienceWizard').default)
Vue.component('app-payrun-period-wizard', require('./Components/View/Payroll/Components/PayrunPeriodWizard').default)
Vue.component('app-beneficiary-badge-wizard', require('./Components/View/Payroll/Components/BeneficiaryBadgeWizard').default)
Vue.component('app-manual-payrun-note-wizard', require('./Components/View/Payroll/Components/ManualPayrunNoteWizard').default)
Vue.component('app-payslip-setting', require('./Components/View/Setting/Component/Payroll/PayslipSetting').default)

//Payrun
Vue.component('app-employee-payrun-and-badge', require('./Components/View/Employee/Components/Payrun/PayrunAndBadge').default)
Vue.component('app-employee-payrun-period-modal', require('./Components/View/Employee/Components/Payrun/Components/EmployeePayrunPeriodModal').default)
Vue.component('app-employee-beneficiary-modal', require('./Components/View/Employee/Components/Payrun/Components/EmployeeBeneficiaryModal').default)

//Payslip
Vue.component('app-payslip', require('./Components/View/Payroll/Payslip').default)
Vue.component('app-payslip-view-modal', require('./Components/View/Payroll/Components/PayslipViewModal').default)
Vue.component('app-payslip-edit-modal', require('./Components/View/Payroll/Components/PayslipEditModal').default)
Vue.component('app-payslip-conflict-modal', require('./Components/View/Payroll/Components/PayslipConflictModal').default)
Vue.component('app-payrun-conflict-modal', require('./Components/View/Payroll/Components/PayrunConflictModal').default)

//Payroll Summery
Vue.component('app-payroll-summery', require('./Components/View/Payroll/PayrollSummery').default)


//Import
Vue.component('app-import-employees', require('./Components/View/Setting/Import/ImportEmployees').default);
Vue.component('app-import-layout', require('./Components/View/Setting/ImportLayout').default);
