import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_PROVIDER} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableProviders({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_PROVIDER).then(({data}) => {
                commit('PROVIDER_LIST', data)
            });
        }
    }
}

const mutations = {
    PROVIDER_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
