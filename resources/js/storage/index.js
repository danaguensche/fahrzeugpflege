import { createStore } from 'vuex'
import auth from "./auth.js";

export default createStore({
  state: {
    //Login
    activeForm: 'login',

    //Sidebar
    isSidebarOpen: false
  },

  modules: {
    auth
  },
  
  mutations: {
    //Login
    setActiveForm(state, formName) {
      state.activeForm = formName
    },

    //Sidebar
    toggleSidebar(state){
      state.isSidebarOpen = !state.isSidebarOpen
    }
  },
  actions: {
    switchForm({ commit }, formName) {
      commit('setActiveForm', formName)
    }
  }
})
