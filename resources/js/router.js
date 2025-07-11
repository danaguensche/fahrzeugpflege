import { createRouter, createWebHistory } from 'vue-router';
import CalendarPage from './components/pages/CalendarPage.vue';
import DashboardPage from './components/pages/DashboardPage.vue';
import CarsPage from './components/pages/CarsPage.vue';
import JobsPage from './components/pages/JobsPage.vue';
import ReportForm from './components/Reports/ReportForm.vue';
import ReportsPage from './components/pages/ReportsPage.vue';
import ProfilePage from './components/pages/ProfilePage.vue';
import SettingsPage from './components/pages/SettingsPage.vue';
import LoginPage from './components/pages/LoginPage.vue';
import SignUpPage from './components/pages/SignUpPage.vue';
import CustomerPage from './components/pages/CustomerPage.vue';
import CarDetailsPage from './components/pages/CarDetailsPage.vue';
import CustomerDetailsPage from './components/pages/CustomerDetailsPage.vue';
import JobsDetailsPage from './components/pages/JobsDetailsPage.vue';
import ForgotPassword from './components/Auth/ForgotPassword.vue';
import ResetPassword from './components/Auth/ResetPassword.vue';
import store from "./storage/index.js";

const routes = [
    {
        path: '/dashboard',
        component: DashboardPage,
        meta: { requiresAuth: true, title: 'Dashboard' }
    },
    {
        path: '/kalender',
        component: CalendarPage,
        meta: { requiresAuth: true, title: 'Kalender' }
    },
    {
        path: '/fahrzeuge',
        component: CarsPage,
        meta: { requiresAuth: true, title: 'Fahrzeuge' }
    },
    {
        path: '/fahrzeuge/fahrzeugdetails/:kennzeichen',
        component: CarDetailsPage,
        meta: { requiresAuth: true, title: 'Fahrzeugdetails' }
    },
    {
        path: '/kunden',
        component: CustomerPage,
        meta: { requiresAuth: true, title: 'Kunden' }
    },
    {
        path: '/kunden/kundendetails/:id',
        component: CustomerDetailsPage,
        meta: { requiresAuth: true, title: 'Kundendetails' }
    },
    {
        path: '/auftraege',
        component: JobsPage,
        meta: { requiresAuth: true, title: 'Aufträge' }
    },
    {
        path: '/auftraege/jobdetails/:id',
        component: JobsDetailsPage,
        meta: { requiresAuth: true, title: 'Jobdetails' }
    },
    {
        path: '/berichte/form/:formtype',
        component: ReportForm,
        meta: { requiresAuth: true, title: 'Berichtsformular' }
    },
    {
        path: '/berichte',
        component: ReportsPage,
        meta: { requiresAuth: true, title: 'Berichte' }
    },
    {
        path: '/profil',
        component: ProfilePage,
        meta: { requiresAuth: true, title: 'Profil' }
    },
    {
        path: '/einstellungen',
        component: SettingsPage,
        meta: { requiresAuth: true, title: 'Einstellungen' }
    },
    {
        path: '/login',
        component: LoginPage,
        meta: { title: 'Login' }
    },
    {
        path: '/signup',
        component: SignUpPage,
        meta: { title: 'Registrieren' }
    },
    {
        path: '/',
        redirect: '/dashboard',
        meta: { title: 'Dashboard' }
    },
    {
        path: '/forgot-password',
        component: ForgotPassword,
        meta: { title: 'Passwort vergessen' }
    },
    {
        path: '/reset-password/:token',
        component: ResetPassword,
        meta: { title: 'Passwort zurücksetzen' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = store.state.auth.isLoggedIn;
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

    if (requiresAuth && !isAuthenticated) {
        next('/login');
    } else if (isAuthenticated && (to.path === '/login' || to.path === '/signup' || to.path === '/')) {
        next('/dashboard');
    } else {
        next();
    }
});

router.afterEach((to) => {
    document.title = to.meta.title ? `${to.meta.title} - Fahrzeugpflege` : 'Fahrzeugpflege';
});

export default router;
