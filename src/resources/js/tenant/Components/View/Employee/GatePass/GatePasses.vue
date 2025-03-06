<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('gate_passes')">
            <app-default-button
                :title="$fieldTitle('add', 'GatePass', true)"
                v-if="$can('create_helmets')"
                @click="openModal()"
            />


        </app-page-top-section>

        <app-table
            id="gate-pass-table"
            :options="options"
            @action="triggerActions"
        />

        <app-gate-pass-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('gate-pass-table')"
            @cancelled="cancelled"
        />
        <app-release-confirmation-modal
                v-if="ReleaseconfirmationModalActive"
                icon="trash-2"
                modal-id="app-release-confirmation-modal"
                @releaseconfirmed="releaseconfirmed('gate-pass-table')"
                @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
    import GatePassMixin from "../../../Mixins/GatePassMixin";
    import coreLibrary from "../../../../../core/helpers/CoreLibrary";
    import {GATE_PASSE} from "../../../../Config/ApiUrl";




    export default {

        name: "AppGatePass",
        extends: coreLibrary,
        mixins: [HelperMixin, GatePassMixin],
        data() {
            return {
                isModalActive: false,
                formData: {},
                selectedUrl: '',
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.title === this.$t('edit')) {
                    this.selectedUrl = `${GATE_PASSE}/${row.id}`;
                    this.isModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            openModal() {
                this.isModalActive = true;
                this.selectedUrl = ''
            },
        }
    }
</script>
