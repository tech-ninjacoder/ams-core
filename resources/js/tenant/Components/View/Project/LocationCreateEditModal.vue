<template>
    <modal id="location-modal"
           v-model="showModal"
           :title="generateModalTitle('location')"
           @submit="submitData" :loading="loading"
           :preloader="preloader">

        <form
            ref="form"
            :data-url='selectedUrl ? selectedUrl : LOCATION'
            @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('name')"
                :placeholder="$placeholder('name')"
                v-model="formData.name"
                :required="true"
                :error-message="$errorMessage(errors, 'name')">
            </app-form-group>

<!--            <app-form-group-->
<!--                type="select"-->
<!--                :label="$t('parent_id')"-->
<!--                :placeholder="$textAreaPlaceHolder('parent_id')"-->
<!--                v-model="formData.parent_id"-->
<!--                :required="true"-->
<!--                :error-message="$errorMessage(errors, 'parent_id')">-->
<!--            </app-form-group>-->

        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
    import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
    import {LOCATION} from "../../../Config/ApiUrl";

    export default {
        name: "LocationCreateEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {},
                LOCATION,}
        },
        methods: {
            afterSuccess({data}) {
                this.formData = {};
                $('#location-modal').modal('hide');
                this.$emit('input', false);
                this.toastAndReload(data.message, 'location-table');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
        },
    }
</script>

