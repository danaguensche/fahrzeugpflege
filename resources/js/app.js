import "./bootstrap.js";
import { createApp } from 'vue';
import Router from './router.js';
import App from './components/App.vue';
import MainSidebar from "./components/sidebar/MainSidebar.vue";
import Login from "./components/Login/Login.vue";

const app = createApp(App);

app.component('main-sidebar', MainSidebar);
app.component('login-component', Login);

app.use(Router);

app.mount('#app');
