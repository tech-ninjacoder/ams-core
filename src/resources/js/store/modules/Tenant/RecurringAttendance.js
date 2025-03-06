import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_RECURRING_ATTENDANCE} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableRecurringAttendance({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_RECURRING_ATTENDANCE).then(({data}) => {
                commit('RECURRING_ATTENDANCE_LIST', data)
            });
        }
    }
}

const mutations = {
    RECURRING_ATTENDANCE_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
