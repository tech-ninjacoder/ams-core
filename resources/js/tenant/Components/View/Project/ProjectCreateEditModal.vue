<template>
    <modal id="project-modal"
           size="extra-large"
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
          <div class="row">
            <div class="col-md-6">
                <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
                </div>
            <div class="col-md-3">
              <app-form-group
                      :label="$t('pme_id')"
                      type="text"
                      v-model="formData.pme_id"
                      :placeholder="$placeholder('Project ID', '')"
                      :required="true"
                      :error-message="$errorMessage(errors, 'pme_id')"
              />
            </div>
            <div class="col-md-3">

              <app-form-group-selectable
                      type="select"
                      :label="$t('status')"
                      list-value-field="name"
                      :list = "list"
                      v-model="formData.status_id"
                      :required="true"
                      :error-message="$errorMessage(errors, 'status')"
                      :fetch-url="`${apiUrl.SELECTABLE_PROJECT_STATUS}`"
              />
            </div>
          </div>

            <div class="row">
            <div class="col-md-4">
            <app-form-group
                    :label="$t('p_start_date')"
                    type="date"
                    v-model="formData.p_start_date"
                    :placeholder="$placeholder('p_start_date', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'p_start_date')"
            />
            </div>
            <div class="col-md-4">
            <app-form-group
                    :label="$t('p_end_date')"
                    type="date"
                    v-model="formData.p_end_date"
                    :placeholder="$placeholder('p_end_date', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'p_end_date')"
            />
            </div>
            <div class="col-md-4">
            <app-form-group
                    :label="$t('est_man_hour')"
                    type="number"
                    v-model="formData.est_man_hour"
                    :placeholder="$placeholder('est_man_hour', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'est_man_hour')"
            />
            </div>
            </div>
          <div class="row">
          <div class="col-md-4">
            <app-form-group
                    :label="$t('lunch_in')"
                    type="radio"
                    :list="[
                {id:'0',value: $t('outside')},
                {id:'1', value:  $t('inside')},
                // {id:'other', value:  $t('other')}
            ]"
                    v-model="formData.lunch_in"
                    :error-message="$errorMessage(errors, 'lunch_in')"
            />
          </div >
          <div class="col-md-4">
            <app-form-group
                    :label="$t('location_details')"
                    type="text"
                    v-model="formData.location"
                    :placeholder="$placeholder('location', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'location')"
            />
          </div>
            <div class="col-md-4">
            <app-form-group-selectable
                    type="select"
                    :label="$t('contractor')"
                    list-value-field="name"
                    :list = "list"
                    v-model="formData.contractor_id"
                    :error-message="$errorMessage(errors, 'contractors')"
                    :fetch-url="`${apiUrl.TENANT_SELECTABLE_CONTRACTOR}`"
            />
              <app-form-group-selectable
                  type="select"
                  :label="$t('location')"
                  list-value-field="name"
                  :list = "list"
                  v-model="formData.location_id"
                  :error-message="$errorMessage(errors, 'locations')"
                  :fetch-url="`${apiUrl.TENANT_SELECTABLE_LOCATION}`"
              />
              <app-form-group-selectable
                  type="select"
                  :label="$t('subdivision')"
                  list-value-field="name"
                  :list = "list"
                  v-model="formData.subdivision_id"
                  :error-message="$errorMessage(errors, 'subdivisions')"
                  :fetch-url="`${apiUrl.TENANT_SELECTABLE_SUBDIVISION}`"
              />
          </div>
          </div>
          <div class="col-md-6">
            <app-form-group-selectable
                    type="search-select"
                    :label="$t('parent')"
                    list-value-field="pme_id"
                    :list = "list"
                    v-if="type"
                    v-model="formData.parent_project_id"
                    :error-message="$errorMessage(errors, 'parents')"
                    :fetch-url="`${apiUrl.SELECTABLE_PARENT_PROJECT}`"
            />
          </div>
            <app-form-group
                    :label="$t('description')"
                    type="text"
                    v-model="formData.description"
                    :placeholder="$placeholder('description', '')"
                    :required="true"
                    :error-message="$errorMessage(errors, 'description')"
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
import {PROJECTS, SELECTABLE_PROJECT_STATUS} from "../../../Config/ApiUrl";

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
              parent_project_id: [],
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
          return this.selectedUrl ? (!this.$can('update_projects') || this.viewOnly ):
              !this.$can('create_projects')
        }
    },
    methods: {
        submit() {
            this.loading = true;
            this.message = '';
            this.errors = {};
            this.formData.p_start_date = formatDateForServer(this.formData.p_start_date);
            this.formData.p_end_date = formatDateForServer(this.formData.p_end_date);



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
            if (data.parent.length > 0) {
              this.formData.parent_project_id = data.parent[0].id;
            }
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
