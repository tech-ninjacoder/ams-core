<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('recurring_attendance')">
            <app-default-button
                :title="$fieldTitle('add', 'recurring_attendance', true)"
                v-if="$can('create_working_shifts')"
                @click="openModal()"
            />
<!--            <app-default-button-->
<!--                    :title="$t('wizard')"-->
<!--                    v-if="$can('create_working_shifts')"-->
<!--                    @click="openWizardModal()"-->
<!--            />-->
        </app-page-top-section>

        <app-table
            id="recurring-attendance-table"
            :options="options"
            @action="triggerActions"
        />

        <app-recurring-attendance-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />



        <app-employee-to-recurring-attendance
            v-if="isAddEmployeeModalActive"
            v-model="isAddEmployeeModalActive"
            :id="raId"
        />

      <app-confirmation-modal
          v-if="confirmationModalActive"
          icon="trash-2"
          modal-id="app-confirmation-modal"
          @confirmed="confirmed('recurring-attendance-table')"
          @cancelled="cancelled"
      />


    </div>
</template>

<script>

import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";
import ProjectMixin from "../../Mixins/ProjectMixin";
import {PROJECTS, RECURRING_ATTENDANCE} from "../../../Config/ApiUrl";
import coreLibrary from "../../../../core/helpers/CoreLibrary";
import RecurringAttendanceMixin from "../../Mixins/RecurringAttendanceMixin";


export default {
    name: "recurring-attendance",
    extends: coreLibrary,
    mixins: [HelperMixin, RecurringAttendanceMixin],
    data() {
        return {
            isModalActive: false,
            raId: '',
            selectedUrl: '',
            message: '',
            isAddEmployeeModalActive: false,
            isAddManagerModalActive: false,
            isAddCoordinatorModalActive: false,
            isAddGeometryModalActive: false,
            isWizardModalActive: false,
            isAddWorkingShiftModalActive: false,
            isAddGatePassesModalActive: false

        }
    },



    methods: {
        triggerActions(row, action, active) {
          console.log('triggeraction')
            if (action.name === 'edit') {
              console.log('edit')
                this.selectedUrl = `${RECURRING_ATTENDANCE}/${row.id}`;
                this.isModalActive = true;
            } else if (action.name === 'delete') {
              console.log('delete')
                this.delete_url = `${RECURRING_ATTENDANCE}/${row.id}`;
                this.message = action.message;
                this.confirmationModalActive = true;
            }else if (action.name === 'release') {
                this.getAction(row, action, active);
                //
                // this.selectedUrl = `${PROJECTS}/${row.id}`;
                // console.log(this.selectedUrl);
                // this.message = action.message;
                // this.ReleaseconfirmationModalActive = true;
            }else if (action.name === 'add-employee') {
                this.isAddEmployeeModalActive = true;
                this.raId = row.id
            }

        },
        openModal() {
            this.selectedUrl = '';
            this.isModalActive = true;
        },
        openWizardModal(){
            this.isWizardModalActive = true;

        },


    },
    computed: {
        // ownProject() {
        //     return window.project.id === this.projectId;
        // },

    }
}
</script>
