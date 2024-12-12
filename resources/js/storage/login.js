import { createStore } from "vuex";

export default loginStore({

    state: {
        activeForm: 'login'
    },
    mutations: {
        //Login
        setActiveForm(state, formName) {
            state.activeForm = formName
        },
    },

    actions: {
        switchForm({ commit }, formName) {
            commit('setActiveForm', formName)
        }
    }
});