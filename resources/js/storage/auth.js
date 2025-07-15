import { createStore } from "vuex";

export default createStore({
  state: {
    activeForm: 'login',
    isLoggedIn: !!localStorage.getItem('token'),
    token: localStorage.getItem('token')
  },
  mutations: {
    setActiveForm(state, formName) {
      state.activeForm = formName;
    },
    setLoggedIn(state, value) {
      state.isLoggedIn = value;
    },
    setToken(state, token) {
      state.token = token;
      localStorage.setItem('token', token);
    },
    clearToken(state) {
      state.token = null;
      localStorage.removeItem('token');
    }
  },
  actions: {
    switchForm({ commit }, formName) {
      commit('setActiveForm', formName);
    },
    login({ commit }, token) {
      commit('setToken', token);
      commit('setLoggedIn', true);
    },
    logout({ commit }) {
      commit('clearToken');
      commit('setLoggedIn', false);
    },
    checkAuthStatus({ commit }) {
      const token = localStorage.getItem('token');
      if (token) {
        commit('setToken', token);
        commit('setLoggedIn', true);
      } else {
        commit('setLoggedIn', false);
      }
    }
  },
  // getters: {
  //   isLoggedIn: state => state.isLoggedIn,
  //   getToken: state => state.token
  // }
});
