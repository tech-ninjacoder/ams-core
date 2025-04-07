<template>
    <div class="content-wrapper">
        <div class="d-flex justify-content-lg-end mb-primary">

        <div class="dropdown">
            <button type="button"
                    class="btn btn-success dropdown-toggle"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                {{ $t('download') }}
                <app-icon name="chevron-down"/>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item"
                   @click="downloadReport">
                    Emp. Activity Report
                </a>
                <a :href="this.path" class="dropdown-item">
                    Project Report
                </a>
                <a :href="this.Visitspath" class="dropdown-item">
                    Visit History
                </a>
            </div>
            </div>
        </div>


        <div class="row align-items-center">
            <div class="col-md-12 col-lg-6">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb p-0 d-flex align-items-center mb-primary">
                            <li class="breadcrumb-item page-header d-flex align-items-center">
                                <h4 class="mb-0" >{{ $t('project_details') }}</h4>
<!--                                <h4 class="mb-0" v-else>{{ $t('employee_details') }}</h4>-->
                            </li>
                            <template v-if="this.$can('view_employees') && !ownProfile">
                                <li class="ml-2">|</li>
                                <li>
                                    <a :href="urlGenerator(apiUrl.ALL_PROJECTS_URL_FRONT_END)"
                                       class="btn btn-link text-primary pl-2">{{ $t('back_to_all_projects') }}</a>
                                </li>
                            </template>

                        </ol>

                    </nav>
                </div>
            </div>

            <div class="col-md-12 col-lg-6" v-if="!isActionButtonHide">
                <div class="d-flex justify-content-lg-end mb-primary">
                    <div class="dropdown">
                        <button type="button"
                                v-if="actionButtonReady"
                                class="btn btn-success dropdown-toggle"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            {{ $t('action') }}
                            <app-icon name="chevron-down"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-profile mb-primary">
            <div class="card card-with-shadow py-5 border-0" style="min-height: 220px;">
                <app-overlay-loader v-if="loader"/>
                <div v-else class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-5">
                        <div class="media border-right px-5 pr-xl-5 pl-xl-0 user-header-media align-items-center">
                            <div class="profile-pic-wrapper">
                                <div class="mx-xl-auto">
                                    <div class="image-area d-flex">
                                        <img id="imageResult"
                                             :src="picture_link"
                                             alt=""
                                             class="img-fluid mx-auto my-auto">
                                    </div>
                                </div>
                            </div>
                            <div class="media-body user-info-header">
                                <h4>
                                    <p class="text-muted mb-0">
                                        {{ projectDetails.pme_id }}
                                    </p> {{ projectDetails.name }}
                                </h4>
                                <p class="text-muted mt-2 mb-0" v-if="projectDetails.status_id === 1" >
                                    <app-badge :label="$t('ACTIVE')" className="badge badge-primary badge-lg mr-2"/>

                                </p>
                                <p class="text-muted mt-2 mb-0" >
                                    {{ projectDetails.description }}
                                </p>
                                <p class="text-muted mb-0" >
                                    {{ this.projectDetails.users.length  }} emp. currently assigned to this project
                                    <download-excel
                                            class="btn btn-default"
                                            :data="this.projectDetails.users"
                                            worksheet="My Worksheet"
                                            name="filename.xls"
                                            alt="Download Active Employees"
                                    >
                                        <button type="button" class="btn btn-primary btn-sm" >
                                            <app-icon name="download"/>
                                            </button>
                                    </download-excel>
                                </p>

                                <p class="text-muted" v-if="employee.roles">
                                    {{ rolesNames }}
                                </p>
                                <p class="text-muted" v-if="employee.skills">
                                    {{ skillsNames }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-7">
                        <div
                            class="user-details px-5 px-sm-5 px-md-5 px-lg-0 px-xl-0 mt-5 mt-sm-5 mt-md-0 mt-lg-0 mt-xl-0">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-3">
                                    <div class="border-right custom">
                                        <div class="media mb-4 mb-xl-4">
                                            <div class="align-self-center mr-3">
                                                <app-icon name="map-pin"/>
                                            </div>
                                            <div class="media-body">
                                                <p class="text-muted mb-0">
                                                    {{ $t('Location') }}
                                                </p>
                                                <p class="mb-0" v-if="projectDetails.location">
                                                    {{ projectDetails.location }}
                                                </p>
                                                <p class="mb-0" v-else>
                                                    {{ $t('not_added_yet') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="media mb-4 mb-xl-4">
                                            <div class="align-self-center mr-3">
                                                <app-icon name="clipboard"/>
                                            </div>
                                            <div class="media-body">
                                                <p class="text-muted mb-0">
                                                    {{ $t('Manager') }}
<!--                                                    <button type="button"-->
<!--                                                            class="text-size-12 btn btn-sm btn-outline-primary rounded-pill px-2 padding-y-1 mb-1 ml-2"-->
<!--                                                            @click.prevent="viewWorkShift">-->
<!--                                                        {{ this.$t('view') }}-->
<!--                                                    </button>-->
                                                </p>
                                                <p class="mb-0" v-if="this.projectDetails.managers.length > 0">
                                                    {{ this.projectDetails.managers[0].full_name }}
                                                </p>
                                                <p class="mb-0" v-else>
                                                    {{ $t('not_added_yet') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="media mb-4 mb-xl-4">
                                            <div class="align-self-center mr-3">
                                                <app-icon name="calendar"/>
                                            </div>
                                            <div class="media-body">
                                                <p class="text-muted mb-0">
                                                    {{ $t('gate_pass') }}
                                                </p>
                                                <p class="mb-0" v-if="this.projectDetails.gate_passes.length > 0">
                                                    {{ this.projectDetails.gate_passes[0].name }}
                                                </p>
                                                <p class="mb-0" v-else>
                                                    {{ $t('not_added_yet') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-3">
                                    <div class="media mb-4 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="eye"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('Coordinator') }}
                                            </p>
                                          <p class="mb-0" v-if="this.projectDetails.coordinators.length > 0">
                                            {{ this.projectDetails.coordinators[0].full_name }}
                                          </p>
                                          <p class="mb-0" v-else>
                                            {{ $t('not_added_yet') }}
                                          </p>
                                        </div>
                                    </div>
                                    <div class="media mb-0 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('created_at') }}
                                            </p>
                                            <p class="mb-0" v-if="this.projectDetails.created_at">
                                                {{ formatDateToLocal(this.projectDetails.created_at) }}</p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="media mb-0 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('working_shift') }}
                                            </p>
                                            <p class="mb-0" v-if="this.projectDetails.working_shifts.length > 0">
                                                {{ this.projectDetails.working_shifts[0].name }}</p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-3">
                                    <div class="media mb-4 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('p_start_date') }}
                                            </p>
                                            <p class="mb-0" v-if="this.projectDetails.p_start_date">
                                                {{ formatDateToLocal(this.projectDetails.p_start_date) }}
                                            </p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="media mb-0 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('p_end_date') }}
                                            </p>
                                            <p class="mb-0" v-if="this.projectDetails.p_end_date">
                                                {{ formatDateToLocal(this.projectDetails.p_end_date) }}</p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="media mb-0 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('parent') }}
                                            </p>
                                            <p class="mb-0" v-if="this.projectDetails.parent.length > 0">
                                                {{ this.projectDetails.parent[0].name }}</p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-3">
                                    <div class="media mb-4 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('est_man_hour') }}
                                            </p>
                                            <p class="mb-0" v-if="this.projectDetails.est_man_hour">
                                                {{ this.projectDetails.est_man_hour}}
                                            </p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="media mb-0 mb-xl-4">
                                        <div class="align-self-center mr-3">
                                            <app-icon name="calendar"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-muted mb-0">
                                                {{ $t('act_man_hour') }}
                                            </p>
                                            <p class="mb-0" v-if="this.projectDetails.act_man_hour">
                                                {{ this.projectDetails.act_man_hour }}</p>
                                            <p class="mb-0" v-else>
                                                {{ $t('not_added_yet') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <app-tab :tabs="tabs" :icon="tabIcon"/>

    </div>
</template>

<script>
import {axiosPost, urlGenerator, axiosGet} from '../../../../common/Helper/AxiosHelper';
import {FormMixin} from "../../../../core/mixins/form/FormMixin";
import {formatDateToLocal, isAfterNow} from "../../../../common/Helper/Support/DateTimeHelper";
import {mapState} from "vuex";
import JobHistoryEditModal from "../Employee/Components/JobHistory/components/JobHistoryEditModal";
import {WORKING_SHIFTS, PROJECT_REPORT, PROJECT_FULL_REPORT} from "../../../Config/ApiUrl";
import {formatCurrency, numberFormatter} from "../../../../common/Helper/Support/SettingsHelper";
import ProjectDetailsTabMixin from "../../Mixins/ProjectDetailsTabMixin";


export default {
    name: "ProjectDetails",
    mixins: [FormMixin, ProjectDetailsTabMixin],
    components: {JobHistoryEditModal},
    props: {
        projectId: {},
        //managerDept: {}
    },
    data() {
        return {
            numberFormatter,
            formatCurrency,
            urlGenerator,
            files: [],
            profile_picture: '',
            tabIcon: 'user',
            viewWorkShiftModal: false,
            workShiftUrl: '',
            path: 'pdf/report/'+ this.projectId,
            Visitspath: 'excel/visit-report/'+ this.projectId,
            empcount: '',
            project: {
                users:{},
                managers: {},
                coordinators: {},

            },
            excel:[1,2,3,4,5,6],
            employee: {
                status: {},
                employment_status: {}
            },
            tabs: [],
            formatDateToLocal,
            isAfterNow,
        }
    },
    created() {
        this.tabs = this.ownProject ? this.projectTabs : this.projectDetailsTabs.concat(this.projectTabs);
    },
    mounted() {
        this.$store.dispatch("getProjectDetails", this.projectId);
        // this.$store.dispatch('getEmployeeSocialLinks', this.employeeId)

        $(function () {
            $('#upload').on('change', function () {
                this.readURL();
            });
        });

    },
    methods: {
        readURL() {
            this.files = this.$refs.changeProfileImage.files;
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#imageResult').attr('src', e.target.result);
                };
                let image = reader.readAsDataURL(this.files[0]);

                let formData = new FormData;

                formData.append('profile_picture', this.files[0]);
                formData.append('project_id', this.projectId);

                axiosPost(`admin/auth/users/profile-picture`, formData).then(response => {
                    this.$store.dispatch("getEmployeeDetails", this.employeeId);
                    this.$toastr.s(response.data ? response.data.message : '');
                }).catch(error => {
                    this.$store.dispatch("getEmployeeDetails", this.employeeId);
                    this.$toastr.e(error.response.data ? error.response.data.errors.profile_picture[0] : '');
                });

            }
        },
        reloadPage() {
            this.$store.dispatch("getProjectDetails", this.projectId);
            this.$hub.$emit('employeeDetailsActionHappened', this.employeeId);
        },
        employeeActionHappened() {
            this.$hub.$emit('employeeDetailsActionHappened', this.employeeId);
        },
        viewWorkShift() {
            this.viewWorkShiftModal = true;
            this.workShiftUrl = `${WORKING_SHIFTS}/${this.employee.working_shift.id}`;
        },
        downloadReport(){
            axiosGet(`${PROJECT_REPORT}/${this.projectId}`,{
                responseType: 'blob'
            }).then((response) => {
                // Let's create a link in the document that we'll
                // programmatically 'click'.
                const link = document.createElement('a');

                // Tell the browser to associate the response data to
                // the URL of the link we created above.
                link.href = window.URL.createObjectURL(
                    new Blob([response.data])
                );

                // Tell the browser to download, not render, the file.
                link.setAttribute('download', 'emp_project_assigment_histor.csv');

                // Place the link in the DOM.
                document.body.appendChild(link);

                // Make the magic happen!
                link.click();
            }); // Please catch me!;

        },
        ProjectPDFReport(){
            // axiosGet(`${PROJECT_FULL_REPORT}/${this.projectId}`,{
            //     responseType: 'blob'
            // }).then((response) => {
            //     // Let's create a link in the document that we'll
            //     // programmatically 'click'.
            //     const link = document.createElement('a');
            //
            //     // Tell the browser to associate the response data to
            //     // the URL of the link we created above.
            //     link.href = window.URL.createObjectURL(
            //         new Blob([response.data])
            //     );
            //
            //     // Tell the browser to download, not render, the file.
            //     link.setAttribute('download', 'emp_project_full_report.pdf');
            //
            //     // Place the link in the DOM.
            //     document.body.appendChild(link);
            //
            //     // Make the magic happen!
            //     link.click();
            // }); // Please catch me!;
            //
            return this.projectId;
        }
    },
    computed: {
        ...mapState({
            projectDetails: state => state.projects.project,
            loader: state => state.loading,
            // socialLinkLoader: state => state.employees.socialLinkLoader,
            // socialLinks: state => state.employees.socialLinks
        }),
        actionButtonReady() {
            return !!this.project.full_name
        },
        picture_link() {

            return urlGenerator('/images/pme-project.png')
        },
        attendanceActionTitle() {
            return this.$can('update_attendance_status') ? this.$t('add_attendance') : this.$t('request_attendance');
        },
        leaveActionTitle() {
            return this.$can('assign_leaves') ? this.$t('assign_leave') : this.$t('request_leave');
        },
        ownProfile() {
            return window.user.id === this.employeeId;
        },
        joiningDateTitle() {
            return this.employee.profile?.joining_date ? this.$t('edit_joining_date') : this.$t('add_joining_date');
        },
        salaryTitle() {
            return this.employee.updated_salary ? this.$t('edit_salary') : this.$t('add_salary');
        },
        rolesNames(){
            let title = '';
            if (!this.employee.roles.length) return ''
            this.employee?.roles.map((role) => {
                title += ` ${role.name},`
            })
            return title.replace(/,+$/, '');
        },
        skillsNames(){
            let title = '';
            if (!this.employee.skills.length) return ''
            this.employee?.skills.map((skill) => {
                title += ` ${skill.name},`
            })
            return title.replace(/,+$/, '');
        },
        isActionButtonHide(){
            return !this.attendanceActionPermission &&
                !this.leaveActionPermission &&
                !this.terminatePermission &&
                !this.terminatePermission &&
                !this.joiningPermission &&
                !this.rejoinPermission;
        },
        attendanceActionPermission(){
            return (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated') && this.employee.status.name !== 'status_inactive'
        },
        leaveActionPermission(){
            return (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated') && this.employee.status.name !== 'status_inactive'
        },
        terminatePermission(){
            return this.employee.employment_status && this.employee.status.name !== 'status_invited' && this.employee.employment_status.alias !== 'terminated' && this.$can('terminate_employees')
        },
        salaryPermission(){
            return this.$can('update_employee_job_history') && (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated')
        },
        joiningPermission(){
            return this.$can('update_employee_job_history') && (this.employee.employment_status && this.employee.employment_status.alias !== 'terminated')
        },
        rejoinPermission(){
            return this.employee.employment_status && this.employee.employment_status.alias === 'terminated' && this.$can('rejoin_employees')
        }
    },
    watch: {
        employeeDetails: {
            handler: function (project) {
                this.project = project;
                if (project.name) {
                    let title = document.title.split('-');
                    title[0] = project.name;
                    document.title = title.join(' - ');
                }
            },
            deep: true
        }
    }
}
</script>

<style scoped lang="scss">
.user-social-link {
    svg {
        width: 17px;
        height: 17px;
    }

    i {
        font-size: 17px;
    }
}
</style>
