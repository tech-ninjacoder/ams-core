<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('subdivision')">
            <app-default-button
                :title="$fieldTitle('add', 'subdivision', true)"
                v-if="$can('create_subdivisions')"
                @click="openModal()"
            />


        </app-page-top-section>

        <app-table
            id="subdivision-table"
            :options="options"
            @action="triggerActions"
        />

        <app-subdivision-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('subdivision-table')"
            @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";
    import SubdivisionMixin from "../../Mixins/SubdivisionMixin";
    import {SUBDIVISION} from "../../../Config/ApiUrl";
    import {mapState} from "vuex";
    import coreLibrary from "../../../../core/helpers/CoreLibrary";




    export default {

        name: "AppSubdivision",
        extends: coreLibrary,
        mixins: [HelperMixin, SubdivisionMixin],
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
                    this.selectedUrl = `${SUBDIVISION}/${row.id}`;
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
                this.toastAndReload(response.data.message, 'subdivision-table');


            },
        },
    }
</script>
