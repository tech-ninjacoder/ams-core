<template>
    <modal
        id="job-history-edit-modal"
        :scrollable="false"
        v-model="showModal"
        :title="modalTitle"
        :btnLabel="buttonLabel"
        :submitBtnClass="modalType === 'approve' || modalType === 'correct' || modalType === 'clear_correction' || modalType === 'remove-employee' ? 'btn-danger' : 'btn-primary'"
        @submit="submit"

        :loading="loading"
        :preloader="preloader">

        <form
            ref="form"
            @submit.prevent="submitData">

            <app-input
                v-if="modalType === 'approve'"
                type="textarea"
                v-model="formData.description"
                :text-area-rows="4"
                :error-message="$errorMessage(errors, 'description')"
                :placeholder="$t('enter_approval_note')"
            />
          <app-input
              v-if="modalType === 'correct'"
              type="number"
              v-model="formData.hours"
              :error-message="$errorMessage(errors, 'hours')"
              :placeholder="$t('hours_correction')"
          />
          <app-input
              v-if="modalType === 'correct'"
              type="number"
              v-model="formData.minutes"
              :error-message="$errorMessage(errors, 'minutes')"
              :placeholder="$t('minutes_correction')"
          />
          <app-input
              v-if="modalType === 'correct'"
              type="number"
              v-model="formData.total"
              :error-message="$errorMessage(errors, 'total')"
              :max-length=14
              :placeholder="$t('total_correction')"
          />
            <app-note
                    v-if="modalType === 'clear_correction'"
                    class="pt-2 clearfix"
                    :title="'note'"
                    content-type="html"
                    note-type="note-red"
                    :notes="'submitting this request will clear all hours and minutes corrections for the selected attendances'"
            />

        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../../common/Mixin/Global/ModalMixin";
// import AttendanceDailyLogMixin from "../../../../Mixins/AttendanceDailyLogMixin";

import {formatDateForServer} from "../../../../../../common/Helper/Support/DateTimeHelper";
import {axiosPost} from "../../../../../../common/Helper/AxiosHelper";
import BasicInfo from "../../../Project/BasicInfo";

export default {
    name: "AttendanceContextActionModal",
    components: {BasicInfo},
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        modalType: {},
        attendances: {},
        all_attendances: false,
    },
    data() {
        return {}
    },
    methods: {
        submit() {
            this.loading = true;
            let actionType = {
                'approve': 'approve',
            }[this.modalType]
            this.formData.attendance_records = this.attendances.map(attendance => attendance.id);
          this.formData.all_data = this.all_attendances;
            if (actionType === 'approve') {
                this.updateAttendance(actionType)
            }else if(this.modalType ==='correct'){
              actionType = 'correct'

              this.correctAttendance(actionType)
              console.log('hello')
              console.log(actionType)
              // console.log(this.attendances.date)
              if (this.attendances.length > 0) {
                const firstRecord = this.attendances[0];
                const firstDate = firstRecord.in_date;
                this.formData.date = firstDate;
                console.log(firstDate);
              }


              console.log(this.formData)
            }else if(this.modalType ==='clear_correction'){
                actionType = 'clear_correction'

                this.clearCorrection(actionType)
                console.log('hello')
                console.log(actionType)
                // console.log(this.attendances.date)
                if (this.attendances.length > 0) {
                    const firstRecord = this.attendances[0];
                    const firstDate = firstRecord.in_date;
                    this.formData.date = firstDate;
                    console.log(firstDate);
                }


                console.log(this.formData)
            }
        },

        updateAttendance(actionType) {
            axiosPost(
                `${this.apiUrl.ATTENDANCES}/${actionType}/update`,
                this.formData
            ).then(({data}) => {
                this.loading = false;
                $('#job-history-edit-modal').modal('hide');
                this.$toastr.s('', data.message);
                this.showModal = false;
                this.$hub.$emit(`reload-daily-log`)
            }).catch(({response}) => {
                this.loading = false;
                this.$toastr.e('', response.data.message);
            })
        },
      correctAttendance(actionType) {
        axiosPost(
            `${this.apiUrl.ATTENDANCES}/${actionType}/update`,
            this.formData
        ).then(({data}) => {
          this.loading = false;
          $('#job-history-edit-modal').modal('hide');
          this.$toastr.s('', data.message);
          this.showModal = false;
          this.$hub.$emit(`reload-daily-log`)
        }).catch(({response}) => {
          this.loading = false;
          this.$toastr.e('', response.data.message);
        })
      },
        clearCorrection(actionType) {
            axiosPost(
                `${this.apiUrl.ATTENDANCES}/${actionType}/update`,
                this.formData
            ).then(({data}) => {
                this.loading = false;
                $('#job-history-edit-modal').modal('hide');
                this.$toastr.s('', data.message);
                this.showModal = false;
                this.$hub.$emit(`reload-daily-log`)
            }).catch(({response}) => {
                this.loading = false;
                this.$toastr.e('', response.data.message);
            })
        }
    },
    computed: {
        buttonLabel() {
            if (this.modalType === 'approve') {
                return this.$t('approve');
            }else if(this.modalType === 'clear_correction'){
                return this.$t('clear');
            }
            return 'save';
        },
        modalTitle() {
            if (this.modalType === 'approve') {
                return this.$t('confirm_employee_approve')
            }else if(this.modalType === 'correct'){
              return this.$t('bulk_correction')

            }else if(this.modalType === 'clear_correction'){
                return this.$t('Clear Corrections')
            }
            return this.$addLabel(this.modalType)
        }
    }
}
</script>
