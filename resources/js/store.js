import { createStore } from 'vuex'

export default createStore({
  state: {
    activeForm: null
  },
  mutations: {
    setActiveForm(state, formName) {
      state.activeForm = formName
    }
  },
  actions: {
    updateActiveForm({ commit }, formName) {
      commit('setActiveForm', formName)
    }
  }
})
