<template>
    <div>

        <div class="position-relative" v-if="adminSummaryPermissions">
            <app-overlay-loader v-if="preloader"/>
            <div class="row mb-primary" :class="{'loading-opacity': preloader}">
                <div class="col-sm-6 col-lg-2 mb-4 mb-lg-0" v-if="$can('view_employees')">
                    <app-widget
                        type="app-widget-with-icon"
                        :label="$t('total_employee')"
                        :number="numberFormatter(cardSummaries.total_employee)"
                        icon="user"
                    />
                </div>
                <div class="col-sm-6 col-lg-2 mb-4 mb-lg-0" v-if="$can('view_departments')">
                    <app-widget
                        type="app-widget-with-icon"
                        :label="$t('total_department')"
                        :number="numberFormatter(cardSummaries.total_department)"
                        icon="home"
                    />
                </div>
                <div class="col-sm-6 col-lg-2 mb-4 mb-lg-0" v-if="$can('view_departments')">
                <app-widget
                        type="app-widget-with-icon"
                        :label="$t('total_projects')"
                        :number="numberFormatter(cardSummaries.total_projects)"
                        icon="activity"
                />
                </div>
                <div class="col-sm-6 col-lg-2 mb-4 mb-lg-0" v-if="$can('view_departments')">
                    <app-widget
                            type="app-widget-with-icon"
                            :label="$t('total_helmets')"
                            :number="numberFormatter(cardSummaries.total_helmets)"
                            icon="wifi"
                    />
                </div>
                <div class="col-sm-6 col-lg-2 mb-4 mb-lg-0" v-if="$can('view_departments')">
                    <app-widget
                            type="app-widget-with-icon"
                            :label="$t('total_gate_passes')"
                            :number="numberFormatter(cardSummaries.total_gate_passes)"
                            icon="award"
                    />
                </div>
<!--                <div class="col-sm-6 col-lg-3 mb-4 mb-sm-0" v-if="$can('view_all_leaves')">-->
<!--                    <app-widget-->
<!--                        type="app-widget-with-icon"-->
<!--                        :label="$t('total_leave_request')"-->
<!--                        :number="numberFormatter(cardSummaries.total_leave_request)"-->
<!--                        icon="edit"-->
<!--                    />-->
<!--                </div>-->
                <div class="col-sm-6 col-lg-2" v-if="$can('view_all_leaves')">
                    <app-widget
                        type="app-widget-with-icon"
                        :label="$t('on_leave_today')"
                        :number="numberFormatter(cardSummaries.on_leave_today)"
                        icon="user-x"
                    />
                </div>
            </div>
        </div>
<!--        collapse button-->
        <p>
            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#laboDistribution" aria-expanded="false" aria-controls="laboDistribution">
                Labor Distribution Table
            </button>
            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#projectSupply" aria-expanded="false" aria-controls="projectSupply">
                Project Supply Table
            </button>
        </p>
        <!--        collapse area-->

        <div class="collapse" id="laboDistribution">
            <div class="card card-body">
                <div class="row">
                    <div class="col-12 col-sm-12" v-if="employeeStatisticsPermissions">
                        <app-labor-supply/>
                    </div>
                </div>
            </div>
        </div>
        <!--        collapse area-->
        <div class="collapse" id="projectSupply">
            <div class="card card-body">
                <div class="row">
                    <div class="col-12 col-sm-12" v-if="employeeStatisticsPermissions">
                        <app-project-supply/>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-9 mb-4 mb-md-0" v-if="employeeStatisticsPermissions">
                <app-employee-statistics/>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-3" v-if="attendancePermissions">
                <on-working-today/>
            </div>
        </div>
<!--        <div class="row">-->
<!--            <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-9 mb-4 mb-md-0" v-if="employeeStatisticsPermissions">-->
<!--                <app-project-statistics/>-->
<!--            </div>-->
<!--        </div>-->

    </div>
</template>

<script>
import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {APP_DASHBOARD, CHECK_DEFAULT_WORKING_SHIFT} from "../../../../Config/ApiUrl";
import OnWorkingToday from "./OnWorkingToday";

export default {
    name: "AdminDashboard",
    components: {OnWorkingToday},
    data() {
        return {
            numberFormatter,
            preloader: false,
            cardSummaries: {},
        }
    },
    created() {
        this.getSummeryData();
    },
    methods: {
        getSummeryData() {
            this.preloader = true;
            axiosGet(`${APP_DASHBOARD}/summery`).then(({data}) => {
                this.cardSummaries = data;
                this.preloader = false;
            })
        },
    },
    computed: {
        adminSummaryPermissions() {
            return this.$can('view_employees') ||
                this.$can('view_departments') || this.$can('view_all_leaves')
        },
        employeeStatisticsPermissions() {
            return this.$can('view_employment_statuses') ||
                this.$can('view_designations') || this.$can('view_departments')
        },
        attendancePermissions() {
            return this.$can('view_all_attendance')
        }
    }
}
</script>

<style scoped>

</style>
