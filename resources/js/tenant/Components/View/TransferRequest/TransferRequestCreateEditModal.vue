<template>
    <modal id="app-transfer-request-modal"
           v-model="showModal"
           :title="generateModalTitle('transfer_request')"
           @submit="submitData" :loading="loading"
           :preloader="preloader">

        <form
            ref="form"
            :data-url='selectedUrl ? selectedUrl : TRANSFER_REQUEST'
            @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('title')"
                :placeholder="$placeholder('title')"
                v-model="formData.title"
                :required="true"
                :error-message="$errorMessage(errors, 'title')">
            </app-form-group>

            <app-form-group
                type="textarea"
                :label="$t('description')"
                :placeholder="$textAreaPlaceHolder('description')"
                v-model="formData.description"
                :required="true"
                :error-message="$errorMessage(errors, 'description')">
            </app-form-group>
            <app-form-group-selectable
                    type="select"
                    :label="$t('department')"
                    list-value-field="name"
                    v-model="formData.department_id"
                    :error-message="$errorMessage(errors, 'departments')"
                    :fetch-url="`${apiUrl.SELECTABLE_ALL_DEPARTMENT}`"
            />

        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
    import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
    import {TRANSFER_REQUEST, SELECTABLE_DEPARTMENT, SELECTABLE_ALL_DEPARTMENT} from "../../../Config/ApiUrl";
    import {axiosGet} from "../../../../common/Helper/AxiosHelper";


    export default {
        name: "TransferRequestCreateEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {},
                TRANSFER_REQUEST,
              SELECTABLE_ALL_DEPARTMENT
            }
        },
        mounted() {
            // this.getProjectManagers();
        },
        methods: {
            afterSuccess({data}) {
                this.formData = {};
                $('#transfer-request-modal').modal('hide');
                this.$emit('input', false);
                this.toastAndReload(data.message, 'transfer-request-table');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
            // getProjectManagers() {
            //     axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/managers`).then(({data}) => {
            //         this.preloader = false;
            //         this.formData.managers = data;
            //     })
            // }
        },
    }
</script>

