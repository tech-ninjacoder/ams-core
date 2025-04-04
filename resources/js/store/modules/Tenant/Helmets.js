import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE_HELMET} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableHelmets({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_HELMET).then(({data}) => {
                commit('HELMET_LIST', data)
            });
        }
    }
}

const mutations = {
    HELMET_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
