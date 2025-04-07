<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('alerts')">

        </app-page-top-section>

        <app-table
            id="alerts-table"
            :options="options"
            @action="triggerActions"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../../../common/Mixin/Global/HelperMixin";
    import AlertMixins from "../../../../Mixins/AlertsMixin";
    import coreLibrary from "../../../../../../core/helpers/CoreLibrary";
    import {ALERTS} from "../../../../../Config/ApiUrl";




    export default {

        name: "AppAlerts",
        extends: coreLibrary,
        mixins: [HelperMixin, AlertMixins],
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
                    this.selectedUrl = `${ALERTS}/${row.id}`;
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
