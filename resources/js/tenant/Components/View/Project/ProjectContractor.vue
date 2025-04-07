<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('contractor')">
            <app-default-button
                :title="$fieldTitle('add', 'contractor', true)"
                v-if="$can('create_contractors')"
                @click="openModal()"
            />


        </app-page-top-section>

        <app-table
            id="contractor-table"
            :options="options"
            @action="triggerActions"
        />

        <app-contractor-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('contractor-table')"
            @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";
    import ContractorMixin from "../../Mixins/ContractorMixin";
    import {CONTRACTOR} from "../../../Config/ApiUrl";
    import {mapState} from "vuex";
    import coreLibrary from "../../../../core/helpers/CoreLibrary";




    export default {

        name: "AppContractor",
        extends: coreLibrary,
        mixins: [HelperMixin, ContractorMixin],
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
                    this.selectedUrl = `${CONTRACTOR}/${row.id}`;
                    this.isModalActive = true;
                } else {
                    this.getAction(row, action, active)
                }
            },

            openModal() {
                this.isModalActive = true;
                this.selectedUrl = ''
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
                this.toastAndReload(response.data.message, 'contractor-table');


            },
        },
    }
</script>
