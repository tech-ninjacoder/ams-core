import {ATTENDANCE_DAILY_LOG, EMPLOYEES, EMPLOYEES_PROFILE} from "../../Config/ApiUrl";
import { convertSecondToHourMinutes} from "../../../common/Helper/Support/DateTimeHelper";
import AttendanceHelperMixin from "./AttendanceHelperMixin";
import {attendanceBehavior, filterLastPendingData} from "../View/Attendance/Helper/Helper";
import {
    DepartmentFilterMixin, GatePassFilterMixin,
    ProjectFilterMixin,
    SkillFilterMixin,
    UserFilterMixin,
    WorkingShiftFilterMixin,
    RecurringAttendanceFilterMixin
} from "./FilterMixin";
import {axiosDelete, axiosGet, axiosPatch, urlGenerator} from "../../../common/Helper/AxiosHelper";
import {TENANT_USERS} from "../../../common/Config/apiUrl";

export default {
    mixins: [AttendanceHelperMixin, DepartmentFilterMixin, WorkingShiftFilterMixin, UserFilterMixin, ProjectFilterMixin, SkillFilterMixin, GatePassFilterMixin, RecurringAttendanceFilterMixin],
    data() {
        return {
            options: {
                name: this.$t('daily_log'),
                url: ATTENDANCE_DAILY_LOG,
                showHeader: true,
                showCount: true,
                showClearFilter: true,
                enableRowSelect: true,

                columns: [
                    {
                        title: this.$t('id'),
                        type: 'object',
                        key: 'user',
                        isVisible: true,
                        modifier: (value, row) => {
                            return value ? value.profile.employee_id : '-';
                        }
                    },{
                        title: this.$t('profile'),
                        type: 'component',
                        componentName: 'app-attendance-employee-media-object',
                        key: 'user',
                    },
                    {
                        title: this.$t('punched_in'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-in-date-time',
                    },
                    {
                        title: this.$t('behavior'),
                        type: 'custom-html',
                        key: 'behavior',
                        modifier: attendanceBehavior
                    },
                    {
                        title: this.$t('punched_out'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-out-date-time',
                    },
                    {
                        title: this.$t('type'),
                        type: 'component',
                        isVisible: true,
                        componentName: 'app-attendance-type',
                        key: 'details',
                    },
                    {
                        title: this.$t('Source'),
                        type: 'custom-html',
                        key: 'source',
                        isVisible: false,

                        modifier: (value, row) => {
                            if(row.source === 2){
                                return 'R' ;
                            }else if(row.source === 0){
                                return 'A' ;

                            }else if(row.source === 1){
                                return 'M' ;

                            }
                        }

                    },
                    {
                        title: this.$t('total_hours'),
                        type: 'custom-html',
                        key: 'details',
                        modifier: (details, attendance) => convertSecondToHourMinutes(this.getTotalWorked(attendance).asSeconds())
                    },
                    // {
                    //     title: this.$t('T2'),
                    //     type: 'custom-html',
                    //     key: 'details',
                    //     modifier: (details, attendance) => convertSecondToHourMinutes(this.getTotalWorked2(attendance).asSeconds())
                    // },\
                    {
                        title: this.$t('lunch_break'),
                        type: 'custom-html',
                        key: 'details',
                        modifier: (details, attendance) => this.getLunchZone(attendance)
                    },
                    {
                        title: this.$t('T2'),
                        type: 'custom-html',
                        key: 'cal_total',
                        modifier: (value, row) => {
                            return row.cal_total ;
                        }

                    },

                    {
                        title: this.$t('time_correction'),
                        type: 'custom-html',
                        key: 'details',
                        modifier: (details, attendance) => this.getCorrection(attendance)
                    },{
                        title: this.$t('assigned_project'),
                        type: 'custom-html',
                        key: 'user',
                        isVisible: true,//boudgeau
                        modifier: (user) => {
                            let projectId = '';
                            if (Array.isArray(user.past_projects) && user.past_projects.length > 0) {
                                projectId = user.past_projects[0].pme_id;
                            }
                            return projectId ? `<span class="badge badge-pill badge-success text-capitalize">${projectId}</span>` : 'N/A';

                            // return projectId;
                        }
                    },{
                        title: this.$t('assigned_workishft'),
                        type: 'custom-html',
                        key: 'user',
                        isVisible: true,//boudgeau
                        modifier: (user) => {
                            let project_working_shifts = 'N/A';
                            // Iterate over the projects array
                            user.past_projects.forEach(project => {
                                // Access the working shifts array of the project
                                const workingShifts = project.working_shifts;

                                // Iterate over the working shifts array
                                workingShifts.forEach(shift => {
                                    // Access the shift's name
                                    project_working_shifts = shift.name;
                                    // Do something with the shift's name, such as display it on the page
                                    // console.log(project_working_shifts);
                                });
                            });

                            // return working_shifts ? `<span class="badge badge-pill badge-success text-capitalize">${working_shifts}</span>` : 'N/A';

                            return project_working_shifts;
                        }
                    },{
                        title: this.$t('assigned_workishft_details'),
                        type: 'custom-html',
                        key: 'user',
                        isVisible: true,//boudgeau
                        modifier: (user, data) => {
                            let project_working_shifts_details = 'N/A';
                            // Iterate over the projects array
                            user.past_projects.forEach(project => {
                                // Access the working shifts array of the project
                                const workingShifts = project.working_shifts;

                                // Iterate over the working shifts array
                                workingShifts.forEach(shift => {
                                    // Access the shift's name
                                    const project_working_shifts = shift.details;
                                    // Do something with the shift's name, such as display it on the page
                                    // console.log(project_working_shifts);
                                    project_working_shifts.forEach(details => {
                                        // Access the shift's name
                                        // const ws_details = details.weekday;
                                        // project_working_shifts_details = details.weekday;
                                        // Assuming the JSON data is stored in a variable named 'data'
                                        const weekdays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                                        const today = new Date(data.in_date).getDay(); // Get the current day of the week
                                        const todayWeekday = weekdays[today]; // Get the corresponding weekday string
                                        // console.log('Today is : '+todayWeekday);

                                        // Compare with the weekday property in the data
                                        if (details.weekday === todayWeekday) {
                                            // Do something if the weekday is equal to today
                                            // Define the start and end times as strings
                                            const startAt = details.start_at;
                                            const endAt = details.end_at;

                                            // Convert the times to Date objects (use a common date for both)
                                            const startDate = new Date("2000-01-01 " + startAt);
                                            const endDate = new Date("2000-01-01 " + endAt);

                                            // Calculate the time difference in milliseconds
                                            const timeDiffMs = endDate - startDate;

                                            // Convert the time difference to hours and minutes
                                            const hoursDiff = Math.floor(timeDiffMs / 1000 / 60 / 60);
                                            const minutesDiff = Math.floor((timeDiffMs / 1000 / 60) % 60);

                                            project_working_shifts_details = hoursDiff+':'+minutesDiff;
                                            // console.log(data.in_date)

                                        } else {
                                            // Do something if the weekday is not equal to today
                                            // project_working_shifts_details = 'N/A'
                                        }
                                        // Do something with the shift's name, such as display it on the page
                                        // console.log(project_working_shifts);
                                    });
                                });
                            });

                            // return working_shifts ? `<span class="badge badge-pill badge-success text-capitalize">${working_shifts}</span>` : 'N/A';

                            return project_working_shifts_details;
                        }
                    },
                    {
                        title: this.$t('pme_id'),
                        type: 'custom-html',
                        key: 'details',
                        modifier: (details, attendance) => this.getProjects(attendance)
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status_id',
                        modifier: (status_id) => {
                            let status = '';
                            if (status_id === 6) {
                                status = '<a class="text-danger">Pending</a>'
                            } else if (status_id === 7) {
                                status = '<a class="text-success">Approved</a>'
                            }else if (status_id === 8) {
                                status = '<a class="text-danger">Rejected</a>'
                            }else if (status_id === 10) {
                                status = '<a class="text-danger">Canceled</a>'
                            }
                            return status;
                        }
                    },
                    {
                        title: this.$t('synch_date'),
                        type: 'custom-html',
                        key: 'synch_date',
                        isVisible: false,//boudgeau
                        modifier: ( synch_date, attendance) => {
                            if (synch_date === null){
                                return '<a class="text-danger">NO</a>'
                            }else {
                                return '<a class="text-success">'+synch_date+'</a>'
                            }
                        }
                    },
                    {
                        title: this.$t('hrms'),
                        type: 'custom-html',
                        key: 'synch_date',
                        isVisible: false,//boudgeau
                        modifier: ( synch_date, attendance) => {
                            if (synch_date === null){
                                return '<a class="text-danger">NO</a>'
                            }else {
                                return '<a class="text-success">YES</a>'
                            }
                        }
                    },
                    // {
                    //     title: this.$t('projectss'),
                    //     type: 'custom-html',
                    //     // componentName: 'app-employee-project',
                    //     key: 'details',
                    //     isVisible: true,
                    //     modifier: (details) => {
                    //         let projectName = '';
                    //         details.forEach((element, index) => {
                    //             projectName += index > 0 ? ', ' + element.name : element.name;
                    //         });
                    //         projectName = '<span class="badge badge-pill badge-success text-capitalize">'+projectName+'</span>'
                    //         projectName = projectName ? '<span class="badge badge-pill badge-success text-capitalize">'+projectName+'</span>' : '<span class="badge badge-pill badge-danger text-capitalize">Free</span>';
                    //         return projectName;
                    //     }
                    // },
                    // {
                    //     title: this.$t('projects'),
                    //     type: 'component',
                    //     key: 'details',
                    //     componentName: 'app-attendance-projects',
                    // },
                    {
                        title: this.$t('entry'),
                        type: 'expandable-column',
                        key: 'details',
                        isVisible: true,
                        componentName: 'app-attendance-expandable-column',
                        modifier: (details) => {
                            return {
                                title: details.length > 1 ? this.$t('multi') : this.$t('single'),
                                expandable: details.length > 1,
                                className: details.length > 1 ? 'warning' : 'success'
                            };
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    }
                ],
                filters: [
                    {
                        title: this.$t('today'),
                        type: "date",
                        key: "date",
                        initValue: new Date(), // not required
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
                        type: "drop-down-filter",
                        key: "working_shifts",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_working_shifts')
                    },
                    {
                        title: this.$t('project'),
                        type: "drop-down-filter",
                        key: "projects",
                        option: [],
                        listValueField: 'pme_id',
                        permission: !!this.$can('view_working_shifts')
                    },
                    {
                        title: this.$t('behavior'),
                        type: "drop-down-filter",
                        key: "behavior",
                        option: [
                            {id: 'early', name: this.$t('early')},
                            {id: 'regular', name: this.$t('regular')},
                            {id: 'late', name: this.$t('late')},
                        ],
                        listValueField: 'name'
                    },
                    {
                        title: this.$t('status'),
                        type: "drop-down-filter",
                        key: "status",
                        option: [
                            {id: 7, name: this.$t('approved')},
                            {id: 6, name: this.$t('pending')},
                        ],
                        listValueField: 'name'
                    },
                    {
                        title: this.$t('type'),
                        type: "drop-down-filter",
                        key: "type",
                        option: [
                            {id: 'auto', name: this.$t('auto')},
                            {id: 'manual', name: this.$t('manual')},
                        ],
                        listValueField: 'name'
                    },
                    {
                        title: this.$t('Employee'),
                        type: "multi-select-filter",
                        key: "users",
                        option: [],
                        listValueField: 'full_name',
                        permission: !!this.$can('view_all_attendance') && !!this.$can('view_users')
                    },
                    {
                        title: this.$t('skill'),
                        type: "multi-select-filter",
                        key: "skills",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_roles')
                    },
                    {
                        title: this.$t('gate_passes'),
                        type: "multi-select-filter",
                        key: "gate_passes",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_designations')
                    },
                    {
                        title: this.$t('recurring_attendance'),
                        type: "multi-select-filter",
                        key: "recurring_attendance",
                        option: [],
                        listValueField: 'id',
                        permission: !!this.$can('view_designations')
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 50,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'dailyLogTableRow',
                        url: ATTENDANCE_DAILY_LOG,
                        name: 'edit',
                        modifier: row =>  (this.$can('update_attendances') ||
                            this.$can('send_attendance_request')) && row?.details?.length < 2
                    },

                    // {
                    //     title: this.$t('Correct'),
                    //     icon: 'edit',
                    //     type: 'dailyLogTableRow',
                    //     url: ATTENDANCE_DAILY_LOG,
                    //     name: 'correct',
                    //     modifier: row =>  (this.$can('update_attendances') ||
                    //         // this.$can('send_attendance_request')) && row?.details?.length < 2
                    //         this.$can('send_attendance_request'))
                    //
                    // },
                    {
                        title: this.$t('Checkout'),
                        icon: 'edit',
                        type: 'dailyLogTableRow',
                        url: ATTENDANCE_DAILY_LOG,
                        name: 'checkout',
                        modifier: row =>  (this.$can('update_attendances') ||
                            this.$can('send_attendance_request')) && row?.details?.length < 2
                    },
                    {
                        title: this.$t('change_log'),
                        icon: 'trash-2',
                        name: 'change-log',
                        type: 'dailyLogTableRow',
                        modifier: row => row?.details?.length < 2
                    },
                    {
                        title: this.$t('cancel'),
                        name: 'cancel',
                        icon: 'trash-2',
                        type: 'dailyLogTableRow',
                        modalClass: 'warning',
                        modalSubtitle: this.$t('you_are_going_to_cancel_a_attendance'),
                        modalIcon: 'slash',
                        // modifier: row => this.$can('update_attendances') &&
                        //     !!this.collection(row.details).first()?.added_by && row?.details?.length < 2 &&
                        //     (row.user_id !== window.user.id || this.$isAdmin())

                    },
                ],

            },
            selectedAttendances: [],
            all_data: false,
            isContextMenuOpen: false,
            attendance: {},


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
            this.selectedAttendances = data;
            this.isContextMenuOpen = data.length;
            if(isSelectedAll === true){
                console.log('all')
                this.all_data = true;
            }
        },
        triggerActions(row, action, active) {
            this.promptIcon = action.icon;
            this.attendanceId = row.id;
            this.modalClass = action.modalClass;
            console.log('rr');
            if (action.key === 'approve') {
                this.confirmationModalActive = true;
                this.promptAction = action.key;
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('you_are_going_to_terminate_an_employee');
            }
        },
        triggerConfirm() {

            if (this.promptAction === 'approve') {
                this.approve();
            }
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        },
        // approve() {
        //     this.loading = true;
        //     axiosPatch(`${ATTENDANCES}/${this.employeeId}/terminate`).then(({data}) => {
        //         this.confirmationModalActive = false;
        //         this.toastAndReload(data.message, 'employee-table');
        //         setTimeout(() => {
        //             this.isTerminationReasonModalActive = true;
        //         })
        //     }).catch(({data}) => {
        //         this.confirmationModalActive = false;
        //         this.toastAndReload(data.message, 'employee-table');
        //     }).finally(() => this.closeConfirmation());
        // },
        // rejoining() {
        //     this.confirmationModalActive = false;
        //     this.employmentStatusModalActive = true;
        // },
        // reloadEmployeeTable() {
        //     this.$hub.$emit('reload-employee-table');
        // },
        // removeEmployee() {
        //     this.loading = true;
        //     axiosPatch(`${TENANT_USERS}/${this.employeeId}/remove-from-employee`)
        //         .then(({data}) => {
        //             this.confirmationModalActive = false;
        //             this.toastAndReload(data.message, 'employee-table')
        //         }).catch(({response}) => {
        //         this.toastException(response.data)
        //         this.confirmationModalActive = false;
        //     }).finally(() => this.closeConfirmation());
        // }
    }
}
