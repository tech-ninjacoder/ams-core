<template>

    <div class="card card-with-shadow border-0 h-100 min-height-350">
        <div class="card-header bg-transparent p-primary d-flex justify-content-between align-items-center">
            <h5 class="card-title text-capitalize mb-0">{{ $t('employee_statistics') }}</h5>
            <ul class="nav tab-filter-menu justify-content-flex-end">
                <li class="nav-item"
                    v-for="(item, index) in employeeStatisticFilters"
                    :key="index">
                    <a href="#"
                       class="nav-link py-0"
                       :class="{'active': index === activeEmployeeStatisticsFilterIndex}"
                       @click.prevent="getFilterValue(item, index)">
                        {{ item.value }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body position-relative" :class="{'loading-opacity': preloader}">
            <app-overlay-loader v-if="preloader"/>
            <app-chart
                v-else
                type="horizontal-line-chart"
                :height="380"
                :maximum-range="20"
                :labels="employeeStatisticsLabels"
                :data-sets="employeeStatisticsDataSet"
            />
        </div>
    </div>


</template>

<script>
import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {APP_DASHBOARD} from "../../../../Config/ApiUrl";

export default {
    name: "employeeStatistics",
    data() {
        return {
            numberFormatter,
            preloader: false,

            activeEmployeeStatisticsFilterIndex: 0,
            employeeStatisticFilterValue: 'by_employment_status',
            employeeStatisticFilters: [],
            employeeStatisticsLabels: [],
            employeeStatisticsDataSet: [
                {
                    borderWidth: 1,
                    barThickness: 25,
                    barPercentage: 0.5,
                    data: [],
                    borderColor: [],
                    backgroundColor: []
                }
            ],
        }
    },
    created() {
        this.setEmployeeStatisticFilters()
        this.getEmployeeStatisticsData();
    },
    methods: {
        setEmployeeStatisticFilters(){
            if (!!this.$can('view_departments')){
                this.employeeStatisticFilters.push({id: 'by_status', value: this.$t('by_status')})
            }
            if (!!this.$can('view_employment_statuses')){
                this.employeeStatisticFilters.push({id: 'by_employment_status', value: this.$t('by_employment_status')})
            }
            if (!!this.$can('view_designations')){
                this.employeeStatisticFilters.push({id: 'by_designation', value: this.$t('by_designation')})
            }
            if (!!this.$can('view_departments')){
                this.employeeStatisticFilters.push({id: 'by_department', value: this.$t('by_department')})
            }
            if (!!this.$can('view_departments')){
                this.employeeStatisticFilters.push({id: 'by_skills', value: this.$t('by_skills')})
            }
            if (!!this.$can('view_departments')){
                this.employeeStatisticFilters.push({id: 'by_projects', value: this.$t('by_projects')})
            }


            this.employeeStatisticFilterValue = this.employeeStatisticFilters[0]?.id

        },
        getFilterValue(item, index) {
            this.employeeStatisticFilterValue = item.id;
            this.activeEmployeeStatisticsFilterIndex = index;
            this.getEmployeeStatisticsData();
        },
        getEmployeeStatisticsData() {
            this.preloader = true;
            axiosGet(`${APP_DASHBOARD}/employee-statistics?key=${this.employeeStatisticFilterValue}`)
                .then(({data}) => {
                    this.employeeStatisticsLabels = Object.keys(data);
                    const values = Object.values(data);

                    // Define an array of colors (you can add more colors if you have more bars)
                    const colors = [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FFCD56', '#4BC0C0', '#9966FF', '#FF6384'
                    ];

                    // Map the colors to the data values
                    this.employeeStatisticsDataSet[0].data = values;
                    this.employeeStatisticsDataSet[0].borderColor = values.map((_, index) => colors[index % colors.length]);
                    this.employeeStatisticsDataSet[0].backgroundColor = values.map((_, index) => colors[index % colors.length]);

                    this.preloader = false;
                })
                .catch((err) => {
                    this.$toastr.e(err.message);
                    this.preloader = false;
                });
        }


    },
}
</script>
