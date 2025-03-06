<template>
    <div>
        <div class="bulk-floating-action-wrapper">
            <div class="actions" :class="{'loading-opacity': loading}">

                <app-overlay-loader v-if="loading"/>

                <template v-else>
                    <app-context-button
                        icon="activity"
                        :title="this.$t('actions')">
                        <a  href="#" class="dropdown-item" @click="openActionModal('approve')">
                            {{ this.$t('approve') }}
                        </a>
                      <a  href="#" class="dropdown-item" @click="openActionModal('correct')">
                        {{ this.$t('correct') }}
                      </a>
                        <a  href="#" class="dropdown-item" @click="openActionModal('clear_correction')">
                            {{ this.$t('clear_correction') }}
                        </a>
                    </app-context-button>
                </template>

            </div>
        </div>

        <attendance-context-action-modal
            v-if="contextModal"
            v-model="contextModal"
            :modal-type="contextType"
            :attendances="attendances"
            :all_attendances="all_data"
        />

        <app-confirmation-modal
            :title="promptTitle"
            :message="promptMessage"
            :modal-class="modalClass"
            :icon="promptIcon"
            v-if="confirmationModelActive"
            modal-id="app-confirmation-modal"
            @confirmed="triggerConfirm"
            @cancelled="cancelled"
            :loading="loading"
            :self-close="false"
        />
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import AttendanceContextActionModal from "./Context/AttendanceContextActionModal";
import {axiosPost} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "AttendanceContextMenu",
    components: {AttendanceContextActionModal},
    mixins: [FormHelperMixins],
    props: {
        attendances: {},
        all_data:false
    },
    data() {
        return {
            departmentFlag: true,
            attendanceFlaf: true,
            workshiftFlag: true,
            projectFlag: true,
            confirmationModelActive: false,
            contextModal: false,
            contextType: '',
            loading: false,
            formData: {},
            promptIcon: '',
            promptTitle: '',
            promptMessage: '',
            modalClass: '',
            project_id: '',
            attendance_id: '',

        }
    },
    methods: {
        closeContextMenu() {
            this.$emit('close')
        },
        openActionModal(context) {
            this.contextModal = true;
            this.contextType = context;
            console.log('context')
        },
        handleLists(action) {
            this.contextType = action;
            this.confirmationModelActive = true;
            this.promptTitle = this.$t('are_you_sure');
            this.promptIcon = 'check-circle';
            this.modalClass = 'primary';
            action === 'department' ?
                this.promptMessage = this.$t('you_are_going_to_change_the_department_of_selected_employees') :
                this.promptMessage = this.$t('you_are_going_to_change_the_work_shift_of_selected_employees')
        },
        triggerConfirm() {
            this.loading = true;
            this.formData.attendances = this.attendances.map(attendance => attendance.id)
            const url = this.contextType === 'department' ?
                `${this.apiUrl.DEPARTMENTS}/move-employees` :
                `${this.apiUrl.EMPLOYEES}/${this.contextType}/update`;
            axiosPost(
                url,
                this.formData
            ).then(({data}) => {
                this.loading = false;
                this.confirmationModelActive = false;
                this.toastAndReload(data.message, 'attendance-table');
            }).catch(({response}) => {
                this.closeContextMenu();
                this.loading = false;
                this.$toastr.e('', response.data.message);
            }).finally(() => this.closeConfirmation());
        },
        cancelled() {
            this.confirmationModelActive = false;
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        },
    }
    // computed: {
    //     notTerminated() {
    //         return this.employees.filter(employee => employee.employment_status.alias !== 'terminated').length;
    //     }
    // }
}
console.log('context');

</script>
