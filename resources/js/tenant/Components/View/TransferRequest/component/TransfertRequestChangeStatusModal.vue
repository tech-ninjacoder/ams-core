<template>
    <modal id="app-transfer-request-status-modal"
           size="large"
           v-model="showModal"
           title="Update Transfer Request"
           @submit="submitData"
           :scrollable="false"
           :loading="loading"
           :preloader="preloader"
    >
        <form method="post" ref="form"
              :data-url='selectedUrl ? selectedUrl : TRANSFER_REQUEST'
              @submit.prevent="submitData" >
            <app-form-group
                :label="$t('status')"
                type="select"
                :list="[
                  {id:'pending',value: $t('Pending')},
                  {id:'finished', value:  $t('Finished')},
                ]"
                v-model="formData.status"
                :placeholder="$placeholder('status')"
                :required="true"
                :error-message="$errorMessage(errors, 'status')"
            />
            <app-form-group
                :label="$t('comment')"
                type="textarea"
                v-model="formData.comment"
                :placeholder="$placeholder('comment')"
                :required="true"
                :error-message="$errorMessage(errors, 'comment')"
            />
        </form>

    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {formatDateTimeForServer, dateTimeToLocalWithFormat} from "../../../../../common/Helper/Support/DateTimeHelper";
import {TRANSFER_REQUEST, SELECTABLE_DEPARTMENT, SELECTABLE_ALL_DEPARTMENT} from "../../../../Config/ApiUrl";


export default {
    name: "TransferRequestsStatusModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
      tableId: {}
    },
  data() {
    return {
      formData: {},
      TRANSFER_REQUEST,
    }
  },
    methods: {
        submitData(){
            let formData = {...this.formData};
            this.setBasicFormActionData()
                .save(this.formData);
        },
        afterSuccessFromGetEditData({data}) {
            this.formData = data;
            this.preloader = false;
        },
        afterSuccess(response) {
            this.formData = {};
            $('#app-transfer-request-status-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(response.data.message, 'transfer-request-table')
          // this.toastAndReload(data.message, 'transfer-request-table');


        },

    }
}
</script>

<style scoped>

</style>
