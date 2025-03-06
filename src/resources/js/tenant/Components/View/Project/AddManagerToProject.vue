<template>
    <modal id="project-manager-modal"
           v-model="showModal"
           :title="$addLabel('project_engineer')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form
            :data-url='`${apiUrl.PROJECTS}/${id}/add-managers`'
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
                :label="$t('project_engineer')"
                list-value-field="full_name"
                :list = "list"
                v-model="formData.managers"
                :error-message="$errorMessage(errors, 'managers')"
                :fetch-url="`${apiUrl.SELECTABLE_MANAGERS}`"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";

export default {
    name: "AddManagerToProject",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        id: {
            required: true
        }
    },
    data() {
        return {
            formData: {
                managers: []
            },
            list: []
        }
    },
    mounted() {
        this.preloader = true;
        this.getProjectManagers();
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {managers: []};
            $('#project-manager-modal').modal('hide')
            this.toastAndReload(data.message, 'project-table');
        },
        getProjectManagers() {
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/managers`).then(({data}) => {
                this.preloader = false;
                this.formData.managers = data;
            })
        }
    },
}
</script>
