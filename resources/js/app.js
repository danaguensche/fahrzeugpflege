import "./bootstrap.js";
import { createApp } from 'vue';
import Router from './router.js';
import App from './components/App.vue';

const app = createApp(App);

app.use(Router);
// uwu

app.mount('#app');
