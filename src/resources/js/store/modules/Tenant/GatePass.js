import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {TENANT_SELECTABLE_FILTER_GATE_PASS} from "../../../common/Config/apiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableGatePasses({commit}) {
        axiosGet(TENANT_SELECTABLE_FILTER_GATE_PASS).then(({data}) => {
            commit('GATE_PASS_LIST', data)
        });
    }
}

const mutations = {
    GATE_PASS_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
