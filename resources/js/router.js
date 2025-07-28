import { createRouter, createWebHistory } from 'vue-router';
import MainLayout from './components/layouts/MainLayout.vue';
import CalendarComponent from './components/Calendar/CalendarComponent.vue';
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
import store from './storage/index.js'; // Import your Vuex store

const routes = [
    {
        path: '/',
        component: WelcomePage, // Public landing page
        name: 'Welcome'
    },
    {
        path: '/login',
        component: LoginPage,
        name: 'Login'
    },
    {
        path: '/signup',
        component: SignUpPage,
        name: 'SignUp'
    },
    {
        path: '/', // This is a parent route for authenticated sections
        component: MainLayout,
        children: [
            {
                path: 'dashboard', // /dashboard
                component: DashboardPage,
                meta: { roles: ['trainee', 'trainer', 'admin'] }
            },
            {
                path: 'kalender', // /kalender
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
                path: 'berichte/form/:formtype',
                component: ReportForm,
                meta: { roles: ['trainer', 'admin'] }
            },
            {
                path: 'berichte',
                component: ReportsPage,
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
            // Add a route for 'Benutzer' (Users) page, visible only to admin
            {
                path: 'benutzer',
                component: () => import('./components/pages/UsersPage.vue'), // Assuming you will create this component
                meta: { roles: ['admin'] }
            },
        ],
        meta: { requiresAuth: true } // All children of this parent route require authentication
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

router.beforeEach((to, from, next) => {
    const isLoggedIn = store.state.auth.isLoggedIn;
    const userRole = store.state.auth.userRole;

    console.log(`Navigating to: ${to.path}`);
    console.log(`isLoggedIn: ${isLoggedIn}, userRole: ${userRole}`);

    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const requiredRoles = to.meta.roles;

    // 1. If the user is trying to access a route that requires authentication
    if (requiresAuth) {
        if (!isLoggedIn) {
            // If not logged in, redirect to login
            return next('/login');
        } else {
            // Logged in, now check roles
            if (requiredRoles) {
                // If roles are required for this route
                if (!userRole || !requiredRoles.includes(userRole)) {
                    // If user has no valid role, or role is not allowed, redirect to login
                    return next('/login');
                } else {
                    // User is logged in and has the required role
                    return next();
                }
            } else {
                // No specific roles required for this authenticated route, allow access
                return next();
            }
        }
    }
    // 2. If the user is logged in and tries to access login/signup/welcome page
    else if ((to.path === '/login' || to.path === '/signup' || to.path === '/') && isLoggedIn) {
        return next('/dashboard');
    }
    // 3. For all other cases (public routes, or authenticated users on allowed routes)
    else {
        return next();
    }
});

export default router;
