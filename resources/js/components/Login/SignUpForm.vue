<template>
    <form class="forms-form" @submit.prevent="submitForm">
        <fieldset class="forms-fieldset">
            <div class="forms-field">
                <input type="text" v-model="firstname" placeholder="Vorname" id="firstname" class="forms-field-input"
                    required />
                <span v-if="errors.firstname" class="error-message">{{ errors.firstname }}</span>
            </div>
            <div class="forms-field">
                <input type="text" v-model="lastname" placeholder="Nachname" id="lastname" class="forms-field-input"
                    required />
                <span v-if="errors.lastname" class="error-message">{{ errors.lastname }}</span>
            </div>
            <div class="forms-field">
                <input type="email" v-model="email" placeholder="Email" id="email" class="forms-field-input" required />
                <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
            </div>
            <div class="forms-field">
                <input type="password" v-model="password" placeholder="Passwort" id="password" class="forms-field-input"
                    required />
                <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
            </div>
            <div class="forms-field">
                <input type="password" v-model="passwordRepeat" placeholder="Passwort wiederholen" id="password-repeat"
                    class="forms-field-input" required />
                <span v-if="errors.passwordRepeat" class="error-message">{{ errors.passwordRepeat }}</span>
            </div>
        </fieldset>
        <div class="forms-buttons">
            <SubmitButton :buttonLabel="buttonLabel"></SubmitButton>
        </div>
    </form>
</template>

<script>
import SubmitButton from './Slots/SubmitButton.vue';

export default {
    name: "SignUpForm",
    components: {
        SubmitButton,
    },

    data() {
        return {
            firstname: "",
            lastname: "",
            email: "",
            password: "",
            passwordRepeat: "",
            buttonLabel: "Registrieren",
            errors: {}
        };
    },

    methods: {
        submitForm() {
            this.errors = {};
            if (this.validateForm()) {
                console.log("Form Submitted", {
                    firstname: this.firstname,
                    lastname: this.lastname,
                    email: this.email,
                    password: this.password,
                    passwordRepeat: this.passwordRepeat,
                });
            }
        },

        validateForm() {
            const isValid =
                this.validateFirstname() &
                this.validateLastname() &
                this.validateEmail() &
                this.validatePassword() &
                this.validatePasswordRepeat();

            return !!isValid;
        },

        validateFirstname() {
            if (this.firstname.length === 0) {
                this.errors.firstname = "Vorname ist erforderlich.";
                return false;
            }
            return true;
        },

        validateLastname() {
            if (this.lastname.length === 0) {
                this.errors.lastname = "Nachname ist erforderlich.";
                return false;
            }
            return true;
        },

        validateEmail() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(this.email)) {
                this.errors.email = "Bitte geben Sie eine gültige Email-Adresse ein.";
                return false;
            }
            return true;
        },

        validatePassword() {
            if (this.password.length < 8) {
                this.errors.password = "Das Passwort muss mindestens 8 Zeichen lang sein.";
                return false;
            }
            return true;
        },

        validatePasswordRepeat() {
            if (this.password !== this.passwordRepeat) {
                this.errors.passwordRepeat = "Die Passwörter stimmen nicht überein.";
                return false;
            }
            return true;
        }
    }
};
</script>