import Vue from 'vue'
import Vuex from 'vuex'
import {formatted_date, formatted_time} from '../common/Helper/Support/DateTimeHelper'

//Import you module here
import Support from "./modules/Common/Support";
import Notifications from "./modules/Common/Notifications";
import Notification from "./modules/Common/Settings/Notification";
import User from "./modules/Common/User";
import NotificationEvent from "./modules/Common/Settings/NotificationEvent";
import Settings from "./modules/Common/Settings/Settings";
import Role from "./modules/Common/Role";
import CustomField from "./modules/Common/Settings/CustomField/CustomField";
import Profile from "./modules/Common/Profile";
import TenantSettings from "./modules/Tenant/TenantSettings";
import Department from "./modules/Tenant/Departments";
import Departments from "./modules/Tenant/Departments";
import Designations from "./modules/Tenant/Designations";
import Providers from "./modules/Tenant/Providers";
import Helmets from "./modules/Tenant/Helmets";
import RecurringAttendance from "./modules/Tenant/RecurringAttendance";

import WorkingShifts from "./modules/Tenant/WorkingShift";
import Roles from "./modules/Tenant/Roles";
import Skills from "./modules/Tenant/Skills";
import Project from "./modules/Tenant/Project";
import EmploymentStatuses from "./modules/Tenant/EmployeeStatuses";
import Employees from "./modules/Tenant/Employees";
import CalendarFilter from "./modules/Tenant/CalendarFilter";
import Payrun from "./modules/Tenant/Payrun";
import GatePass from "./modules/Tenant/GatePass";

Vue.use(Vuex);

export default new Vuex.Store({

    state: {
        loading: false,
        settings: {
            dateFormat: formatted_date(),
            timeFormat: parseInt(formatted_time())
        },
        theme: {
            darkMode: false
        }
    },

    getters: {},

    actions: {
        setLoader({commit}, loading) {
            commit('SET_LOADER', loading)
        }
    },

    mutations: {
        SET_LOADER(state) {
            state.loading = !state.loading;
        }
    },

    modules: {
        support: Support,
        notifications: Notifications,
        user: User,
        notification: Notification,
        notification_event: NotificationEvent,
        settings: Settings,
        role: Role,
        custom_field: CustomField,
        profile: Profile,
        tenant_settings: TenantSettings,
        department: Department,
        departments: Departments,
        designations: Designations,
        providers: Providers,
        helmets: Helmets,
        recurring_attendance: RecurringAttendance,
        working_shifts: WorkingShifts,
        roles: Roles,
        skills: Skills,
        employment_statuses: EmploymentStatuses,
        employees: Employees,
        calendar: CalendarFilter,
        payrun: Payrun,
        projects: Project,
        gate_passes: GatePass
    }
});
