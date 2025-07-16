import "./bootstrap.js";
import { createApp } from 'vue';
import Router from './router.js';
import App from './components/App.vue';
import store from "./storage/index.js";
import axios from 'axios';
import vuetify from '../plugins/vuetify.js';
import "vuetify/styles";
import '@mdi/font/css/materialdesignicons.css';

// axios Konfiguration
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Accept'] = 'application/json';

// Watch for changes in the token and update Authorization header
store.watch(
    (state) => state.auth.token,
    (newToken) => {
        if (newToken) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
        } else {
            delete axios.defaults.headers.common['Authorization'];
        }
    },
    { immediate: true } // Run immediately to set the initial token
);

// Axios Interceptor for error handling
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Handle unauthorized access, e.g., redirect to login
            console.error('Unauthorized access. Redirecting to login.');
            store.dispatch('auth/logout'); // Clear token and user data
            Router.push('/login'); // Redirect to login page
        }
        return Promise.reject(error);
    }
);

const app = createApp(App);

app.config.globalProperties.$http = axios;

app.use(Router);
app.use(store);
app.use(vuetify);

// Ensure auth status is checked before mounting the app
store.dispatch('auth/checkAuthStatus').then(() => {
    app.mount('#app');
});