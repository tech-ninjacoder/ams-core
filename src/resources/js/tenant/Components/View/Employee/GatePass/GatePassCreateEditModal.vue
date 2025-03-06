<template>
    <modal id="gate-pass-modal"
           v-model="showModal"
           :title="generateModalTitle('Gate Pass')"
           @submit="submit" :loading="loading"
           :preloader="preloader">

        <form
            ref="form"
            :data-url='selectedUrl ? selectedUrl : GATE_PASSE'
            @submit.prevent="submit"
        >
            <app-form-group
                :label="$t('name')"
                :placeholder="$placeholder('name')"
                v-model="formData.name"
                :required="true"
                :error-message="$errorMessage(errors, 'name')">
            </app-form-group>
            <app-form-group
                    type="date"
                    :label="$t('valid_from')"
                    :placeholder="$placeholder('Validity Start Date')"
                    v-model="formData.valid_from"
                    :required="true"
                    :error-message="$errorMessage(errors, 'valid_from')">
            </app-form-group>
            <app-form-group
                    type="date"
                    :label="$t('valid_to')"
                    :placeholder="$placeholder('Validity End Date')"
                    v-model="formData.valid_to"
                    :required="true"
                    :error-message="$errorMessage(errors, 'valid_to')">
            </app-form-group>

            <app-form-group
                type="textarea"
                :label="$t('description')"
                :placeholder="$textAreaPlaceHolder('description')"
                v-model="formData.description"
                :required="true"
                :error-message="$errorMessage(errors, 'description')">
            </app-form-group>

            <app-form-group
                    :label="$t('valid')"
                    type="radio"
                    :list="[
                {id:'0',value: $t('no')},
                {id:'1', value:  $t('yes')},
            ]"
                    v-model="formData.valid"
                    :error-message="$errorMessage(errors, 'valid')"
            />

        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
    import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
    import {GATE_PASSE} from "../../../../Config/ApiUrl";
    import {formatDateForServer} from "../../../../../common/Helper/Support/DateTimeHelper";


    export default {
        name: "GatePassCreateEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {},
                GATE_PASSE,

            }
        },
        methods: {
            submit() {
                this.loading = true;
                this.message = '';
                this.errors = {};
                this.formData.valid_from = formatDateForServer(this.formData.valid_from);
                this.formData.valid_to = formatDateForServer(this.formData.valid_to);
                let formData = {...this.formData};
                this.save(formData);
            },
            afterSuccess({data}) {
                this.formData = {};
                $('#gate-pass-modal').modal('hide');
                this.$emit('input', false);
                this.toastAndReload(data.message, 'gate-pass-table');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
        },
    }
</script>

