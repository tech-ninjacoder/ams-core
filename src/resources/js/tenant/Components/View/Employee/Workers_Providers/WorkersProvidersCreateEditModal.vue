<template>
    <modal id="workers-providers-modal"
           v-model="showModal"
           :title="generateModalTitle('workers_providers')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">
        <form :data-url='selectedUrl ? selectedUrl : apiUrl.WORKERS_PROVIDERS'
              method="POST"
              ref="form">

            <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
            <app-form-group
                :label="$t('description')"
                type="textarea"
                v-model="formData.description"
                :placeholder="$textAreaPlaceHolder('description')"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";

export default {
    name: "WorkersProvidersCreateEdit",
    components: {},
    mixins: [FormHelperMixins, ModalMixin],
    comments: {},
    data() {
        return {

            formData: {
                name: '',
                worker_providerID: '',
            },
        }
    },
    computed: {
        parentDeptEditPermission() {
            return this.selectedUrl ? this.formData.worker_providerID : true;
        }
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {};
            $('#department-modal').modal('hide');
            this.toastAndReload(data.message, 'department-table')
            this.$emit('input', false);
        },
        afterSuccessFromGetEditData({data}) {
            this.preloader = false
            this.formData = data
            if (this.formData.worker_providerID === null) {
                this.formData.worker_providerID = ''
            }
        }
    }
}
</script>
