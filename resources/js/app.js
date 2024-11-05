import "./bootstrap.js";
import { createApp } from 'vue';
import Router from './router.js';
import App from './components/App.vue';
import MainSidebar from "./components/sidebar/MainSidebar.vue";
import Login from "./components/Login/Login.vue";
import store from "./storage/index.js";

const app = createApp(App);

app.component('main-sidebar', MainSidebar);
app.component('login-component', Login);

app.config.globalProperties.$http = axios;

app.use(Router);
app.use(store);

app.mount('#app');
