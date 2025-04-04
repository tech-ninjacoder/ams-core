<template>
    <modal id="contractor-modal"
           v-model="showModal"
           :title="generateModalTitle('contractor')"
           @submit="submitData" :loading="loading"
           :preloader="preloader">

        <form
            ref="form"
            :data-url='selectedUrl ? selectedUrl : CONTRACTOR'
            @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('name')"
                :placeholder="$placeholder('name')"
                v-model="formData.name"
                :required="true"
                :error-message="$errorMessage(errors, 'name')">
            </app-form-group>

            <app-form-group
                type="textarea"
                :label="$t('phone_number')"
                :placeholder="$textAreaPlaceHolder('phone_number')"
                v-model="formData.phone_number"
                :required="true"
                :error-message="$errorMessage(errors, 'phone_number')">
            </app-form-group>
            <app-form-group
                    type="textarea"
                    :label="$t('note')"
                    :placeholder="$textAreaPlaceHolder('note')"
                    v-model="formData.note"
                    :required="true"
                    :error-message="$errorMessage(errors, 'note')">
            </app-form-group>

        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
    import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
    import {CONTRACTOR} from "../../../Config/ApiUrl";

    export default {
        name: "ContractorCreateEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {},
                CONTRACTOR,}
        },
        methods: {
            afterSuccess({data}) {
                this.formData = {};
                $('#contractor-modal').modal('hide');
                this.$emit('input', false);
                this.toastAndReload(data.message, 'contractor-table');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
        },
    }
</script>

