<template>
    <modal id="provider-modal"
           v-model="showModal"
           :title="generateModalTitle('provider')"
           @submit="submitData" :loading="loading"
           :preloader="preloader">

        <form
            ref="form"
            :data-url='selectedUrl ? selectedUrl : PROVIDER'
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
                :label="$t('contract_type')"
                type="radio-buttons"
                :required="true"
                :list="list"
                v-model="formData.contract_type"
                :error-message="$errorMessage(errors, 'contract_type')"
            >
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
    import {PROVIDER, SELECTABLE_DEPARTMENT} from "../../../../Config/ApiUrl";

    export default {
        name: "ProviderCreateEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
              list: [
                {
                  id: 'in_house',
                  value: 'in-house'
                },
                {
                  id: 'outsourced',
                  value: 'Outsourced'
                }
              ],
                formData: {},
                PROVIDER,
                SELECTABLE_DEPARTMENT
            }
        },
        methods: {
            afterSuccess({data}) {
                this.formData = {};
                $('#provider-modal').modal('hide');
                this.$emit('input', false);
                this.toastAndReload(data.message, 'provider-table');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
        },
    }
</script>

