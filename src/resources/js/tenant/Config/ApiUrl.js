import {TENANT_BASE_URL} from '../../common/Config/UrlHelper';

export const TENANT_GENERAL_SETTING_URL = `${TENANT_BASE_URL}general/settings`;
export const TENANTS = `app/user/tenants`;
export const EMPLOYMENT_STATUSES = `${TENANT_BASE_URL}app/employment-statuses`;
export const DESIGNATION = `${TENANT_BASE_URL}app/designations`;
export const TRANSFER_REQUEST = `${TENANT_BASE_URL}employees/transfer/requests`;

export const PROVIDER = `${TENANT_BASE_URL}app/providers`; //boudgeau
export const HELMET = `${TENANT_BASE_URL}app/helmets`; //boudgeau
export const GATE_PASSE = `${TENANT_BASE_URL}app/gate_passes`; //boudgeau
export const ALERTS = `${TENANT_BASE_URL}app/alerts`; //boudgeau
export const CONTRACTOR = `${TENANT_BASE_URL}app/contractors`; //boudgeau
export const LOCATION = `${TENANT_BASE_URL}app/locations`; //boudgeau
export const SUBDIVISION = `${TENANT_BASE_URL}app/subdivisions`; //boudgeau

export const HELMET_SYNC = `${TENANT_BASE_URL}/helmets/sync`; //boudgeau

export const SKILL = `${TENANT_BASE_URL}app/skills`; //boudgeau

export const RELEASE_HELMET = `${TENANT_BASE_URL}app/release-helmet`; //boudgeau
export const RELEASE_PARENT_PROJECT = `${TENANT_BASE_URL}app/release-parent-project`; //boudgeau

export const RELEASE_GATE_PASS = `${TENANT_BASE_URL}app/release-gate-pass`; //boudgeau




export const TEST_MAIL = `${TENANT_BASE_URL}app/test-mail/send`;

// Department
export const DEPARTMENTS = `${TENANT_BASE_URL}app/departments`;
export {TENANT_SELECTABLE_USER as TENANT_SELECTABLE_USER}  from "../../common/Config/apiUrl";
export {TENANT_SELECTABLE_CONTRACTOR as TENANT_SELECTABLE_CONTRACTOR}  from "../../common/Config/apiUrl";
export {TENANT_SELECTABLE_LOCATION as TENANT_SELECTABLE_LOCATION}  from "../../common/Config/apiUrl";
export {TENANT_SELECTABLE_SUBDIVISION as TENANT_SELECTABLE_SUBDIVISION}  from "../../common/Config/apiUrl";

export {TENANT_SELECTABLE_ROLES as TENANT_SELECTABLE_ROLES}  from "../../common/Config/apiUrl";
export const SELECTABLE_DEPARTMENT = `${TENANT_BASE_URL}selectable/departments`;
export const SELECTABLE_ALL_DEPARTMENT = `${TENANT_BASE_URL}selectable/all_departments`;

export const SELECTABLE_MANAGERS =  `${TENANT_BASE_URL}selectable/role/managers`;
export const SELECTABLE_ROLE = `${TENANT_BASE_URL}selectable/roles`;
export const UPDATE_USER_TENANT_LAST_LOGIN = 'app/user/update-tenant';
export const TENANT_REDIRECTION_URL = 'admin/tenant-dashboard-redirect';
export const ORGANIZATION_STRUCTURE = `${TENANT_BASE_URL}/app/organization-structure`;
export const TENANTS_FRONT_END = 'app/tenants/lists';
export const EMPLOYEES_LIST = `${TENANT_BASE_URL}app/employee-list`;
export const SELECTABLE_DESIGNATION = `${TENANT_BASE_URL}selectable/designations`;
export const SELECTABLE_PROVIDER = `${TENANT_BASE_URL}selectable/providers`; //boudgeau
export const SELECTABLE_HELMET = `${TENANT_BASE_URL}selectable/helmets`; //boudgeau
export const SELECTABLE_FREE_HELMET = `${TENANT_BASE_URL}selectable/free-helmets`; //boudgeau
export const SELECTABLE_GUARDIANS = `${TENANT_BASE_URL}selectable/guardians`; //boudgeau

export const SELECTABLE_GATE_PASS = `${TENANT_BASE_URL}selectable/gate_passes`; //boudgeau
export const SELECTABLE_FREE_GATE_PASS = `${TENANT_BASE_URL}selectable/free-gate_passes`; //boudgeau

