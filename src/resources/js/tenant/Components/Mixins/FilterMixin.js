// Department Filter
import {collection} from "../../../common/Helper/helpers";
import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {GATE_PASSE, SELECTABLE_PROJECT_STATUS, TENANT_SELECTABLE_MANAGER} from "../../Config/ApiUrl";

export const DepartmentFilterMixin = {
    computed: {
        departments() {
            return this.$store.state.departments.selectable;
        }
    },

    watch: {
        'departments.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'departments') option.push(...this.departments)
                })
            },
            immediate: true
        }
    },

    created() {
        let filter = collection(this.options.filters?.filter(filter => filter.key === 'departments')).first();
        if (this.$can('view_departments')) {
            this.$store.dispatch('getSelectableDepartments', filter.manager ? filter.manager : false)
        }
    }
};

// Designation Filter
export const DesignationFilterMixin = {
    computed: {
        designations() {
            return this.$store.state.designations.selectable;
        }
    },

    watch: {
        'designations.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'designations') option.push(...this.designations)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_designations')) {
            this.$store.dispatch('getSelectableDesignations')
        }
    }
};

// Provider Filter
export const ProviderFilterMixin = {
    computed: {
        providers() {
            return this.$store.state.providers.selectable;
        }
    },

    watch: {
        'providers.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'providers') option.push(...this.providers)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_designations')) {
            this.$store.dispatch('getSelectableProviders')
        }
    }
};

// Helmet Filter
export const HelmetFilterMixin = {
    computed: {
        helmets() {
            return this.$store.state.helmets.selectable;
        }
    },

    watch: {
        'helmets.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'helmets') option.push(...this.helmets)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_designations')) {
            this.$store.dispatch('getSelectableHelmets')
        }
    }
};

// Recurring Attendance Filter
export const RecurringAttendanceFilterMixin = {
    computed: {
        recurring_attendance() {
            return this.$store.state.recurring_attendance.selectable;
        }
    },

    watch: {
        'recurring_attendance.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'recurring_attendance') option.push(...this.recurring_attendance)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_designations')) {
            this.$store.dispatch('getSelectableRecurringAttendance')
        }
    }
};

// WorkingShift Filter
export const WorkingShiftFilterMixin = {
    computed: {
        workingShifts() {
            return this.$store.state.working_shifts.selectable;
        }
    },

    watch: {
        'workingShifts.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'working_shifts') option.push(...this.workingShifts)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_working_shifts')) {
            this.$store.dispatch('getSelectableWorkingShifts')
        }
    }
};

//Roles Filter
export const RoleFilterMixin = {
    computed: {
        roles() {
            return this.$store.state.roles.selectable;
        }
    },

    watch: {
        'roles.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'roles') option.push(...this.roles)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_roles')) {
            this.$store.dispatch('getSelectableRoles', {alias: 'tenant'})
        }
    }
};

//Skills Filter
export const SkillFilterMixin = {
    computed: {
        skills() {
            return this.$store.state.skills.selectable;
        }
    },

    watch: {
        'skills.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'skills') option.push(...this.skills)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_roles')) {
            this.$store.dispatch('getSelectableSkills')
        }
    }
};

//Skills Filter
export const GatePassFilterMixin = {
    computed: {
        GatePasse() {
            return this.$store.state.gate_passes.selectable;
        }
    },

    watch: {
        'GatePasse.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'gate_passes') option.push(...this.GatePasse)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_roles')) {
            this.$store.dispatch('getSelectableGatePasses')
        }
    }
};

// Projects Filter
export const ProjectFilterMixin = {
    computed: {
        projects() {
            return this.$store.state.projects.selectable;
        }
    },

    watch: {
        'projects.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'projects') option.push(...this.projects)
                })
            },
            immediate: true
        }
    },


    created() {
        if (this.$can('view_designations')) {
            this.$store.dispatch('getSelectableProjects')
        }
    }
};
//Employment Status Filter
export const EmploymentStatusFilterMixin = {
    computed: {
        employmentStatuses() {
            return this.$store.state.employment_statuses.selectable;
        }
    },

    watch: {
        'employmentStatuses.length': {
            handler: function (length) {
                this.options.filters.find(({key, option}) => {
                    if (key === 'employment_statuses') option.push(...this.employmentStatuses)
                })
            },
            immediate: true
        }
    },

    created() {
        if (this.$can('view_employment_statuses')) {
            this.$store.dispatch('getSelectableEmploymentStatuses')
        }
    }
};


//Users Filter
export const UserFilterMixin = {
    methods:{
        getUsers(){
            axiosGet(this.apiUrl.TENANT_SELECTABLE_USER).then(({data})=>{
                this.options.filters.find(fl => fl.key === 'users' && fl.type === 'multi-select-filter').option = data;
            })
        }
    },
    created() {
        this.options.filters.find(fl => fl.key === 'users' && fl.type === 'multi-select-filter').permission ?
        this.getUsers() : null;
    }
};

export const ContractorFilterMixin = {
    methods:{
        getContractors(){
            axiosGet(this.apiUrl.TENANT_SELECTABLE_CONTRACTOR).then(({data})=>{
                this.options.filters.find(fl => fl.key === 'contractors' && fl.type === 'multi-select-filter').option = data;
            })
        }
    },
    created() {
        this.options.filters.find(fl => fl.key === 'contractors' && fl.type === 'multi-select-filter').permission ?
            this.getContractors() : null;
    }
};

export const LocationFilterMixin = {
    methods:{
        getLocations(){
            axiosGet(this.apiUrl.TENANT_SELECTABLE_LOCATION).then(({data})=>{
                this.options.filters.find(fl => fl.key === 'locations' && fl.type === 'multi-select-filter').option = data;
            })
        }
    },
    created() {
        this.options.filters.find(fl => fl.key === 'locations' && fl.type === 'multi-select-filter').permission ?
            this.getLocations() : null;
    }
};

export const SubdivisionFilterMixin = {
    methods:{
        getSubdivisions(){
            axiosGet(this.apiUrl.TENANT_SELECTABLE_SUBDIVISION).then(({data})=>{
                this.options.filters.find(fl => fl.key === 'subdivisions' && fl.type === 'multi-select-filter').option = data;
            })
        }
    },
    created() {
        this.options.filters.find(fl => fl.key === 'subdivisions' && fl.type === 'multi-select-filter').permission ?
            this.getSubdivisions() : null;
    }
};
export const ProjectStatusFilterMixin = {
    methods:{
        getUsers(){
            axiosGet(this.apiUrl.SELECTABLE_PROJECT_STATUS).then(({data})=>{
                this.options.filters.find(fl => fl.key === 'status' && fl.type === 'multi-select-filter').option = data;
            })
        }
    },
    created() {
        this.options.filters.find(fl => fl.key === 'status' && fl.type === 'multi-select-filter').permission ?
            this.getUsers() : null;
    }
};
