<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('daily_log')">
            <app-attendance-top-buttons :tableId="tableId"/>
        </app-page-top-section>
        <app-table
            :id="tableId"
            :options="options"
            @getRows="getSelectedRows"
            @action="triggerActions"
        />

        <app-attendance-create-edit-modal
            v-if="isAttendanceModalActive"
            v-model="isAttendanceModalActive"
        />

        <app-attendance-edit-request-modal
            v-if="isEditModalActive"
            v-model="isEditModalActive"
            :selectedUrl="selectedUrl"
            :tableId="tableId"
        />
      <app-attendance-correct-request-modal
          v-if="isCorrectModalActive"
          v-model="isCorrectModalActive"
          :selectedUrl="selectedUrl"
          :tableId="tableId"
      />

        <app-attendance-checkout-request-modal
                v-if="isCheckoutModalActive"
                v-model="isCheckoutModalActive"
                :selectedUrl="selectedUrl"
                :tableId="tableId"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :message="modalSubtitle"
            :modal-class="modalClass"
            :icon="modalIcon"
            modal-id="app-confirmation-modal"
            @confirmed="updateStatus()"
            @cancelled="cancelled"
        />
        <attendance-context-menu
                v-if="isContextMenuOpen"
                :attendances="selectedAttendances"
                :all_data="all_data"
                @close="isContextMenuOpen = false"
        />

        <app-attendance-log-modal
            v-if="isAttendanceLogModalActive"
            v-model="isAttendanceLogModalActive"
            :url="changeLogUrl"
            :tableId="tableId"
        />

    </div>
</template>

<script>
import AttendanceDailyLogMixin from "../../Mixins/AttendanceDailyLogMixin";
import AppAttendanceTopButtons from "./Component/AppAttendanceTopButtons";
import AttendanceRequestActionMixin from "../../Mixins/AttendanceRequestActionMixin";
import AttendanceContextMenu from "./Component/AttendanceContextMenu";

export default {
    name: "DailyLog",
    components: {AttendanceContextMenu, AppAttendanceTopButtons},
    mixins: [AttendanceDailyLogMixin, AttendanceRequestActionMixin],
    data() {
        return {
            tableId:'daily-log',
            isAttendanceModalActive: false,
            selectedUrl: ''
        }
    },
    methods: {
        openAttendanceModal() {
            this.isAttendanceModalActive = true;
        },
    }
}
</script>
