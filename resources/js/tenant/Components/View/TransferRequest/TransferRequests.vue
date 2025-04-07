<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('transfer_request')">
            <app-default-button
                :title="$fieldTitle('add', 'transfer_request', true)"
                v-if="$can('view_designations')"
                @click="openModal()"
            />
        </app-page-top-section>

        <app-table
            id="transfer-request-table"
            :options="options"
            @action="triggerActions"
        />

        <app-transfer-request-modal
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            @close="isModalActive = false"
        />
      <app-transfer-request-status-modal
          v-if="isStatusModalActive"
          v-model="isStatusModalActive"
          :selected-url="selectedUrl"
          @close="isStatusModalActive = false"
      />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('transfer-request-table')"
            @cancelled="cancelled"
        />
    </div>
</template>
<script>
    import HelperMixin from "../../../../common/Mixin/Global/HelperMixin";
    // import TransferRequestMixin from "../../Mixins/TransferRequestMixin";
    import {TRANSFER_REQUEST} from "../../../Config/ApiUrl";
    import {mapState} from "vuex";
    import TransferRequestMixin from "../../Mixins/TransferRequestMixin";

    export default {

        name: "AppTransferRequest",
        mixins: [HelperMixin, TransferRequestMixin],
        data() {
            return {
                isModalActive: false,
              isStatusModalActive: false,

              selectedUrl: '',
            }
        },

        methods: {
            triggerActions(row, action, active) {
                if (action.title === this.$t('edit')) {
                    this.selectedUrl = `${TRANSFER_REQUEST}/${row.id}`;
                    this.isModalActive = true;
                }else if (action.title === this.$t('status')) {
                  this.selectedUrl = `${TRANSFER_REQUEST}/${row.id}`;
                  this.isStatusModalActive = true;
                }  else {
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
