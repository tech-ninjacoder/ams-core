<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('all_employees')" icon="briefcase">
            <app-default-button
                v-if="$can('invite_employees')"
                @click="isModalActive = true"
                :title="$fieldTitle('Add', 'employee', true)"/>
            <app-default-button
                    v-if="$can('invite_employees')"
                    @click="Excelexport()"
                    :title="$t('export')"/>
          <app-default-button
              v-if="$can('invite_employees')"
              @click="PrintLaborDistribution()"
              :title="$t('print_labor_distribution')"/>

        </app-page-top-section>

        <app-table
            id="employee-table"
            :options="options"
            :card-view="false"
            @getRows="getSelectedRows"
            @action="triggerActions"
        />

        <app-employee-invite
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
        />

        <app-confirmation-modal
            :title="promptTitle"
            :message="promptMessage"
            :modal-class="modalClass"
            :icon="promptIcon"
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="triggerConfirm"
            @cancelled="cancelled"
            :loading="loading"
            :self-close="false"
        />

        <app-employment-status-modal
            v-if="employmentStatusModalActive"
            v-model="employmentStatusModalActive"
            :id="employeeId"
        />

        <app-employee-termination-reason-modal
            v-if="isTerminationReasonModalActive"
            v-model="isTerminationReasonModalActive"
            :id="employeeId"
        />

        <employee-context-menu
            v-if="isContextMenuOpen"
            :employees="selectedEmployees"
            @close="isContextMenuOpen = false"
        />

        <app-attendance-create-edit-modal
            v-if="attendanceModalActive"
            v-model="attendanceModalActive"
            :employee="employee"
        />

        <app-leave-create-edit-modal
            v-if="leaveModalActive"
            v-model="leaveModalActive"
            :employee="employee"
        />

        <job-history-edit-modal
            v-if="isJobHistoryEditModalActive"
            v-model="isJobHistoryEditModalActive"
            :modalType="modalAction"
            :employee="employee"
            @reload="reloadEmployeeTable"
        />

    </div>
</template>

<script>
import EmployeeMixin from "../../Mixins/EmployeeMixin";
import EmployeeContextMenu from "./Components/EmployeeContextMenu";
import JobHistoryEditModal from "./Components/JobHistory/components/JobHistoryEditModal";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";
import {FilterMixin} from "../../../../core/components/filter/mixins/FilterMixin";
// import AppTable from "../../../../core/components/datatable/index.vue";
import {EMPLOYEES, LABOR_DISTRIBUTION} from "../../../Config/ApiUrl";
import {TENANT_BASE_URL} from '../../../../common/Config/UrlHelper';
import * as apiUrl from "../../../Config/ApiUrl";



export default {
    components: {EmployeeContextMenu, JobHistoryEditModal},
    mixins: [EmployeeMixin],
    mounted() {
        this.getSalaryRange();
        // this.Excelexport();
    },
    methods:{
        getSalaryRange() {
            axiosGet(this.apiUrl.SALARY_RANGE).then(({data}) => {
                let salaryFilter = this.options.filters.find(item => item.key === 'salary');
                salaryFilter.maxRange = data.max_salary;
                salaryFilter.minRange = data.min_salary < data.max_salary ? data.min_salary : 0;
            })
        },
        Excelexport(){
            // console.log(FilterMixin.props.filterKey);
            console.log(this.$filters_front);
            console.log(EMPLOYEES);
            let url = '?';
            // this.$filters_front.forEach((value, index) => {
            //     console.log(value);
            //     console.log(index);
            //     url = url+'?'+value+'='+index;
            // });
            for (let i = 0; i < this.$filters_front.length; i++) {
                url = url+this.$filters_front[i].key+'='+this.$filters_front[i].value+'&';
            }

            window.open('/'+EMPLOYEES+'/data/export/'+url, '_blank', 'noreferrer');


            // console.log(EMPLOYEES_LIST)

            // let e = TableWithoutWrapper.data.filterValues;

        },
      PrintLaborDistribution(){
          console.log('print distribution');
          window.open('/'+EMPLOYEES+'/distribution/today/', '_blank', 'noreferrer');

      }
    }
}
</script>
