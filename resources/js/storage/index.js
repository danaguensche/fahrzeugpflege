import { createStore } from 'vuex'
import auth from "./auth.js";

export default createStore({
    state: {
        //Sidebar
        isSidebarOpen: false
    },

    modules: {
        auth
    },

    mutations: {
        //Sidebar
        toggleSidebar(state) {
            state.isSidebarOpen = !state.isSidebarOpen
        }
    },
})