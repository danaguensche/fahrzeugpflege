// store/auth.js
export default {

    namespaced: true,

    state: {
        isLoggedIn: !!localStorage.getItem('token'),
        token: localStorage.getItem('token'),
        userRole: localStorage.getItem('userRole') || null,
        userId: localStorage.getItem('userId') || null,
        initialized: false,
    },
    mutations: {

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
        },

        setInitialized(state, value) {
            state.initialized = value;
        },
    },

    actions: {

        login({ commit }, { token, role, id }) {
            commit('setToken', token);
            commit('setUserRole', role);
            commit('setUserId', id);
            commit('setLoggedIn', true);
            commit('setInitialized', true);
        },

        logout({ commit }) {
            commit('clearToken');
            commit('setLoggedIn', false);
            commit('setInitialized', true);
        },

        initializeAuth({ commit, state }) {
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
            commit('setInitialized', true);
        },
    },

    getters: {
        isAdminOrTrainer: (state) => {
            return state.userRole === 'admin' || state.userRole === 'trainer';
        },
        isAuthenticated: (state) => state.isLoggedIn && !!state.token,
        initialized: (state) => state.initialized,
    },
};