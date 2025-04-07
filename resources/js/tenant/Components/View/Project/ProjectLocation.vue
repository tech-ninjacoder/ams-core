<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('location')">
            <app-default-button
                :title="$fieldTitle('add', 'location', true)"
                v-if="$can('create_locations')"
                @click="openModal()"
            />


        </app-page-top-section>

        <app-table
            id="location-table"
            :options="options"
            @action="triggerActions"
        />

        <app-location-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('location-table')"
            @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";
    import LocationMixin from "../../Mixins/LocationMixin";
    import {CONTRACTOR, LOCATION} from "../../../Config/ApiUrl";
    import {mapState} from "vuex";
    import coreLibrary from "../../../../core/helpers/CoreLibrary";




    export default {

        name: "AppLocation",
        extends: coreLibrary,
        mixins: [HelperMixin, LocationMixin],
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
                    this.selectedUrl = `${LOCATION}/${row.id}`;
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
                this.toastAndReload(response.data.message, 'location-table');


            },
        },
    }
</script>
