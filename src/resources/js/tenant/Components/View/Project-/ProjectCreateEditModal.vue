<template>
    <modal id="project-modal"
           size="large"
           v-model="showModal"
           :title="this.$fieldTitle(this.selectedUrl ?
           (checkDisable ? 'view': 'edit') : 'add', 'project', true)"
           @submit="submit"
           :hide-submit-button="checkDisable"
           :hide-cancel-button="makeDefault"
           :close-button="false"
           :modal-backdrop="!makeDefault"
           :cancel-btn-label="checkDisable ? $t('close') : $t('cancel')"
           :loading="loading"
           :preloader="preloader">

        <form :data-url='selectedUrl ? selectedUrl : apiUrl.PROJECTS'
              :class="{'disabled-section' : checkDisable}"
              method="POST"
              ref="form">
            <app-note v-if="this.selectedUrl && !viewOnly"
                      class="mb-4"
                      :title="$t('note')"
                      :notes="(checkDisable && $can('update_working_shifts')) ?
                      $t('this_workshift_is_read_only_due_to_attendance_history') :
                      $t('working_shift_update_note')"
            />
            <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
            <app-form-group
                    :label="$t('description')"
                    type="text"
                    v-model="formData.description"
                    :placeholder="$placeholder('description', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'description')"
            />
            <app-form-group
                    :label="$t('location')"
                    type="text"
                    v-model="formData.location"
                    :placeholder="$placeholder('location', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'location')"
            />




        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../common/Mixin/Global/ModalMixin";
import {localToUtc, formatDateForServer, formatUtcToLocal} from "../../../../common/Helper/Support/DateTimeHelper";
import {cloneDeep} from 'lodash'
import {errorMessageForArray} from '../../../../common/Helper/Support/FormHelper'
import {PROJECTS} from "../../../Config/ApiUrl";

export default {
    name: "ProjectCreateEditModal",
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
                // type: 'regular',
                // departments: [],
                // weekdays: [],
                users: [],
                // attendances_count: 0,
                // details: [
                //     {
                //         weekday: 'sun',
                //         start_at: '',
                //         end_at: '',
                //         is_weekend: 0
                //     },
                //     {
                //         weekday: 'mon',
                //         start_at: '',
                //         end_at: '',
                //         is_weekend: 0
                //     },
                //     {
                //         weekday: 'tue',
                //         start_at: '',
                //         end_at: '',
                //         is_weekend: 0
                //     },
                //     {
                //         weekday: 'wed',
                //         start_at: '',
                //         end_at: '',
                //         is_weekend: 0
                //     },
                //     {
                //         weekday: 'thu',
                //         start_at: '',
                //         end_at: '',
                //         is_weekend: 0
                //     },
                //     {
                //         weekday: 'fri',
                //         start_at: '',
                //         end_at: '',
                //         is_weekend: 0
                //     },
                //     {
                //         weekday: 'sat',
                //         start_at: '',
                //         end_at: '',
                //         is_weekend: 0
                //     }
                // ],
            },
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
        submit() {
            this.loading = true;
            this.message = '';
            this.errors = {};

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
            $('#project-modal').modal('hide')
            this.toastAndReload(data.message, 'project-table');
        },
        afterSuccessFromGetEditData({data}) {
            this.formData = data;
            this.formData.name = data.name;
            this.formData.description = data.description;
            this.formData.location = data.location;
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
