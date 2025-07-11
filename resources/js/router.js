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
import WelcomePage from './components/pages/WelcomePage.vue';
import CustomerPage from './components/pages/CustomerPage.vue';
import CarDetailsPage from './components/pages/CarDetailsPage.vue';
import CustomerDetailsPage from './components/pages/CustomerDetailsPage.vue';
import JobsDetailsPage from './components/pages/JobsDetailsPage.vue';
import ForgotPassword from './components/Auth/ForgotPassword.vue';
import ResetPassword from './components/Auth/ResetPassword.vue';

const routes = [
    {
        path: '/dashboard',
        component: DashboardPage,
    },
    {
        path: '/kalender',
        component: CalendarPage
    },
    {
        path: '/fahrzeuge',
        component: CarsPage
    },
    {
        path: '/fahrzeuge/fahrzeugdetails/:kennzeichen',
        component: CarDetailsPage
    },
    {
        path: '/kunden',
        component: CustomerPage
    },
    {
        path: '/kunden/kundendetails/:id',
        component: CustomerDetailsPage
    },

    {
        path: '/auftraege',
        component: JobsPage
    },
    {
        path: '/auftraege/jobdetails/:id',
        component: JobsDetailsPage
    },
    {
        path: '/berichte/form/:formtype',
        component: ReportForm
    },
    {
        path: '/berichte',
        component: ReportsPage
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
    },
    {
        path: '/forgot-password',
        component: ForgotPassword
    },
    {
        path: '/reset-password/:token',
        component: ResetPassword
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
