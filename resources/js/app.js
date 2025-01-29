import "./bootstrap.js";
import { createApp } from 'vue';
import Router from './router.js';
import App from './components/App.vue';
import AlertBase from "./components/Alerts/alertBase.vue";
import store from "./storage/index.js";
import axios from 'axios';
import vuetify from '../plugins/vuetify.js';
import "vuetify/styles";
import '@mdi/font/css/materialdesignicons.css';

// axios Konfiguration
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Accept'] = 'application/json';

const token = localStorage.getItem('apiToken'); 
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);


app.component('alert-base', AlertBase);

app.config.globalProperties.$http = axios;

app.use(Router);
app.use(store);
app.use(vuetify);

app.mount('#app');
