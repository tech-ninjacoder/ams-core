import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {PROJECTS,RELEASE_PARENT_PROJECT} from "../../Config/ApiUrl";
// import {formatDateToLocal, formatUtcToLocal} from "../../../common/Helper/Support/DateTimeHelper";
// import moment from "moment";
import {ContractorFilterMixin, LocationFilterMixin, SubdivisionFilterMixin, ProjectStatusFilterMixin} from "./FilterMixin";


import AppFunction from "../../../core/helpers/app/AppFunction";

export default {
    mixins: [DatatableHelperMixin, ContractorFilterMixin, LocationFilterMixin, SubdivisionFilterMixin, ProjectStatusFilterMixin],
    data() {
        return {
            projectId: '',
            options: {
                name: this.$t('project'),
                url: PROJECTS,
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
                        title: this.$t('pme_id'),
                        type: 'text',
                        key: 'pme_id',
                        isVisible: true,
                    },
                    // {
                    //     title: this.$t('name'),
                    //     type: 'text',
                    //     key: 'name',
                    //     isVisible: true,
                    // },
                    {
                        title: this.$t('name'),
                        type: 'component',
                        componentName: 'app-project-object',
                        key: 'image',
                        isVisible: true,

                        modifier: (value, row) => {
                            return row.icon ? `${AppFunction.getBaseUrl()}/${row.icon.value.split('/').filter(d => d).join('/')}` : '';
                        }
                    },

                    // {
                    //     title: this.$t('start_date'),
                    //     type: 'custom-html',
                    //     key: 'start_date',
                    //     isVisible: true,
                    //     modifier: start_date => start_date ? formatDateToLocal(start_date) : '-'
                    // },
                    // {
                    //     title: this.$t('end_date'),
                    //     type: 'custom-html',
                    //     key: 'end_date',
                    //     isVisible: true,
                    //     modifier: end_date => end_date ? formatDateToLocal(end_date) : '-'
                    // },
                    // {
                    //     title: this.$t('type'),
                    //     type: 'custom-html',
                    //     key: 'type',
                    //     isVisible: true,
                    //     modifier: type => type ? `<span class="badge badge-pill badge-${type === 'regular' ? 'primary' : 'success'}">${this.$t(type)}</span>` : '-'
                    // },
                    {
                        title: this.$t('description'),
                        type: 'text',
                        key: 'description',
                        isVisible: true,
                    },
                    {
                        title: this.$t('geofence_id'),
                        type: 'text',
                        key: 'geofence_id',
                        isVisible: false,
                    },
                    {
                        title: this.$t('contractor'),
                        type: 'object',
                        key: 'contractors',
                        isVisible: true,
                        modifier: (value, row) => {
                            return value ? value.name : '-';
                        }
                    },
                    {
                        title: this.$t('location'),
                        type: 'object',
                        key: 'locations',
                        isVisible: true,
                        modifier: (value, row) => {
                            return value ? value.name : '-';
                        }
                    },
                    {
                        title: this.$t('subdivision'),
                        type: 'object',
                        key: 'subdivisions',
                        isVisible: true,
                        modifier: (value, row) => {
                            return value ? value.name : '-';
                        }
                    },
                    {
                        title: this.$t('location_details'),
                        type: 'text',
                        key: 'location',
                        isVisible: true,
                    },
                    {
                        title: this.$t('type'),
                        type: 'custom-html',
                        // componentName: 'app-employee-project',
                        key: 'type',
                        isVisible: true,
                        modifier: (type,row) => {
                            //display M if mixed, display g if has a childrens display S if has a parent
                            if (row.parent.length > 0 && row.childrens.length > 0) {
                                return '<span class="badge badge-pill badge-info"> M </span>';
                            } else if (row.parent.length > 0) {
                                return '<span class="badge badge-pill badge-danger"> S </span>';
                            } else if (row.childrens.length > 0) {
                                return '<span class="badge badge-pill badge-dark"> G </span>';
                            }
                        }
                    },

                    {
                        title: this.$t('parent_group'),
                        type: 'object',
                        key: 'parent',
                        isVisible: true,
                        modifier: (parent) => {
                            let projectParent = '';
                            parent.forEach((element, index) => {
                                projectParent += index > 0 ? ', ' + element.pme_id : element.pme_id;
                            });
                            projectParent = projectParent ? projectParent : '-';
                            return projectParent;
                        }
                    },
                    {
                        title: this.$t('geometry'),
                        type: 'custom-html',
                        // componentName: 'app-employee-project',
                        key: 'geometry',
                        isVisible: true,
                        modifier: (geometry) => {
                            return geometry === null ? '<a class="text-danger text-capitalize">not completed</a>' : '<a class="text-success text-capitalize">ready</a>';
                        }
                    },
                    {
                        title: this.$t('geofence'),
                        type: 'custom-html',
                        // componentName: 'app-employee-project',
                        key: 'geofence_id',
                        isVisible: false,
                        modifier: (geofence_id) => {
                            return geofence_id === null ? '<a class="text-danger text-capitalize">OFF</a>' : '<a class="text-success text-capitalize">ON</a>';
                        }
                    },

                    {
                        title: this.$t('working_shift_name'),
                        type: 'text',
                        key: 'working_shifts_name',
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
                        title: this.$t('est_man_hour'),
                        type: 'text',
                        key: 'est_man_hour',
                        isVisible: true,
                    },
                    {
                        title: this.$t('lunch_in'),
                        type: 'custom-html',
                        // componentName: 'app-employee-project',
                        key: 'lunch_in',
                        isVisible: true,
                        modifier: (lunch_in) => {
                            return lunch_in === 0 ? 'outside' : 'inside';
                        }
                    },
                    {
                        title: this.$t('employees'),
                        type: 'text',
                        key: 'users_count',
                        isVisible: true,
                    },
                    {
                        title: this.$t('coordinator'),
                        type: 'custom-html',
                        key: 'coordinators',
                        isVisible: true,
                        modifier: (coordinators) => {
                            let projectCoordinator = '';
                            coordinators.forEach((element, index) => {
                                projectCoordinator += index > 0 ? ', ' + element.full_name : element.full_name;
                            });
                            projectCoordinator = projectCoordinator ? projectCoordinator : '-';
                            return projectCoordinator;
                        }
                    },
                    {
                        title: this.$t('project_engineer'),
                        type: 'custom-html',
                        key: 'managers',
                        isVisible: true,
                        modifier: (managers) => {
                            let projectManager = '';
                            managers.forEach((element, index) => {
                                projectManager += index > 0 ? ', ' + element.full_name : element.full_name;
                            });
                            projectManager = projectManager ? projectManager : '-';
                            return projectManager;
                        }
                    },
                    {
                        title: this.$t('start_date'),
                        type: 'text',
                        key: 'p_start_date',
                        isVisible: true,
                    },
                    {
                        title: this.$t('end_date'),
                        type: 'text',
                        key: 'p_end_date',
                        isVisible: true,
                    },
                    // {
                    //     title: this.$t('managers'),
                    //     type: 'text',
                    //     key: 'managers.full_name',
                    //     isVisible: true,
                    // },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    },
                ],
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    }
                    ,
                    {
                        title: this.$t('contractor'),
                        type: "multi-select-filter",
                        key: "contractors",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_projects')  && !!this.$can('view_contractors')

                    },
                    {
                        title: this.$t('location'),
                        type: "multi-select-filter",
                        key: "locations",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_projects')  && !!this.$can('view_locations')
                    },
                    {
                        title: this.$t('subdivision'),
                        type: "multi-select-filter",
                        key: "subdivisions",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_projects')  && !!this.$can('view_subdivisions')
                    },
                    {
                        title: this.$t('status'),
                        type: "multi-select-filter",
                        key: "status",
                        option: [],
                        listValueField: 'translated_name',
                        permission: !!this.$can('view_projects')  && !!this.$can('view_users')
                    },
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
                    {
                        title: this.$t('type'),
                        type: 'radio',
                        key: 'type',
                        // isVisible: true,
                        // "initValue": 2,
                        option: [
                            {id: '0', value: 'all'},
                            {id: '1', value: 'Sub Group'},
                            {id: '2', value: 'Group'},
                            {id: '3', value: 'Mixed'},
                            {id: '4', value: 'Normal'}


                        ],
                        "header": {
                            "title": 'filter projects record by type, sub group or main group',
                            // "description": 'You can filter your data table which are created based on segment'
                        },
                    },


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
                        modifier: row => this.$can('update_projects')
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
                        modifier: row => this.$can('delete_projects') &&
                            !(!!parseInt(row.is_default)) && !(!!parseInt(row.attendances_count))
                    },
                    {
                        title: this.$t('release parent'),
                        icon: 'unlock',
                        type: 'modal',
                        component: 'app-release-confirmation-modal',
                        modalId: 'app-release-confirmation-modal',
                        url: RELEASE_PARENT_PROJECT,
                        name: 'release',
                        modifier:( row) => this.$can('update_projects')
                    },
                    {
                        title: this.$addLabel('employee'),
                        icon: 'user',
                        type: 'modal',
                        name: 'add-employee',
                        modifier: row => this.$can('update_projects')
                    }
                    ,
                    {
                        title: this.$addLabel('project_engineer'),
                        icon: 'user',
                        type: 'modal',
                        name: 'add-manager',
                        modifier: row => this.$can('update_projects')
                    }
                    ,
                    {
                        title: this.$addLabel('working_shift'),
                        icon: 'user',
                        type: 'modal',
                        name: 'add-working-shifts',
                        modifier: row => this.$can('update_projects')
                    }
                    ,
                    {
                        title: this.$addLabel('gate_passes'),
                        icon: 'user',
                        type: 'modal',
                        name: 'add-gate-passes',
                        modifier: row => this.$can('update_projects')
                    }
                    ,
                    {
                        title: this.$addLabel('coordinator'),
                        icon: 'user',
                        type: 'modal',
                        name: 'add-coordinator',
                        modifier: row => this.$can('update_projects')
                    }
                    ,
                    {
                        title: this.$addLabel('geometry'),
                        icon: 'user',
                        type: 'modal',
                        name: 'add-geometry',
                        modifier: row => this.$can('update_projects')
                    }
                ],
            }
        }
    }
}
