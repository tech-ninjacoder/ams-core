<template>
    <modal id="recurring-attendance-modal"
           size="extra-large"
           v-model="showModal"
           :title="this.$fieldTitle(this.selectedUrl ?
           (checkDisable ? 'view': 'edit') : 'add', 'recurring-attendance', true)"
           @submit="submit"
           :hide-submit-button="checkDisable"
           :hide-cancel-button="makeDefault"
           :close-button="false"
           :modal-backdrop="!makeDefault"
           :cancel-btn-label="checkDisable ? $t('close') : $t('cancel')"
           :loading="loading"
           :preloader="preloader">

        <form :data-url='selectedUrl ? selectedUrl : apiUrl.RECURRING_ATTENDANCE'
              :class="{'disabled-section' : checkDisable}"
              method="POST"
              ref="form">
          <div class="row">
            <div class="col-md-4">
              <app-form-group-selectable
                      :label="$t('project')"
                      type="search-select"
                      v-model="formData.project_id"
                      list-value-field="pme_name"
                      :placeholder="$t('project_sewarch')"
                      :error-message="$errorMessage(errors, 'project_id')"
                      :fetch-url="`${apiUrl.SELECTABLE_PROJECT}`"
              />
            </div>
            <div class="col-md-4">
              <app-form-group-selectable
                      type="select"
                      :label="$t('status')"
                      list-value-field="translated_name"
                      :list = "list"
                      v-model="formData.status_id"
                      :required="true"
                      :error-message="$errorMessage(errors, 'status')"
                      :fetch-url="`${apiUrl.SELECTABLE_RECURRING_ATTENDANCE_STATUS}`"
              />
            </div>
              <div class="col-md-4">
                  <app-form-group-selectable
                          type="select"
                          :label="$t('workingshift')"
                          list-value-field="name"
                          :list = "list"
                          v-model="formData.working_shift_id"
                          :required="true"
                          :error-message="$errorMessage(errors, 'workingshift')"
                          :fetch-url="`${apiUrl.SELECTABLE_WORKING_SHIFT}`"
                  />
              </div>
          </div>

            <div class="row">
                <div class="col-md-4">
                    <app-form-group
                      :label="$t('punch_in_time')"
                      type="time"
                      default="24"
                      v-model="formData.in_time"
                      :placeholder="$placeholder('punch_in_time')"
                      :required="true"
                      :error-message="$errorMessage(errors, 'in_time')"
                    />
                </div>
                <div class="col-md-4">
                    <app-form-group
                            :label="$t('punch_out_time')"
                            type="time"
                            default="24"
                            v-model="formData.out_time"
                            :placeholder="$placeholder('punch_out_time')"
                            :required="true"
                            :error-message="$errorMessage(errors, 'out_time')"
                    />
                </div>
            </div>





        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {localToUtc, formatDateForServer, formatUtcToLocal} from "../../../../common/Helper/Support/DateTimeHelper";
import {cloneDeep} from 'lodash'
import {errorMessageForArray} from '../../../../common/Helper/Support/FormHelper'
import {PROJECTS, SELECTABLE_PROJECT_STATUS} from "../../../Config/ApiUrl";

export default {
    name: "RecurringAttendanceCreateEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    props:{
      viewOnly:{
          type: Boolean,
          default: false,
      },
      makeDefault:{
          type: Boolean,
          default: false,
      },
        PROJECTS,
    },
    data() {
        return {
            errorMessageForArray,

            formData: {
                name: '',
                description:'',
                location:'',
                users: [],
              parent_project_id: [],

            },
          list: [],
        }
    },
    // mounted() {
    //     if(this.makeDefault){
    //         this.formData.name = this.$t('regular_work_shift');
    //         this.formData.start_date = new Date()
    //     }
    // },
    computed:{
      checkDisable () {
          return this.selectedUrl ? (!this.$can('update_working_shifts') || this.viewOnly ):
              !this.$can('create_working_shifts')
        }
    },
    methods: {
      convertToAmPm(time24) {
        // Split the time string into hours, minutes, and seconds
        const [hours, minutes, seconds] = time24.split(':');
        // Convert hours to an integer
        let hoursInt = parseInt(hours);
        // Determine AM or PM suffix
        const suffix = hoursInt >= 12 ? 'PM' : 'AM';
        // Convert to 12-hour format
        hoursInt = hoursInt % 12 || 12; // Converts '0' or '12' to '12'
        // Return the formatted time string
        return `${hoursInt}:${minutes}:${seconds} ${suffix}`;
      },

      submit() {
            this.loading = true;
            this.message = '';
            this.errors = {};

            this.formData.in_date = formatDateForServer(this.formData.in_date);

            // this.formData.p_start_date = formatDateForServer(this.formData.p_start_date);
            // this.formData.p_end_date = formatDateForServer(this.formData.p_end_date);



            let formData = {...this.formData};
            // if (this.formData.type === 'regular') {
            //     formData = this.prepareRegularType(this.formData);
            // }
            // else {
            //     formData = this.prepareSchedulerType(this.formData);
            // }

            // formData.name = formatDateForServer(formData.start_date);
            // formData.end_date = formatDateForServer(formData.end_date);
            // this.makeDefault ? formData.is_default = 1 : null;

            this.save(formData);
        },
        afterSuccess({data}) {
            this.formData = {};
            $('#recurring-attendance-modal').modal('hide')
            this.toastAndReload(data.message, 'recurring-attendance-table');
        },
        afterSuccessFromGetEditData({data}) {
            this.formData = data;
          this.formData.in_time = this.convertToAmPm(this.formData.in_time);
          this.formData.out_time = this.convertToAmPm(this.formData.out_time);

            // console.log(data.parent[0].id)
            // this.formData.start_date = formatDateForServer(data.start_date);
            // this.formData.end_at = formatUtcToLocal(data.end_at);
            // this.formData.weekdays = data.details.filter(week => {
            //     return parseInt(week.is_weekend);
            // }).map(week => {
            //     return week.weekday;
            // });

            // this.formData.details = data.details.map(details => {
            //     return {
            //         ...details,
            //         start_at: formatUtcToLocal(details.start_at),
            //         end_at: formatUtcToLocal(details.end_at),
            //     }
            // });

            // this.formData.departments = this.collection(data.departments).pluck()
            //     .concat(this.collection(data.upcoming_departments).pluck())
            // this.formData.users = this.collection(data.users).pluck()
            //     .concat(this.collection(data.upcoming_users).pluck())
            this.preloader = false;
        },

        // prepareRegularType(formData) {
        //     let data = cloneDeep(formData);
        //
        //     if (!(data.start_at && data.end_at)) {
        //         return data;
        //     }
        //
        //     data.details.map(day => {
        //         if (data.weekdays.includes(day.weekday)) {
        //             day.is_weekend = 1;
        //             return day;
        //         }
        //
        //         day.end_at = localToUtc(data.end_at);
        //         day.start_at = localToUtc(data.start_at);
        //         day.is_weekend = data.weekdays.includes(day.weekday) ? 1 : 0
        //         return day;
        //     });
        //
        //     return data;
        // },
        // prepareSchedulerType(formData) {
        //     let data = cloneDeep(formData);
        //     data.details.map(day => {
        //         day.start_at = localToUtc(day.start_at);
        //         day.end_at = localToUtc(day.end_at);
        //         day.is_weekend = !!(day.start_at && day.end_at) ? 0 : 1
        //         return day;
        //     })
        //     return data;
        // }

    },
}
</script>
