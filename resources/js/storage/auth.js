export default {
  namespaced: true, // Add namespaced to avoid action/mutation name collisions
  state: {
    activeForm: 'login',
    isLoggedIn: !!localStorage.getItem('token'),
    token: localStorage.getItem('token'),
    userRole: localStorage.getItem('userRole') || null,
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
    setUserRole(state, role) {
      state.userRole = role;
      localStorage.setItem('userRole', role);
    },
    clearToken(state) {
      state.token = null;
      localStorage.removeItem('token');
      state.userRole = null;
      localStorage.removeItem('userRole');
    }
  },
  actions: {
    switchForm({ commit }, formName) {
      commit('setActiveForm', formName);
    },
    login({ commit }, { token, role }) {
      commit('setToken', token);
      commit('setUserRole', role);
      commit('setLoggedIn', true);
    },
    logout({ commit }) {
      commit('clearToken');
      commit('setLoggedIn', false);
    },
    checkAuthStatus({ commit }) {
      const token = localStorage.getItem('token');
      const userRole = localStorage.getItem('userRole');
      if (token && userRole) {
        commit('setToken', token);
        commit('setUserRole', userRole);
        commit('setLoggedIn', true);
      } else {
        commit('setLoggedIn', false);
      }
    }
  },
  getters: {
    isAdminOrTrainer: (state) => {
      return state.userRole === 'admin' || state.userRole === 'trainer';
    },
  },
};