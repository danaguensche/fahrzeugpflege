import { createRouter, createWebHistory } from 'vue-router';
import FirstPage from './components/pages/FirstPage.vue';

const routes = [
    {
        path: '/my-new-vue-route',
        component: FirstPage
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
