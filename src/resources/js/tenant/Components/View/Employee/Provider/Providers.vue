<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('provider')">
            <app-default-button
                :title="$fieldTitle('add', 'provider', true)"
                v-if="$can('create_providers')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="provider-table"
            :options="options"
            @action="triggerActions"
        />

        <app-provider-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('provider-table')"
            @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
    import ProviderMixin from "../../../Mixins/ProviderMixin";
    import {PROVIDER} from "../../../../Config/ApiUrl";
    import {mapState} from "vuex";

    export default {

        name: "AppProvider",
        mixins: [HelperMixin, ProviderMixin],
        data() {
            return {
                isModalActive: false,
                selectedUrl: '',
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.title === this.$t('edit')) {
                    this.selectedUrl = `${PROVIDER}/${row.id}`;
                    this.isModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            openModal() {
                this.isModalActive = true;
                this.selectedUrl = ''
            },
        },
    }
</script>
