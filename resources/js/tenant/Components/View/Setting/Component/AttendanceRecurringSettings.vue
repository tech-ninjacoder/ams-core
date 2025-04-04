<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else @submit.prevent="submitData" ref="form" :data-url="apiUrl.ATTENDANCE_SETTINGS_RECURRING">

        <app-form-group
                :label="$t('recurring_attendance')"
                v-model="formData.recurring_attendance"
                page="page"
                type="switch"
                :error-message="$errorMessage(errors, 'recurring_attendance')"
                label-alignment="top">
            <template slot="suggestion">
                {{ recurring_attendance_suggestion }}
                <app-note :title="$t('note')" note-type="note-warning" :notes="[$t('recurring_artisan_command')]"/>

            </template>
        </app-form-group>
    </form>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet, axiosPost} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "AttendanceRecurringSettings",
    mixins: [FormHelperMixins],
    data() {
        return {
            formData: {
                recurring_attendance: false,
            },
            recurring_attendance_suggestion: this.$t('recurring_attendance_suggestion')
        }
    },
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
       getRecurringSetting() {
            this.preloader = true;
            axiosGet(this.apiUrl.ATTENDANCE_SETTINGS).then(({data}) => {
                this.formData = {
                    // auto_approval: Boolean(parseInt(data.auto_approval)),
                    recurring_attendance: Boolean(parseInt(data.recurring_attendance))

                }
            }).finally(() => {
                this.preloader = false;
            })
        },
        submitRecurringData() {
            this.preloader = true;
            axiosPost(this.apiUrl.ATTENDANCE_SETTINGS_RECURRING, this.formData)
                .then(({data}) => {
                    this.formData.recurring_attendance = Boolean(parseInt(data.recurring_attendance));
                })
                .finally(() => {
                    this.preloader = false;
                });
        }
    },
    mounted() {
        this.getRecurringSetting();

    },
    watch: {
        'formData.recurring_attendance': {
            handler: function (value) {
                if (!value) {
                    this.formData.recurring_attendance = false;
                    // this.submitRecurringData();
                    this.submitData();
                    this.recurring_attendance_suggestion = this.$t('disabled_recurring_attendance_suggestion');

                } else {
                    this.formData.recurring_attendance = true;
                    this.submitData();
                    this.recurring_attendance_suggestion = this.$t('enabled_recurring_attendance_suggestion');

                }
            }
        }
    }
}
</script>
