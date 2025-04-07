import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {PROJECTS, RECURRING_ATTENDANCE, RELEASE_PARENT_PROJECT} from "../../Config/ApiUrl";
// import {formatDateToLocal, formatUtcToLocal} from "../../../common/Helper/Support/DateTimeHelper";
// import moment from "moment";
import {ContractorFilterMixin, LocationFilterMixin, SubdivisionFilterMixin} from "./FilterMixin";


import AppFunction from "../../../core/helpers/app/AppFunction";

export default {
    mixins: [DatatableHelperMixin, ContractorFilterMixin, LocationFilterMixin, SubdivisionFilterMixin],
    data() {
        return {
            projectId: '',
            options: {
                name: this.$t('recurring_attendance'),
                url: RECURRING_ATTENDANCE,
                showHeader: true,
                showCount: true,
                showClearFilter: true,
                //enableRowSelect: true,

                columns: [
                    {
                        title: this.$t('project_id'),
                        type: 'text',
                        key: 'id',
                        isVisible: true,
                    },
                    {
                            title: this.$t('in_time'),
                            type: 'text',
                            key: 'in_time',
                            isVisible: true,
                    },
                    {
                        title: this.$t('out_time'),
                        type: 'text',
                        key: 'out_time',
                        isVisible: true,
                    },
                    {
                        title: this.$t('project'),
                        type: 'text',
                        key: 'project_name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        isVisible: true,
                        modifier: status => {
                            return `<span class="badge badge-pill badge-${status.class}">
                                ${status.translated_name}
                            </span>`
                        }
                    },
                    {
                        title: this.$t('pme_id'),
                        type: 'text',
                        key: 'project_pme_id',
                        isVisible: true,
                    },
                    {
                        title: this.$t('workingshift'),
                        type: 'text',
                        key: 'workingshift_name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('employees'),
                        type: 'text',
                        key: 'users_count',
                        isVisible: true,
                    },

                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    },
                ],
                filters: [
                    // {
                    //     title: this.$t('created'),
                    //     type: "range-picker",
                    //     key: "date",
                    //     option: ["today", "thisMonth", "last7Days", "thisYear"]
                    // }
                    // ,
                    // {
                    //     title: this.$t('contractor'),
                    //     type: "multi-select-filter",
                    //     key: "contractors",
                    //     option: [],
                    //     listValueField: 'name',
                    //     permission: !!this.$can('view_all_leaves')  && !!this.$can('view_users')
                    //
                    // },
                    // {
                    //     title: this.$t('location'),
                    //     type: "multi-select-filter",
                    //     key: "locations",
                    //     option: [],
                    //     listValueField: 'name',
                    //     permission: !!this.$can('view_all_leaves')  && !!this.$can('view_users')
                    // },
                    // {
                    //     title: this.$t('subdivision'),
                    //     type: "multi-select-filter",
                    //     key: "subdivisions",
                    //     option: [],
                    //     listValueField: 'name',
                    //     permission: !!this.$can('view_all_leaves')  && !!this.$can('view_users')
                    // },
                    // {
                    //     title: this.$t('status'),
                    //     type: "multi-select-filter",
                    //     key: "status",
                    //     option: [],
                    //     listValueField: 'name',
                    //     permission: !!this.$can('view_all_leaves')  && !!this.$can('view_users')
                    // },
                    // {
                    //     title: this.$t('type'),
                    //     type: "radio",
                    //     key: "type",
                    //     option: [
                    //         {id: 0, value: this.$t('sub.')},
                    //         {id: '1', value: this.$t('group')},
                    //         {id: '2', value: this.$t('all')}
                    //         ],
                    //     permission: !!this.$can('view_employees')
                    // },
                    // {
                    //     title: this.$t('type'),
                    //     type: 'radio',
                    //     key: 'type',
                    //     // isVisible: true,
                    //     // "initValue": 2,
                    //     option: [
                    //         {id: '0', value: 'all'},
                    //         {id: '1', value: 'Sub Group'},
                    //         {id: '2', value: 'Group'},
                    //         {id: '3', value: 'Mixed'},
                    //         {id: '4', value: 'Normal'}
                    //
                    //
                    //     ],
                    //     "header": {
                    //         "title": 'filter projects record by type, sub group or main group',
                    //         // "description": 'You can filter your data table which are created based on segment'
                    //     },
                    // },


                    // {
                    //     title: this.$t('type'),
                    //     type: "checkbox",
                    //     key: "type",
                    //     option: [
                    //         {id: 'regular', value: this.$t('regular')},
                    //         {id: 'scheduled', value: this.$t('scheduled')},
                    //     ]
                    // }
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        name: 'edit',
                        modifier: row => this.$can('update_working_shifts')
                    },
                    // {
                    //     title: this.$t('view_workshift'),
                    //     icon: 'eye',
                    //     type: 'modal',
                    //     name: 'edit',
                    //     modifier: row => row.attendances_count > 0 || !this.$can('update_working_shifts')
                    // },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        message: this.$t('you_are_going_to_delete_message', { resource: this.$t('project') }),
                        name: 'delete',
                        modifier: row => this.$can('delete_working_shifts') &&
                            !(!!parseInt(row.is_default)) && !(!!parseInt(row.attendances_count))
                    },
                    {
                        title: this.$addLabel('employee'),
                        icon: 'user',
                        type: 'modal',
                        name: 'add-employee',
                        modifier: row => this.$can('add_employees_to_working_shift')
                    }
                ],
            }
        }
    }
}
