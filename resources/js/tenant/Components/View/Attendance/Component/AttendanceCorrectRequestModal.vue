<template>
    <modal id="attendance-correct-request-modal"
           size="large"
           v-model="showModal"
           :title="generateModalTitle('attendance')"
           @submit="submitData"
           :scrollable="false"
           :loading="loading"
           :preloader="preloader"
    >
        <form method="post" ref="form" :data-url="`${apiUrl.ATTENDANCES}/${formData.id}/correct`" @submit.prevent="submitData" >
            <div class="input-group" style="margin: 10px;">
                <app-form-group
                        :label="$t('hours_correction')"
                        type="number"
                        v-model="formData.hours_correction"
                        :placeholder="$placeholder('hours_correction')"
                        :required="true"
                        :error-message="$errorMessage(errors, 'hours_correction')"
                />
                <app-form-group
                        :label="$t('minutes_correction')"
                        type="number"
                        v-model="formData.minutes_correction"
                        :placeholder="$placeholder('minutes_correction')"
                        :required="true"
                        :error-message="$errorMessage(errors, 'minutes_correction')"
                />
            </div>


        </form>

    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {formatDateTimeForServer, dateTimeToLocalWithFormat} from "../../../../../common/Helper/Support/DateTimeHelper";


export default {
    name: "AttendanceCorrectRequestModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
      tableId: {}
    },
    methods: {
        submitData(){
            let formData = {...this.formData};
            this.setBasicFormActionData()
                .save(this.formData);
        },
        afterSuccessFromGetEditData({data}) {
            this.formData = data;
            this.formData.hours_correction = data.attendance.hours_correction;
            this.formData.minutes_correction = data.attendance.minutes_correction;

            console.log(this.formData)
            this.preloader = false;
        },
        afterSuccess(response) {
            this.formData = {};
            $('#attendance-correct-request-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(response.data.message, this.tableId)
        },

    }
}
</script>

<style scoped>

</style>
