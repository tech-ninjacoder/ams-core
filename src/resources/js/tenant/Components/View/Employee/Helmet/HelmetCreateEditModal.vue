<template>
    <modal id="helmet-modal"
           v-model="showModal"
           :title="generateModalTitle('helmet')"
           @submit="submitData" :loading="loading"
           :preloader="preloader">

        <form
            ref="form"
            :data-url='selectedUrl ? selectedUrl : HELMET'
            @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('imei')"
                :placeholder="$placeholder('imei')"
                v-model="formData.imei"
                :required="true"
                :error-message="$errorMessage(errors, 'imei')">
            </app-form-group>
            <app-form-group
                    :label="$t('barcode')"
                    :placeholder="$placeholder('PME BARCODE')"
                    v-model="formData.pme_barcode"
                    :required="true"
                    :error-message="$errorMessage(errors, 'barcode')">
            </app-form-group>

            <app-form-group
                type="textarea"
                :label="$t('description')"
                :placeholder="$textAreaPlaceHolder('description')"
                v-model="formData.description"
                :required="true"
                :error-message="$errorMessage(errors, 'description')">
            </app-form-group>

        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
    import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
    import {HELMET, SELECTABLE_DEPARTMENT} from "../../../../Config/ApiUrl";

    export default {
        name: "HelmetCreateEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {},
                HELMET,
                SELECTABLE_DEPARTMENT
            }
        },
        methods: {
            afterSuccess({data}) {
                this.formData = {};
                $('#helmet-modal').modal('hide');
                this.$emit('input', false);
                this.toastAndReload(data.message, 'helmet-table');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
        },
    }
</script>

