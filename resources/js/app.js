import "./bootstrap.js";
import { createApp } from 'vue';
import Router from './router.js';
import App from './components/App.vue';
import MainSidebar from "./components/sidebar/MainSidebar.vue";
import Login from "./components/Login/Login.vue";
import AlertBase from "./components/Alerts/alertBase.vue";
import store from "./storage/index.js";
import axios from 'axios';

//axios
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Accept'] = 'application/json';




const app = createApp(App);

app.component('main-sidebar', MainSidebar);
app.component('login-component', Login);
app.component('alert-base', AlertBase)

app.config.globalProperties.$http = axios;

app.use(Router);
app.use(store);

app.mount('#app');


