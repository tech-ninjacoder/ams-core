<template>
    <div>
        <div class="btn-group dropdown">
            <button type="button"
                    class="btn btn-success dropdown-toggle"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                {{ $t('download') }}
                <app-icon name="chevron-down"/>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item"
                   v-if="$can('approve_attendance')"
                   @click="openDownloadModal()">
                    Attendance Report
                </a>
                <a href="" @click.prevent="openInNewTab('/'+apiUrl.ATTENDANCE_DAILY_LOG_TODAY)" class="dropdown-item"
                   v-if="$can('approve_attendance')"
                   @click="apiUrl.ATTENDANCE_DAILY_LOG_TODAY">
                    Today's Attendance Report
                </a>
                <a href="#" class="dropdown-item"
                   v-if="$can('approve_attendance')"
                   @click="openAbsenceDownloadModal()">
                    Absence Report
                </a>
              <a href="#" class="dropdown-item"
                 v-if="$can('synch_attendance')"
                 @click="hrms_upload()">
                Upload
              </a>
            </div>
        </div>

        <app-default-button
                btn-class="btn btn-success mr-2"
                :title="$t('settings')"
                :url="apiUrl.ATTENDANCE_SETTINGS_FRONT_END"
                v-if="$can('view_attendance_settings')"
        />


<!--        <app-default-button-->
<!--                btn-class="btn btn-success mr-2"-->
<!--                :title="$t('download')"-->
<!--                :url="apiUrl.ATTENDANCE_DAILY_LOG_TODAY"-->
<!--                v-if="$can('view_attendance_settings')"-->
<!--        />-->
<!--        <app-default-button-->
<!--                btn-class="btn btn-success mr-2"-->
<!--                :title="$t('download_specific_date')"-->
<!--                v-if="$can('view_attendance_settings')"-->
<!--                @click="openDownloadModal()"-->
<!--        />-->
<!--        <app-default-button-->
<!--                btn-class="btn btn-success mr-2"-->
<!--                :title="$t('absences')"-->
<!--                v-if="$can('view_attendance_settings')"-->
<!--                @click="openAbsenceDownloadModal()"-->
<!--        />-->

        <div class="btn-group dropdown">
            <app-default-button
                v-if="this.$can('update_attendance_status') || requestButton"
                :title="$fieldTitle(buttonFirstTitle, 'attendance', true)"
                @click="openAttendanceModal()"
            />
            <button v-if="this.$can('update_attendance_status') && requestButton" class="btn btn-primary rounded-right px-3" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-chevron-down fa-sm"></i>
            </button>
            <div v-if="this.$can('update_attendance_status') && requestButton" class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#" @click="openAdminReqAttendanceModal()">
                    {{ $t('request_attendance') }}
                </a>
            </div>
        </div>

        <app-attendance-create-edit-modal
            :tableId="tableId"
            v-if="isAttendanceModalActive"
            v-model="isAttendanceModalActive"
            :specific-id="specificUserId"
        />
        <app-attendance-download-modal
                :tableId="tableId"
                v-if="isDownloadModalActive"
                v-model="isDownloadModalActive"
                :specific-id="specificUserId"
        />
        <app-absence-download-modal
                :tableId="tableId"
                v-if="isAbsenceDownloadModalActive"
                v-model="isAbsenceDownloadModalActive"
                :specific-id="specificUserId"
        />
    </div>
</template>

<script>

    import AppAttendanceTopButtons from "./AttendanceDownloadModal";
    import {axiosPost} from "../../../../../common/Helper/AxiosHelper";
    import {axiosGet} from "../../../../../common/Helper/AxiosHelper";


    export default {
    name: "AppAttendanceTopButtons",
    props: {
        requestButton: {
            type: Boolean,
            default: false
        },
        tableId: {},
        specificId: {}
    },
    data() {
        return {
            adminRequestId: null,
            isAttendanceModalActive: false,
            isDownloadModalActive: false,
            isAbsenceDownloadModalActive: false,

        }
    },
    computed: {
        user() {
            return window.user
        },
        buttonFirstTitle() {
            return this.$can('update_attendance_status') ? 'add' : 'request';
        },
        specificUserId() {
            return this.specificId ? this.specificId
                : this.adminRequestId ? this.adminRequestId
                    : null
        }
    },
    methods: {
        openAdminReqAttendanceModal() {
            this.adminRequestId = window.user.id
            this.isAttendanceModalActive = true;
        },
        openAttendanceModal() {
            this.adminRequestId = null;
            this.isAttendanceModalActive = true;
        },
        openDownloadModal() {
            this.adminRequestId = null;
            this.isDownloadModalActive = true;
        },
        openAbsenceDownloadModal() {
            this.adminRequestId = null;
            this.isAbsenceDownloadModalActive = true;

        },
        openInNewTab(url) {
            window.open(url, '_blank');
        },
      hrms_upload(){
        axiosGet(
            `${this.apiUrl.ATTENDANCES}/upload`
        ).then(({data}) => {
          this.loading = false;
          $('#job-history-edit-modal').modal('hide');
          this.$toastr.s('', data.message);
          this.showModal = false;
          this.$hub.$emit(`reload-daily-log`)
        }).catch(({response}) => {
          this.loading = false;
          this.$toastr.e('', response.data.message);
        })
      }
    }
}
</script>
