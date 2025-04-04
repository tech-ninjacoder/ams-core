<template>
    <modal id="project-coordinator-modal"
           v-model="showModal"
           :title="$addLabel('coordinator')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
           :scrollable="false"
    >
        <form
            :data-url='`${apiUrl.PROJECTS}/${id}/add-coordinators`'
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
                :label="$t('coordinator')"
                list-value-field="full_name"
                :list = "list"
                v-model="formData.coordinators"
                :error-message="$errorMessage(errors, 'coordinators')"
                :fetch-url="`${apiUrl.TENANT_SELECTABLE_USER}?without=admin&coordinator=only`"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";

export default {
    name: "AddCoordinatorToProject",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        id: {
            required: true
        }
    },
    data() {
        return {
            formData: {
                coordinators: []
            },
            list: []
        }
    },
    mounted() {
        this.preloader = true;
        this.getProjectCoordinators();
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {managers: []};
            $('#project-coordinator-modal').modal('hide')
            this.toastAndReload(data.message, 'project-table');
        },
      getProjectCoordinators() {
            axiosGet(`${this.apiUrl.PROJECTS}/${this.id}/coordinators`).then(({data}) => {
                this.preloader = false;
                this.formData.coordinators = data;
            })
        }
    },
}
</script>
