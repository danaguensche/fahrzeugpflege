<template>

    <!-- submit prevent verhindert, dass die Seite neu geladen wird, wenn das Formular abgeschickt wird -->

    <form @submit.prevent="submitForm">
        <fieldset class="forms-fieldset">
            <div class="forms-field">
                <input type="hidden" name="_token" :value="csrf_token" />
                <input v-model="formData.email" type="email" name="email" id="email" placeholder="Email"
                    class="forms-field-input" required autofocus />
            </div>
            <div class="forms-field">
                <input v-model="formData.password" type="password" name="password" id="password" placeholder="Passwort"
                    class="forms-field-input" required />
            </div>
        </fieldset>
        <div v-if="error" class="alert error">{{ error }}</div>
        <div class="actions">
            <v-checkbox label="Anmeldedaten speichern"></v-checkbox>
            <div class="forms-buttons-forgot">
                <router-link to="/forgot-password" class="forms-buttons-forgot">Passwort vergessen?</router-link>
            </div>
        </div>
        <SubmitButton>Anmelden</SubmitButton>
    </form>
</template>

<script>
import axios from 'axios';
import SubmitButton from './Slots/SubmitButton.vue';

export default {
    name: "LoginForm",
    components: {
        SubmitButton,
    },
    data() {
        return {
            formData: {
                email: '',
                password: ''
            },
            error: null,
            csrf_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    },
    methods: {
        // Wird beim Absenden des Formulars aufgerufen
        async submitForm() {
            this.error = null;
            try {
                const response = await axios.post('/login', this.formData);
                if (response.data.success) {
                    console.log('Login erfolgreich', response.data);
                    console.log('Received token:', response.data.token);
                    console.log('Received user role:', response.data.user.role);

                    localStorage.setItem('token', response.data.token);
                    localStorage.setItem('userRole', response.data.user.role); // Ensure userRole is explicitly set in localStorage

                    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                    this.$store.dispatch('auth/login', { token: response.data.token, role: response.data.user.role });

                    console.log('Token stored in localStorage:', localStorage.getItem('token'));
                    console.log('User role stored in localStorage:', localStorage.getItem('userRole'));

                    window.location.href = response.data.redirect;
                }
            } catch (error) {
                if (error.response && error.response.data) {
                    this.error = error.response.data.message;
                } else {
                    console.error('Login-Fehler:', error);
                    this.error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
                }
            }
        }
    }
}
</script>

<style>
/* Formular-Titel */
.forms-title {
    margin-bottom: 3.125rem;
    font-size: 2rem;
    font-weight: 500;
    color: var(--primary-color);
    text-transform: uppercase;
    letter-spacing: 0.15rem;
    text-align: center;
}

/* Formular-Feld */
.forms-field:not(:last-of-type) {
    margin-bottom: 1.875rem;
}

.forms-field-input {
    width: 100%;
    border: 1px solid #ccc;
    border-radius: var(--border-radius);
    padding: 0.75rem;
    font-size: 1rem;
    font-weight: 300;
    color: var(--text-color-dark);
    transition: border-color var(--transition-speed);
}

.forms-field-input:focus {
    border-color: var(--primary-color);
}

/* Passwort vergessen */
.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.forms-buttons-forgot {
    color: #999;
    text-decoration: underline;
    transition: color var(--transition-speed);
}

.forms-buttons-forgot:hover {
    color: #666;
    cursor: pointer;
}
</style>
