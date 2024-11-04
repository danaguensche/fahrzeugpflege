import { createStore } from 'vuex'

export default createStore({
  state: {
    activeForm: 'login'
  },
  mutations: {
    setActiveForm(state, formName) {
      state.activeForm = formName
    }
  },
  actions: {
    switchForm({ commit }, formName) {
      commit('setActiveForm', formName)
    }
  }
})
