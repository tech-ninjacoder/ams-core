<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('workers_providers')" icon="briefcase">
            <app-default-button
                v-if="$can('create_departments')"
                :title="$addLabel('Worker Provider')"
                @click="openWrokersProvidersModal()"
            />
        </app-page-top-section>

        <app-table
            id="workers_providers-table"
            :options="options"
            @action="triggerActions"
        />

        <app-workers-providers-modal
            v-if="isWorker_providersModalActive"
            v-model="isWorker_providersModalActive"
            :selected-url="selectedUrl"
        />

        <app-confirmation-modal
            :message="promptSubtitle"
            :modal-class="modalClass"
            :icon="promptIcon"
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="changeStatus"
            @cancelled="cancelled"
        />

    </div>
</template>

<script>
    import WorkersProvidersMixin from "../../../Mixins/WorkersProvidersMixin";
    import {axiosPost} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "Workers_Providers",
    mixins: [WorkersProvidersMixin],
    data() {
        return {
            // workers_providers: '',
            worker_provider:'',
            worker_providerID: '',
            workers_providers: [],
            selectedUrl: '',
            modalClass: '',
            promptIcon: '',
            promptSubtitle: '',
            worker_providerName: '',
            worker_providerUser: '',
            isWorker_providersModalActive: false,
            confirmationModalActive: false,
            isEmployeeMovementModalActive: false,
        }
    },
    // created() {
    //     this.$store.dispatch('getStatuses', 'department')
    // },
    watch: {
        isWorker_providersModalActive: function (value) {
            if (!value) {
                this.selectedUrl = '';
            }
        }
    },
    methods: {
        openWrokersProvidersModal() {
            this.selectedUrl = '';
            this.isWorker_providersModalActive = true;
        },

        triggerActions(row, action, active) {
            this.worker_providerID = row.id;
            this.promptIcon = action.icon;
            this.modalClass = action.modalClass;
            this.promptSubtitle = action.promptSubtitle;

            if (action.name === 'edit') {
                this.selectedUrl = `${action.url}/${row.id}`;
                this.isWorker_providersModalActive = true;
            } else {
                this.getAction(row, action, active)
            }
        }
    }
}
</script>