export const SELECTABLE_SKILL = `${TENANT_BASE_URL}selectable/skills`; //boudgeau


export const SELECTABLE_EMPLOYMENT_STATUS = `${TENANT_BASE_URL}selectable/employment-statuses`;
export const SELECTABLE_PROJECT_STATUS = `${TENANT_BASE_URL}selectable/project-statuses`;
export const SELECTABLE_RECURRING_ATTENDANCE_STATUS = `${TENANT_BASE_URL}selectable/recurring-attendance-statuses`;
export const SELECTABLE_RECURRING_ATTENDANCE = `${TENANT_BASE_URL}selectable/recurring-attendance`; //boudgeau

export const SELECTABLE_ATTENDANCE_STATUS = `${TENANT_BASE_URL}selectable/attendance-statuses`; //



export const SELECTABLE_WORKING_SHIFT = `${TENANT_BASE_URL}selectable/working-shifts`;
export const SELECTABLE_PROJECT = `${TENANT_BASE_URL}selectable/projects`;
export const SELECTABLE_PARENT_PROJECT = `${TENANT_BASE_URL}selectable/projects_parents`;
export const SELECTABLE_ACTIVE_PROJECT = `${TENANT_BASE_URL}selectable/active_projects`;


export const EMPLOYEES = `${TENANT_BASE_URL}app/employees`;
export const EMPLOYEES_PROFILE = `${TENANT_BASE_URL}employees`;
export const EMPLOYEE_INVITE = `${TENANT_BASE_URL}app/employees/invite`;
export const ADMIN_INVITE = `${TENANT_BASE_URL}users/invite-admin`;

export const AUTO_EMPLOYEE_ID = `${TENANT_BASE_URL}/employees/profile/employee-id`;
export const WORKING_SHIFTS = `${TENANT_BASE_URL}app/working-shifts`;
export const PROJECTS = `${TENANT_BASE_URL}app/projects`;
export const PROJECTS_OBJECT = `${TENANT_BASE_URL}app/project`;
export const PROJECT_REPORT = `${TENANT_BASE_URL}app/project/report`;
export const PROJECT_FULL_REPORT = `${TENANT_BASE_URL}app/project/pdf/report`;
export const TENANT_SELECTABLE_COORDINATOR = `${TENANT_BASE_URL}selectable/coordinators`;
export const TENANT_SELECTABLE_MANAGER = `${TENANT_BASE_URL}selectable/managers`;

export const RECURRING_ATTENDANCE = `${TENANT_BASE_URL}app/recurring-attendance`;




export const ATTENDANCES = `${TENANT_BASE_URL}app/attendances`
export const EMPLOYEES_FRONTEND = `${TENANT_BASE_URL}employee/lists`;
export const DEPARTMENT_FRONTEND = `${TENANT_BASE_URL}administration/departments`;

//Workers Providers
export const SELECTABLE_WORKERS_PROVIDERS = `${TENANT_BASE_URL}selectable/workers_providers`; //BOUDGEAU
export const WORKERS_PROVIDERS = `${TENANT_BASE_URL}app/workers_providers`; //boudgeau


// Holiday
export const HOLIDAYS = `${TENANT_BASE_URL}app/holidays`;

// Attendance
export const CHECK_PUNCH_IN = `${TENANT_BASE_URL}employees/check-punch-in`;
export const PUNCH_IN_TIME = `${TENANT_BASE_URL}employees/punch-in-time`;
export const ATTENDANCE_SETTINGS = `${TENANT_BASE_URL}app/settings/attendances`;
export const ATTENDANCE_SETTINGS_RECURRING = `${TENANT_BASE_URL}app/settings/attendances/ra`; //BOUDGEAU

export const ATTENDANCE_DAILY_LOG = `${TENANT_BASE_URL}app/attendances/daily-log`;
export const ATTENDANCE_REQUESTS = `${TENANT_BASE_URL}app/attendances/request`;
export const ATTENDANCE_NOTES = `${TENANT_BASE_URL}app/attendances/comments`;
export const ATTENDANCE_SETTINGS_FRONT_END = `${TENANT_BASE_URL}settings/attendance`;
export const ATTENDANCE_DAILY_LOG_TODAY = `${TENANT_BASE_URL}app/attendance/log/today`;


export const SELECTABLE = `${TENANT_BASE_URL}/selectable`;
export const APP_SELECTABLE = `${TENANT_BASE_URL}/app/selectable`;

