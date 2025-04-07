<template>
  <div id="app">
    <h3>Labor Distribution</h3>
    <vue-pivottable-ui
        :data="empData"
        aggregatorName='Count'
        rendererName='Table Heatmap'
        :rows="['provider_name']"
        :cols="['projects_location']"
        :vals="['Total']"
        v-if="$can('view_attendance_settings')"

    >
    </vue-pivottable-ui>
  </div>
</template>


<script>
import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {APP_DASHBOARD} from "../../../../Config/ApiUrl";
import { VuePivottable, VuePivottableUi } from 'vue-pivottable'
import 'vue-pivottable/dist/vue-pivottable.css'


export default {
    name: "LaborSupply",
  components: {
    VuePivottable,
    VuePivottableUi
  },
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
          empData : []
        }
    },
    created() {
        // this.setEmployeeStatisticFilters()
        this.getEmployeeStatisticsData();
    },
    methods: {
        // setEmployeeStatisticFilters(){
        //     if (!!this.$can('view_employment_statuses')){
        //         this.employeeStatisticFilters.push({id: 'by_employment_status', value: this.$t('by_employment_status')})
        //     }
        //     if (!!this.$can('view_designations')){
        //         this.employeeStatisticFilters.push({id: 'by_designation', value: this.$t('by_designation')})
        //     }
        //     if (!!this.$can('view_departments')){
        //         this.employeeStatisticFilters.push({id: 'by_department', value: this.$t('by_department')})
        //     }
        //     if (!!this.$can('view_departments')){
        //         this.employeeStatisticFilters.push({id: 'by_skills', value: this.$t('by_skills')})
        //     }
        //     if (!!this.$can('view_departments')){
        //         this.employeeStatisticFilters.push({id: 'by_projects', value: this.$t('by_projects')})
        //     }
        //
        //     this.employeeStatisticFilterValue = this.employeeStatisticFilters[0]?.id
        //
        // },
        // getFilterValue(item, index) {
        //     this.employeeStatisticFilterValue = item.id;
        //     this.activeEmployeeStatisticsFilterIndex = index;
        //     this.getEmployeeStatisticsData();
        // },
        getEmployeeStatisticsData() {
            this.preloader = true;
            axiosGet(`${APP_DASHBOARD}/labor-supply-statistics`)
                .then(({data}) => {
                    this.employeeStatisticsLabels = Object.keys(data);
                    this.employeeStatisticsDataSet[0].data = Object.values(data);
                    this.employeeStatisticsDataSet[0].borderColor = Object.entries(data).map(color => '#019AFF');
                    this.employeeStatisticsDataSet[0].backgroundColor = Object.entries(data).map(color => '#019AFF');
                    this.preloader = false;
                    this.empData =  Object.values(data);
                })
                .catch((err) => {
                    this.$toastr.e(err.message);
                })
        },

    },
}
</script>
