<template>
    <section class="login-page">
        <div :class="['user-options-forms-registered', { 'active': activeForm === 'login' }]">
            <h2 class="forms-title">Anmelden</h2>
            <LoginForm></LoginForm>
        </div>
        <div :class="['user-options-forms-unregistered', { 'active': activeForm === 'register' }]">
            <h2 class="forms-title">Registrieren</h2>
        </div>
    </section>
</template>

<script>
import LoginForm from './LoginForm.vue';

import { mapState } from 'vuex';
import SignUpForm from './SignUpForm.vue';

export default {
    name: "Login",
    components: {
        LoginForm,
        SignUpForm

    },
    computed: {
        ...mapState(['activeForm']),
        isLoginActive() {
            return this.activeForm === 'login';
        },
        isRegisterActive() {
            return this.activeForm === 'register';
        }
    }
}
</script>

<style>
::selection {
    background-color: #B4D5FF
}


.general {
    font-family: var(--font-family);
    font-size: 1rem;
    line-height: 1.6;
    background-color: var(--background-color);
    color: var(--text-color-dark);
    padding: 2rem 1rem;
}

/* Login-Seite Layout */
.login-page {

    display: flex;
    flex-direction: column;
    justify-content: top-center;
    align-items: center;
    position: relative;
    width: var(--box-width);
    margin: 0 auto;
}

/* Benutzerbereich-Styles */
.user-options-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: var(--box-width);
    margin: 0 auto;
}

/* Benutzeroptionen Text-Bereich */
.user-options-text {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background-color: var(--primary-color);
    padding: 1.5rem;
    width: 100%;
    box-shadow: var(--box-shadow);
}

/* Benutzeroptionen */
.user-options {
    width: 100%;
    color: var(--text-color-light);
    font-weight: 300;
    text-align: center;
}

/* Formular-Bereich */
.user-options-forms-registered,
.user-options-forms-unregistered {
    position: absolute;

    width: 100%;
    background-color: var(--text-color-light);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 3rem;
    transition: all var(--transition-speed) ease;
    opacity: 0;
    visibility: hidden;
    padding: 3rem;

}

.user-options-forms-registered.active,
.user-options-forms-unregistered.active {
    opacity: 1;
    visibility: visible;
    z-index: 1;
}


.alert.error {
    color: red;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.alert.success {
    color: green;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}


/* Medienabfragen für Responsive Design */
@media screen and (max-width: 768px) {
    .user-options-container {
        width: 100%;
    }

    .user-options-text,
    .user-options-forms-registered,
    .user-options-forms-unregistered {
        width: 100%;
    }

    .user-action {
        width: 200px;
    }

    .login-page {
        width: 100%;
    }

    .user-options-forms-registered,
    .user-options-forms-unregistered {
        width: 100%;
    }
}
</style>