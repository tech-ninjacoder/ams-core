import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {PROJECTS, SELECTABLE_PROJECT} from "../../../tenant/Config/ApiUrl";

const state = {
    selectable: [],
    project: {}
}

const getters = {
    getProjectDetails: state => {
        return state.project
    }
};

const actions = {
    getSelectableProjects({commit, state}) {
        if (!Object.keys(state.selectable).length) {
            axiosGet(SELECTABLE_PROJECT).then(({data}) => {
                commit('PROJECT_LIST', data)
            });
        }
    },
    getProjectDetails({commit, state}, projectId){
        commit('SET_LOADER', true)
        axiosGet(`${PROJECTS}/${projectId}`).then(({ data }) =>{
            commit('PROJECT_DETAILS_DATA', data)
        }).finally(()=> commit('SET_LOADER', false))
    },
}

const mutations = {
    PROJECT_LIST(state, data) {
        state.selectable = data;
    },
    PROJECT_DETAILS_DATA(state, data){
        state.project = data;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
