<template>
    <modal id="project-working-shift-modal"
           v-model="showModal"
           :title="$addLabel('working_shift')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form
            :data-url='`${apiUrl.PROJECTS}/${id}/add-working-shifts`'
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
                :label="$t('working_shift')"
                list-value-field="name"
                :list = "list"
                v-model="formData.working_shifts"
                :error-message="$errorMessage(errors, 'working_shift')"
                :fetch-url="`${apiUrl.SELECTABLE_WORKING_SHIFT}`"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";

export default {
    name: "AddWorkingShiftToProject",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        id: {
            required: true
        }
    },
    data() {
        return {
            formData: {
                working_shifts: []
            },
            list: []
        }
    },
    mounted() {
        this.preloader = true;
        this.getProjectWrokingShifts();
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {working_shifts: []};
            $('#project-working-shift-modal').modal('hide')
            this.toastAndReload(data.message, 'project-table');
        },
        getProjectWrokingShifts() {
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/working-shifts`).then(({data}) => {
                this.preloader = false;
                this.formData.working_shifts = data;
            })
        }
    },
}
</script>
