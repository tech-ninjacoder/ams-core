<template>
    <modal id="project-gate-pass-modal"
           v-model="showModal"
           :title="$addLabel('gate_pass')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form
            :data-url='`${apiUrl.PROJECTS}/${id}/add-gate-passes`'
            method="POST"
            ref="form"
        >
<!--            <app-note-->
<!--                class="mb-4"-->
<!--                :title="$t('note')"-->
<!--                :notes="$can('update_working_shifts') ?-->
<!--                      $t('this_workshift_is_read_only_due_to_attendance_history') :-->
<!--                      $t('working_shift_update_note')"-->
<!--            />-->
            <app-form-group-selectable
                type="select"
                :label="$t('gate_passes')"
                list-value-field="name"
                :list = "list"
                v-model="formData.gate_passes"
                :error-message="$errorMessage(errors, 'gate_passes')"
                :fetch-url="`${apiUrl.SELECTABLE_GATE_PASS}`"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";

export default {
    name: "AddGatePassToProject",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        id: {
            required: true
        }
    },
    data() {
        return {
            formData: {
                gate_passes: []
            },
            list: []
        }
    },
    mounted() {
        this.preloader = true;
        this.getProjectGatePasses();
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {gate_passes: []};
            $('#project-gate-pass-modal').modal('hide')
            this.toastAndReload(data.message, 'project-table');
        },
        getProjectGatePasses() {
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/gate-passes`).then(({data}) => {
                this.preloader = false;
                this.formData.gate_passes = data;
            })
        }
    },
}
</script>
