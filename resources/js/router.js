import { createRouter, createWebHistory } from 'vue-router';
import CalenderPage from './components/pages/CalendarPage.vue';
import DashboardPage from './components/pages/DashboardPage.vue';
import CarsPage from './components/pages/CarsPage.vue';
import JobsPage from './components/pages/JobsPage.vue';
import ReportsPage from './components/pages/ReportsPage.vue';
import ChatPage from './components/pages/ChatPage.vue';
import ProfilePage from './components/pages/ProfilePage.vue';
import SettingsPage from './components/pages/SettingsPage.vue';
import LogoutPage from './components/pages/LogoutPage.vue';
import LoginPage from './components/pages/LoginPage.vue';
import SignUpPage from './components/pages/SignUpPage.vue';
import WelcomePage from './components/pages/WelcomePage.vue';

const routes = [
    {
        path: '/dashboard',
        component: DashboardPage,
    },
    {
        path: '/kalender',
        component: CalenderPage
    },
    {
        path: '/fahrzeuge',
        component: CarsPage
    },
    {
        path: '/auftraege',
        component: JobsPage
    },
    {
        path: '/berichte',
        component: ReportsPage
    },
    {
        path: '/chat',
        component: ChatPage
    },
    {
        path: '/profil',
        component: ProfilePage
    },
    {
        path: '/einstellungen',
        component: SettingsPage
    },
    {
        path: '/abmelden',
        component: LogoutPage
    },
    {
        path: '/login',
        component: LoginPage
    },
    {
        path: '/signup',
        component: SignUpPage
    },
    {
        path: '/',
        component: WelcomePage
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
