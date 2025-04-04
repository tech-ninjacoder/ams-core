<template>
    <modal id="absence-download-modal"
           size="large"
           v-model="showModal"
           :title="$t('download_absences')"
           @submit="submitData"
           :scrollable="false"
           :loading="loading"
           :preloader="preloader">
        <form
            ref="form"
            @submit.prevent="submitData"
        >

            <app-form-group
                :label="$t('date')"
                type="date"
                date-mode="date-mode"
                :max-date="new Date()"
                v-model="formData.date"
                :placeholder="$placeholder('date')"
                :required="true"
                :error-message="$errorMessage(errors, 'date')"
            />

        </form>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {formatDateTimeForServer, formatDateForServer} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "AbsenceDownloadModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: {
        tableId: {},
        employee: {},
        specificId: {}
    },
    data() {
        return {
            formData: {},
        }
    },
    mounted() {
        this.$nextTick(()=>{
            if (window.innerHeight < 700){
                document.getElementsByClassName('modal-body')[0].style.height = '560px'
            }
        })
    },
    methods: {
        submitData() {
            this.loading = true;
            const formData = {...this.formData};
            formData.date = formatDateForServer(this.formData.date);

            this.submitFromFixin(
                'post',
                `app/absence/log/download`,
                formData
            );
        },
        afterSuccess(response) {
            this.loading = false;
            $('#absence-download-modal').modal('hide');
            // console.log(response.data);
            // this.toastAndReload(response.data.message, this.tableId)

            // this.$toastr.s('', data.message);
            // if (this.tableId) {
            //     this.$hub.$emit(`reload-${this.tableId}`);
            // } else {
            //     this.$emit('reload')
            // }
            // var data = response.data;

            // Building the CSV from the Data two-dimensional array
            // Each column is separated by ";" and new line "\n" for next row
            var csvContent = response.data;
            // data.forEach(function(infoArray, index) {
            //     dataString = infoArray.join(';');
            //     csvContent += index < data.length ? dataString + '\n' : dataString;
            // });

            // The download function takes a CSV string, the filename and mimeType as parameters
            // Scroll/look down at the bottom of this snippet to see how download is called
            var download = function(content, fileName, mimeType) {
                var a = document.createElement('a');
                mimeType = mimeType || 'application/octet-stream';

                if (navigator.msSaveBlob) { // IE10
                    navigator.msSaveBlob(new Blob([content], {
                        type: mimeType
                    }), fileName);
                } else if (URL && 'download' in a) { //html5 A[download]
                    a.href = URL.createObjectURL(new Blob([content], {
                        type: mimeType
                    }));
                    a.setAttribute('download', fileName);
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                } else {
                    location.href = 'data:application/octet-stream,' + encodeURIComponent(content); // only this mime type is supported
                }
            }

            download(csvContent, formatDateForServer(this.formData.date)+'.csv', 'text/csv;encoding:utf-8');
        },
    },
    created() {
        if (!this.employee && !this.canUpdateStatus && !this.specificId) {
            this.formData.employee_id = window.user.id
        }

        if (this.specificId) {
            this.formData.employee_id = this.specificId
        }
    },
    computed: {
        canUpdateStatus() {
            if (this.specificId && this.$can('update_attendance_status')) {
                return false;
            }
            if (this.employee && this.$can('update_attendance_status')) {
                this.formData.employee_id = this.employee.id
                return false;
            }
            return this.$can('update_attendance_status');
        },
        user_id() {
            return window.user.id;
        },
        buttonFirstTitle() {
            return this.$can('update_attendance_status') ? 'add' : 'request';
        }
    }
}
</script>
