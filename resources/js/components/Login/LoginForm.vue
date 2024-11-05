<template>
    <form @submit.prevent="submitForm" class="forms-form">
        <fieldset class="forms-fieldset">
            <div class="forms-field">
                <input v-model="formData.email" type="email" name="email" id="email" placeholder="Email"
                    class="forms-field-input" required autofocus />
            </div>
            <div class="forms-field">
                <input v-model="formData.password" type="password" name="password" id="password" placeholder="Passwort"
                    class="forms-field-input" required />
            </div>
        </fieldset>
        <div v-if="error" class="alert error">{{ error }}</div>
        <div class="forms-buttons">
            <a href="#" class="forms-buttons-forgot">Passwort vergessen?</a>
            <SubmitButton :buttonLabel="buttonLabel"></SubmitButton>
        </div>
    </form>
</template>

<script>
import SubmitButton from './Slots/SubmitButton.vue';
import axios from 'axios';

export default {
    name: "LoginForm",
    components: {
        SubmitButton,
    },
    data() {
        return {
            buttonLabel: "Anmelden",
            formData: {
                email: '',
                password: ''
            },
            error: null
        }
    },
    methods: {
        update_buttonLabel(newName) {
            this.buttonLabel = newName
        },
        async submitForm() {
            this.error = null;
            try {
                const response = await axios.post('/login', this.formData);
                if (response.data.success) {
                    // Erfolgreicher Login
                    console.log('Login erfolgreich', response.data);
                    // Weiterleitung zum Dashboard oder zur Hauptseite
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
