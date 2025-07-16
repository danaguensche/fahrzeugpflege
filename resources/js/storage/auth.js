export default {
  namespaced: true, // Add namespaced to avoid action/mutation name collisions
  state: {
    activeForm: 'login',
    isLoggedIn: !!localStorage.getItem('token'),
    token: localStorage.getItem('token'),
    userRole: localStorage.getItem('userRole') || null,
    userId: localStorage.getItem('userId') || null,
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
    setUserId(state, id) {
      state.userId = id;
      localStorage.setItem('userId', id);
    },
    clearToken(state) {
      state.token = null;
      localStorage.removeItem('token');
      state.userRole = null;
      localStorage.removeItem('userRole');
      state.userId = null;
      localStorage.removeItem('userId');
    }
  },
  actions: {
    switchForm({ commit }, formName) {
      commit('setActiveForm', formName);
    },
    login({ commit }, { token, role, userId }) {
      commit('setToken', token);
      commit('setUserRole', role);
      commit('setUserId', userId);
      commit('setLoggedIn', true);
    },
    logout({ commit }) {
      commit('clearToken');
      commit('setLoggedIn', false);
    },
    checkAuthStatus({ commit }) {
      const token = localStorage.getItem('token');
      const userRole = localStorage.getItem('userRole');
      const userId = localStorage.getItem('userId');
      if (token && userRole && userId) {
        commit('setToken', token);
        commit('setUserRole', userRole);
        commit('setUserId', userId);
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
};