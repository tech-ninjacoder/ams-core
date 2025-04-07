import HelperMixin from "../../../common/Mixin/Global/HelperMixin";
import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import AppFunction from "../../../core/helpers/app/AppFunction";
import {EMPLOYEES, EMPLOYEES_PROFILE} from "../../Config/ApiUrl";
import {
    DepartmentFilterMixin,
    DesignationFilterMixin,
   // WorkersProvidersFilterMixin,
    ProviderFilterMixin,
    HelmetFilterMixin,
    RecurringAttendanceFilterMixin,
    GatePassFilterMixin,
    WorkingShiftFilterMixin,
    RoleFilterMixin,
    SkillFilterMixin,
    ProjectFilterMixin,
    EmploymentStatusFilterMixin
} from "./FilterMixin"

import {axiosDelete, axiosPatch, urlGenerator} from "../../../common/Helper/AxiosHelper";
import {TENANT_USERS} from "../../../common/Config/apiUrl";
import {calenderTime} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins: [HelperMixin, DatatableHelperMixin, DepartmentFilterMixin, DesignationFilterMixin,ProviderFilterMixin,ProjectFilterMixin,HelmetFilterMixin,RecurringAttendanceFilterMixin, WorkingShiftFilterMixin, RoleFilterMixin,SkillFilterMixin, EmploymentStatusFilterMixin, GatePassFilterMixin],
    data() {
        return {
            isModalActive: false,
            employmentStatusModalActive: false,
            isTerminationReasonModalActive: false,
            promptIcon: '',
            promptTitle: '',
            promptMessage: '',
            modalClass: '',
            confirmationModalActive: false,
            promptAction: '',
            tenant: '',
            selectedUrl: '',
            employeeId: '',
            loading: false,
            attendanceModalActive: false,
            leaveModalActive: false,
            gatepassModalActive: false,
            isJobHistoryEditModalActive: false,
            modalAction: '',
            options: {
                orderBy: "null",//orderby
                name: this.$allLabel('employee'),
                url: EMPLOYEES,
                showCount: true,
                showClearFilter: true,
                responsive: true,
                cardViewComponent: 'app-employee-card-view',
                enableRowSelect: true,
                columns: [
                    {
                        title: this.$t('employee_id'),
                        type: 'object',
                        key: 'profile',
                        isVisible: true,
                        modifier: (value, row) => {
                            return value ? value.employee_id : '-';
                        }
                    },
                    {
                        title: this.$t('profile'),
                        type: 'component',
                        componentName: 'app-employee-media-object',
                        key: 'image',
                        isVisible: true,

                        modifier: (value, row) => {
                            return row.icon ? `${AppFunction.getBaseUrl()}/${row.icon.value.split('/').filter(d => d).join('/')}` : '';
                        }
                    },
                    {
                        title: this.$t('project'),
                        type: 'custom-html',
                        // componentName: 'app-employee-project',
                        key: 'projects',
                        isVisible: true,
                        modifier: (projects) => {
                            let projectName = '';
                            projects.forEach((element, index) => {
                                projectName += index > 0 ? ', ' + element.name : element.name;
                            });
                            // projectName = '<span class="badge badge-pill badge-success text-capitalize">'+projectName+'</span>'
                            projectName = projectName ? '<span class="badge badge-pill badge-success text-capitalize">'+projectName+'</span>' : '<span class="badge badge-pill badge-danger text-capitalize">Free</span>';
                            return projectName;
                        }
                    },
                    {
                        title: this.$t('pme_id'),
                        type: 'custom-html',
                        // componentName: 'app-employee-project',
                        key: 'projects',
                        isVisible: true,
                        modifier: (projects) => {
                            let projectId = '';
                            projects.forEach((element, index) => {
                                projectId += index > 0 ? ', ' + element.pme_id : element.pme_id;
                            });
                            // projectName = '<span class="badge badge-pill badge-success text-capitalize">'+projectName+'</span>'
                            projectId = projectId ? '<span class="badge badge-pill badge-success text-capitalize">'+projectId+'</span>' : '<span class="badge badge-pill badge-danger text-capitalize">Free</span>';
                            return projectId;
                        }
                    },
                    {
                        title: this.$t('skill'),
                        type: 'object',
                        key: 'skills',
                        isVisible: true,
                        modifier: (skills) => {
                            let skillName = '';
                            skills.forEach((element, index) => {
                                skillName += index > 0 ? ', ' + element.name : element.name;
                            });
                            skillName = skillName ? skillName : '-';
                            return skillName;
                        }
                    },

                    {
                        title: this.$t('employment_status'),
                        type: 'component',
                        componentName: 'app-employee-status',
                        key: 'employment_status',
                        isVisible: true,
                    },

                    {
                        title: this.$t('department'),
                        type: 'custom-html',
                        key: 'department',
                        isVisible: false,//boudgeau
                        modifier: (department) => {
                            return department ? department.name ? department.name : '-' : '-';
                        }
                    },
                    {
                        title: this.$t('helmet'),
                        type: 'object',
                        key: 'helmet',
                        isVisible: true,
                        modifier: (helmet, row) => {
                            return helmet ? helmet.pme_barcode ? helmet.pme_barcode : '-' : '-';
                        }
                    },
                    {
                        title: this.$t('RA'),
                        type: 'custom-html',
                        key: 'recurring_attendance',
                        isVisible: true,
                        // modifier: (recurring_attendance, row) => {
                        //     return recurring_attendance ? recurring_attendance.id ? recurring_attendance.id : '-' : '-';
                        // },
                        modifier: (recurring_attendance) => {
                            if (!recurring_attendance) {
                                return ''; // Return an empty string if recurring_attendance is null
                            }

                            if (recurring_attendance.id) {
                                return `<span class="badge badge-pill badge-info text-capitalize">RA: ${recurring_attendance.id}</span>`;
                            }

                            return ''; // Return an empty string if the id does not exist
                        }
                    },
                    {
                        title: this.$t('location'),
                        type: 'custom-html',
                        key: 'projects',
                        isVisible: true,
                        modifier: (projects) => {
                            let projectLocation = '';
                            projects.forEach((element, index) => {
                                projectLocation += index > 0 ? ', ' + element.location : element.location;
                            });
                            projectLocation = projectLocation ? projectLocation : '-';
                            return projectLocation;
                        }
                    },
                    {
                        title: this.$t('manager'),
                        type: 'custom-html',
                        key: 'projects',
                        isVisible: true,
                        modifier: (projects, row) => {
                            let projectManagers = [];
                            let m_name = '';

                            projects.forEach((element, index) => {
                                projectManagers += index > 0 ? projectManagers = element.managers : projectManagers = element.managers;
                                element.managers.forEach((element, index) => {
                                    m_name += index > 0 ? element.first_name : element.first_name;
                                })
                            });
                            return m_name;
                        }
                    },
                    {
                        title: this.$t('provider'),
                        type: 'object',
                        key: 'provider',
                        isVisible: true,
                        modifier: (provider, row) => {
                            return provider ? provider.name : '-';
                        }
                    },
                    {
                        title: this.$t('work_shift'),
                        type: 'object',
                        key: 'working_shift',
                        isVisible: false,
                        modifier: (work_shift, row) => {
                            return work_shift ? work_shift.name ? work_shift.name : '-' : '-';
                        }
                    },
                    {
                        title: this.$t('joining_date'),
                        type: 'object',
                        key: 'profile',
                        isVisible: false,
                        modifier: (value, row) => {
                            return value && value.joining_date ? calenderTime(value.joining_date, false) : this.$t('not_yet_joined');
                        }
                    },
                    {
                        title: this.$t('role'),
                        type: 'object',
                        key: 'roles',
                        isVisible: false,
                        modifier: (roles) => {
                            let roleName = '';
                            roles.forEach((element, index) => {
                                roleName += index > 0 ? ', ' + element.name : element.name;
                            });
                            roleName = roleName ? roleName : '-';
                            return roleName;
                        }
                    },

                    // {
                    //     title: this.$t('project'),
                    //     type: 'object',
                    //     key: 'projects',
                    //     isVisible: true,
                    //     modifier: (projects) => {
                    //         let projectName = '';
                    //         projects.forEach((element, index) => {
                    //             projectName = index > 0 ? ', ' + element.name : element.name;
                    //         });
                    //         projectName = projectName ? projectName : '-';
                    //         return projectName;
                    //     }
                    // },
                    {
                        title: this.$t('actions'),
                        type: 'action'
                    }
                ],
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('joining_date'),
                        key: "joining_date",
                        type: "range-picker",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('designation'),
                        type: "multi-select-filter",
                        key: "designations",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_designations')
                    },
                    {
                        title: this.$t('provider'),
                        type: "multi-select-filter",
                        key: "providers",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_designations')
                    },
                    {
                        title: this.$t('helmet'),
                        type: "multi-select-filter",
                        key: "helmets",
                        option: [],
                        listValueField: 'pme_barcode',
                        permission: !!this.$can('view_designations')
                    },
                    // {
                    //     title: this.$t('recurring_attendance'),
                    //     type: "multi-select-filter",
                    //     key: "recurring_attendance",
                    //     option: [],
                    //     listValueField: 'id',
                    //     permission: !!this.$can('view_designations')
                    // },
                    {
                        title: this.$t('gate_passes'),
                        type: "multi-select-filter",
                        key: "gate_passes",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_designations')
                    },
                    {
                        title: this.$t('employment_status'),
                        type: "multi-select-filter",
                        key: "employment_statuses",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_employment_statuses')
                    },
                    {
                        title: this.$t('department'),
                        type: "multi-select-filter",
                        key: "departments",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_departments')
                    },
                    {
                        title: this.$t('work_shift'),
                        type: "multi-select-filter",
                        key: "working_shifts",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_working_shifts')
                    },
                    {
                        title: this.$t('role'),
                        type: "multi-select-filter",
                        key: "roles",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_roles')
                    },
                    {
                        title: this.$t('skill'),
                        type: "multi-select-filter",
                        key: "skills",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_roles')
                    },
                    // {
                    //     title: this.$t('gender'),
                    //     type: "checkbox",
                    //     key: "gender",
                    //     option: [
                    //         {id: 'male', value: this.$t('male')},
                    //         {id: 'female', value: this.$t('female')}
                    //         ],
                    //     permission: !!this.$can('view_employees')
                    // },
                    {
                        title: this.$t('contract_type'),
                        type: "checkbox",
                        key: "contract_type",
                        option: [
                            {id: 'in_house', value: this.$t('In-House')},
                            {id: 'outsourced', value: this.$t('Outsourced')}
                        ],
                        permission: !!this.$can('view_employees')
                    },
                    {
                        title: this.$t('guardian'),
                        type: "checkbox",
                        key: "is_guardian",
                        option: [
                            {id: '0', value: this.$t('no')},
                            {id: '1', value: this.$t('yes')}
                        ],
                        permission: !!this.$can('view_employees')
                    },
                    {//available
                        title: this.$t('Availability'),
                        type: "radio",
                        key: "available",
                        option: [
                            {id: 'free', value: this.$t('free')},
                            {id: 'assigned', value: this.$t('no')},

                        ],
                        permission: !!this.$can('view_employees'),
                        "header": {
                            "title": 'Project Assignment Filter',
                            "description": 'Filter Employee based on the project assigment',
                        },
                    },
                    { //orderby
                        title: this.$t('orderBy'),
                        type: "radio",
                        key: "orderBy",
                        option: [
                            {id: 'projects_pme_id', value: this.$t('projects_pme_id')},
                            {id: 'skills_name', value: this.$t('skills_name')},
                            {id: 'working_shift_name', value: this.$t('working_shift_name')},
                            {id: 'department_name', value: this.$t('department_name')},
                        ],
                        permission: !!this.$can('view_employees')
                        ,

                        "header": {
                            "title": 'Order Employees',
                            "description": 'choose a field to sort employees by',
                        },
                    },
                    {
                        title: this.$t('Active Helmets'),
                        type: "radio",
                        key: "hasHelmet",
                        option: [
                            {id: '1', value: this.$t('yes')},
                            {id: '0', value: this.$t('no')},
                        ],
                        permission: !!this.$can('view_employees')
                        ,

                        "header": {
                            "title": this.$t('get_active_helmets'),
                            "description": this.$t('filter_employees_based_on_helmets'),
                        },
                    },
                    // {
                    //     title: this.$t("salary"),
                    //     type: "range-filter",
                    //     key: "salary",
                    //     minTitle: this.$t("minimum_salary"),
                    //     maxTitle: this.$t("maximum_salary"),
                    //     maxRange: 100,
                    //     minRange: 0
                    // },
                    {
                        title: this.$t("project"),
                        type: "multi-select-filter",
                        key: "projects",
                        option: [],
                        listValueField: 'pme_name',
                        permission: !!this.$can('view_working_shifts')

                    }
                ],
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('view_details'),
                        key: "view_details",
                    },
                    {
                        title: this.$t('edit'),
                        url: EMPLOYEES,
                        key: "edit",
                        modifier: row => {
                            if (row.employment_status && row.employment_status.alias === 'terminated') {
                                return false;
                            }

                            return this.$can('update_employees') && (row.id != window.user.id || this.$isAdmin());
                        },
                    },
                    {
                        title: this.$t('add_attendance'),
                        key: "add_attendance",
                        modifier: (row) => this.$can('update_attendance_status')
                            && row.employment_status?.alias !== 'terminated'
                            && row.status.name !== 'status_inactive'
                    },
                    {
                        title: this.$t('assign_leave'),
                        key: "assign_leave",
                        modifier: (row) => this.$can('assign_leaves')
                            && row.employment_status?.alias !== 'terminated'
                            && row.status.name !== 'status_inactive'
                    },
                    {
                        title: this.$t('cancel_invitation'),
                        key: "cancel_invitation",
                        icon: 'x-circle',
                        modalClass: 'danger',
                        modifier: row => {
                            if (row.status) {
                                return row.status.name === 'status_invited';
                            }
                        },
                    },
                    {
                        title: this.$t('add_salary'),
                        key: 'add_or_edit_salary',
                        modifier: row => this.$can('add_salary') && !row.updated_salary &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('edit_salary'),
                        key: 'add_or_edit_salary',
                        modifier: row => this.$can('edit_salary') && row.updated_salary &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('add_joining_date'),
                        key: 'add_joining_date',
                        modifier: row => this.$can('update_employees') && !row.profile?.joining_date &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('edit_joining_date'),
                        key: 'add_joining_date',
                        modifier: row => this.$can('update_employees') && row.profile?.joining_date &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('assign_gate_pass'), //boudgeau
                        key: 'assign_gate_pass',
                        modifier: row => this.$can('update_employees') && row.profile?.joining_date &&
                            (row.employment_status && row.employment_status.alias !== 'terminated'),
                    },
                    {
                        title: this.$t('terminate'),
                        key: "terminate",
                        icon: 'log-out',
                        modalClass: 'danger',
                        modifier: row => {
                            if (row.employment_status) {
                                return row.status.name !== 'status_invited' &&
                                    row.employment_status.alias !== 'terminated' && row.id != window.user.id;
                            }
                        },
                    },
                    {
                        title: this.$t('rejoining'),
                        key: "rejoining",
                        icon: 'log-in',
                        modalClass: 'primary',
                        modifier: row => {
                            if (row.employment_status) {
                                return row.employment_status.alias === 'terminated';
                            }
                        },
                    },
                    {
                        title: this.$t('remove_from_employee_list'),
                        key: 'remove_from_employee',
                        modifier: row => parseInt(row.is_in_employee) && !this.$isOnlyDepartmentManager() &&
                            (row.id !== window.user.id || this.$isAdmin())
                    }
                ],
                rowLimit: 10,
                paginationType: "pagination",
            },
            selectedEmployees: [],
            isContextMenuOpen: false,
            employee: {},
        }
    },
    watch: {
        isModalActive: function (value) {
            if (!value) {
                this.selectedUrl = '';
            }
        }
    },
    methods: {
        getSelectedRows(data, isSelectedAll) {
            this.selectedEmployees = data;
            this.isContextMenuOpen = data.length;
        },
        triggerActions(row, action, active) {
            this.promptIcon = action.icon;
            this.employeeId = row.id;
            this.modalClass = action.modalClass;

            if (action.key === 'view_details') {
                window.location = urlGenerator(`${EMPLOYEES_PROFILE}/${this.employeeId}/profile`);
            }

            if (action.key === 'edit') {
                this.selectedUrl = `${action.url}/${row.id}`;
                this.isModalActive = true;
            }

            if (action.key === 'cancel_invitation') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_going_to_cancel_an_invitation');
            }

            if (action.key === 'terminate') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_going_to_terminate_an_employee');
            }

            if (action.key === 'rejoining') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_permitting_an_employee_for_re_joining');
            }

            if (action.key === 'remove_from_employee') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_going_to_remove_an_employee_from_list');
            }

            if (action.key === 'assign_leave') {
                this.leaveModalActive = true;
                this.employee = row;
            }

            if (action.key === 'add_attendance') {
                this.attendanceModalActive = true;
                this.employee = row;
            }

            if (action.key === 'add_joining_date') {
                this.isJobHistoryEditModalActive = true;
                this.employee = row;
                this.modalAction = 'joining_date';
            }

            if (action.key === 'assign_gate_pass') { //boudgeau
                this.isJobHistoryEditModalActive = true;
                this.employee = row;
                this.modalAction = 'gate_pass';
            }

            if (action.key === 'add_or_edit_salary') {
                this.isJobHistoryEditModalActive = true;
                this.employee = row;
                this.modalAction = 'salary';
            }
        },
        triggerConfirm() {
            if (this.promptAction === 'cancel_invitation') {
                this.cancelInvitation();
            }
            if (this.promptAction === 'terminate') {
                this.terminate();
            }
            if (this.promptAction === 'rejoining') {
                this.rejoining();
            }
            if (this.promptAction === 'remove_from_employee') {
                this.removeEmployee();
            }
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        },
        cancelInvitation() {
            this.loading = true;
            axiosDelete(`${EMPLOYEES}/${this.employeeId}/cancel-invitation`).then(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
            }).catch(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
            }).finally(() => this.closeConfirmation());
        },
        terminate() {
            this.loading = true;
            axiosPatch(`${EMPLOYEES}/${this.employeeId}/terminate`).then(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
                setTimeout(() => {
                    this.isTerminationReasonModalActive = true;
                })
            }).catch(({data}) => {
                this.confirmationModalActive = false;
                this.toastAndReload(data.message, 'employee-table');
            }).finally(() => this.closeConfirmation());
        },
        rejoining() {
            this.confirmationModalActive = false;
            this.employmentStatusModalActive = true;
        },
        reloadEmployeeTable() {
            this.$hub.$emit('reload-employee-table');
        },
        removeEmployee() {
            this.loading = true;
            axiosPatch(`${TENANT_USERS}/${this.employeeId}/remove-from-employee`)
                .then(({data}) => {
                    this.confirmationModalActive = false;
                    this.toastAndReload(data.message, 'employee-table')
                }).catch(({response}) => {
                this.toastException(response.data)
                this.confirmationModalActive = false;
            }).finally(() => this.closeConfirmation());
        }
    }
}
