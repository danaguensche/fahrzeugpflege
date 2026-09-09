import { createRouter, createWebHistory } from 'vue-router';
import MainLayout from './components/layouts/MainLayout.vue';
import CalendarComponent from './components/Calendar/CalendarComponent.vue';
import DashboardPage from './components/pages/DashboardPage.vue';
import CarsPage from './components/pages/CarsPage.vue';
import JobsPage from './components/pages/JobsPage.vue';
import ProfilePage from './components/pages/ProfilePage.vue';
import SettingsPage from './components/pages/SettingsPage.vue';
import LoginPage from './components/pages/LoginPage.vue';
import WelcomePage from './components/pages/WelcomePage.vue';
import CustomerPage from './components/pages/CustomerPage.vue';
import CarDetailsPage from './components/pages/CarDetailsPage.vue';
import CustomerDetailsPage from './components/pages/CustomerDetailsPage.vue';
import JobsDetailsPage from './components/pages/JobsDetailsPage.vue';
import ForgotPassword from './components/Auth/ForgotPassword.vue';
import ResetPassword from './components/Auth/ResetPassword.vue';

const routes = [{
        path: '/',
        component: WelcomePage,
        name: 'Welcome'
    },
    {
        path: '/login',
        component: LoginPage,
        name: 'Login'
    },
    {
        path: '/',
        component: MainLayout,
        children: [{
                path: 'dashboard',
                component: DashboardPage,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'kalender',
                component: CalendarComponent,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'fahrzeuge',
                component: CarsPage,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'fahrzeuge/fahrzeugdetails/:kennzeichen',
                component: CarDetailsPage,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'kunden',
                component: CustomerPage,
                meta: { roles: ['trainer', 'admin'] }
            },
            {
                path: 'kunden/kundendetails/:id',
                component: CustomerDetailsPage,
                meta: { roles: ['trainer', 'admin'] }
            },
            {
                path: 'auftraege',
                component: JobsPage,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'auftraege/jobdetails/:id',
                component: JobsDetailsPage,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'profil',
                component: ProfilePage,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'einstellungen',
                component: SettingsPage,
                meta: { roles: ['trainer', 'admin'] }
            },
            {
                path: 'benutzer',
                component: () =>
                    import ('./components/pages/UsersPage.vue'),
                meta: { roles: ['admin'] }
            },
        ],
        meta: { requiresAuth: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const userRole = localStorage.getItem('userRole');

    const isLoggedIn = !!token;

    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const requiredRoles = to.meta.roles;

    if (requiresAuth) {
        if (!isLoggedIn) {
            console.warn('Redirect to login (not authenticated)');
            return next({
                name: 'Login',
                query: to.path !== '/' ? { redirect: to.fullPath } : {},
            });
        }

        if (requiredRoles) {
            if (!userRole || !requiredRoles.includes(userRole)) {
                console.warn('Redirect to dashboard (invalid role)');
                return next('/dashboard');
            }
        }
        return next();
    }

    const isPublicRoot = to.path === '/' || to.path === '/login';
    if (isPublicRoot && isLoggedIn) {
        return next('/dashboard');
    }

    return next();
});

export default router;