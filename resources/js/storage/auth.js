export default {
    namespaced: true,
    state: {
        isLoggedIn: !!localStorage.getItem('token'),
        token: localStorage.getItem('token'),
        userRole: localStorage.getItem('userRole') || null,
        userId: localStorage.getItem('userId') || null,
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
    },
    actions: {
        login({ commit }, { token, role, id }) {
            commit('setToken', token);
            commit('setUserRole', role);
            commit('setUserId', id);
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
        },
    },
    getters: {
        isAdminOrTrainer: (state) => {
            return state.userRole === 'admin' || state.userRole === 'trainer';
        },
    },
};