// Leave
export const LEAVE_TYPES = `${TENANT_BASE_URL}app/leave-types`;
export const LEAVE_REQUESTS = `${TENANT_BASE_URL}app/leaves/request`;
export const SELECTABLE_LEAVE_TYPES = `${TENANT_BASE_URL}selectable/leave-types`;
export const LEAVE_PERIODS = `${TENANT_BASE_URL}app/leave-periods`;
export const LEAVE_SETTINGS_FRONT_END = `${TENANT_BASE_URL}settings/leave-settings`;
export const LEAVES = `${TENANT_BASE_URL}app/leaves`;
export const LEAVE_NOTES = `${TENANT_BASE_URL}app/leaves/request/comments`;
export const SELECTABLE_LEAVE_PERIODS = `${TENANT_BASE_URL}selectable/leave-periods`;
export const LEAVE_SETTINGS = `${TENANT_BASE_URL}app/settings/leaves`;
export const LEAVE_ASSIGN = `${TENANT_BASE_URL}app/leaves/assign`;
export const SELECTABLE_WORK_SHIFT_USERS = `${TENANT_BASE_URL}selectable/work-shift/users`;
export const SELECTABLE_PROJECT_USERS = `${TENANT_BASE_URL}selectable/project/users`;
export const SELECTABLE_PROJECT_MANAGERS = `${TENANT_BASE_URL}selectable/project/managers`;


export const SELECTABLE_WORK_SHIFT_DEPARTMENTS = `${TENANT_BASE_URL}selectable/work-shift/departments`;
export const SELECTABLE_HOLIDAY_DEPARTMENTS = `${TENANT_BASE_URL}selectable/holiday/departments`;
export const SELECTABLE_PAYRUN_DEPARTMENTS = `${TENANT_BASE_URL}selectable/payrun/departments`;
export const TENANT_SELECTABLE_LEAVE_USERS = `${TENANT_BASE_URL}selectable/leave-settings/users`;
export const TENANT_SELECTABLE_ATTENDANCE_USERS = `${TENANT_BASE_URL}selectable/attendance-settings/users`;
export const TENANT_SELECTABLE_DEPARTMENT_DEPARTMENTS = `${TENANT_BASE_URL}selectable/department/departments`;
export const TENANT_SELECTABLE_DEPARTMENT_USERS = `${TENANT_BASE_URL}selectable/department/users`;
export const TENANT_SELECTABLE_PAYRUN_USER = `${TENANT_BASE_URL}selectable/payrun/users`;

//dashboard
export const APP_DASHBOARD = `${TENANT_BASE_URL}app/dashboard`;

//employee
export const ALL_EMPLOYEE_URL_FRONT_END = `${TENANT_BASE_URL}employee/lists`;
export const ALL_PROJECTS_URL_FRONT_END = `${TENANT_BASE_URL}administration/projects`;

export const SALARY_RANGE = `${TENANT_BASE_URL}app/salary-range`;

//Update
export const APP_UPDATE = `${TENANT_BASE_URL}app/updates`;

//Payroll
export const PAYROLL_SETTINGS = `${TENANT_BASE_URL}app/settings/payrun`;
export const PAYROLL_SETTINGS_FRONTEND = `${TENANT_BASE_URL}settings/payroll-settings`;
export const BENEFICIARY_BADGE = `${TENANT_BASE_URL}app/beneficiaries`;
export const SELECTABLE_BENEFICIARY_BADGE = `${TENANT_BASE_URL}selectable/beneficiaries`;
export const PAYRUN_FRONTEND = `${TENANT_BASE_URL}payroll/payrun`;
export const BENEFICIARY_BADGE_FRONTEND = `${TENANT_BASE_URL}payroll/beneficiary-badges`;
export const PAYSLIP_FRONTEND = `${TENANT_BASE_URL}payroll/payslip`;
export const PAYSLIP = `${TENANT_BASE_URL}app/payslip`;
export const DEFAULT_PAYRUN = `${TENANT_BASE_URL}app/payrun/default`;
export const EMPLOYEES_FOLLOWED_BY_EMPLOYEE_PAYRUN = `${TENANT_BASE_URL}app/payrun/default/employees`;
export const MANUAL_PAYRUN = `${TENANT_BASE_URL}/app/payrun/manual`;
export const PAYRUNS = `${TENANT_BASE_URL}/app/payruns`;
export const PAYRUN = `${TENANT_BASE_URL}/app/payrun`;
export const PAYROLL = `${TENANT_BASE_URL}/app/payroll`;

//import
export const IMPORT = `${TENANT_BASE_URL}/app/import`;
