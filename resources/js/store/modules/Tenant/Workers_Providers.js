import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_WORKERS_PROVIDERS} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: [],
    called: false
}

const actions = {
    getSelectableWorkers_Providers({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_WORKERS_PROVIDERS).then(({data}) => {
                commit('WORKERS_PROVIDERS_LIST', data)
            });
        }
    }
}

const mutations = {
    WORKERS_PROVIDERS_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
