import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {TENANT_SELECTABLE_FILTER_SKILLS} from "../../../common/Config/apiUrl";

const state = {
    selectable: []
}

const actions = {
    getSelectableSkills({commit}) {
        axiosGet(TENANT_SELECTABLE_FILTER_SKILLS).then(({data}) => {
            commit('SKILL_LIST', data)
        });
    }
}

const mutations = {
    SKILL_LIST(state, data) {
        state.selectable = data;
    }
}

export default {
    state,
    actions,
    mutations
}
