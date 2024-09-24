import "./bootstrap.js";
import { createApp } from 'vue';
import Router from './router.js';
import App from './components/App.vue';
import MainSidebar from "./components/sidebar/MainSidebar.vue";

const app = createApp(App);
const sidebar = createApp(MainSidebar);

app.use(Router);

app.mount('#app');
sidebar.mount('#sidebar');
