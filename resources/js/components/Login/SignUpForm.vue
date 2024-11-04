<template>
  <form class="forms-form" @submit.prevent="submitForm">
    <fieldset class="forms-fieldset">
      <div v-for="field in formFields" :key="field.id" class="forms-field">
        <input :type="field.type" :id="field.id" :placeholder="field.placeholder" v-model="formData[field.id]"
          class="forms-field-input" />
        <span v-if="errors[field.id]" class="error-message">{{ errors[field.id] }}</span>
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

      formData: {
        firstname: "",
        lastname: "",
        email: "",
        password: "",
        passwordRepeat: "",
      },

      buttonLabel: "Registrieren",

      errors: {},

      formFields: [
        { id: 'firstname', name: 'firstname', type: 'text', placeholder: 'Vorname *' },
        { id: 'lastname', name: 'lastname', type: 'text', placeholder: 'Nachname *' },
        { id: 'email', name: 'email', type: 'email', placeholder: 'Email *' },
        { id: 'password', name: 'password', type: 'password', placeholder: 'Passwort *' },
        { id: 'passwordRepeat', name: 'passwordRepeat', type: 'password', placeholder: 'Passwort wiederholen *' },
      ],

    };
  },
  methods: {

    submitForm() {
      this.errors = {};
      if (this.validateForm()) {
        console.log("Form Submitted", this.formData);
      }
    },

    validateForm() {
      const validators = {
        firstname: this.validateFirstname,
        lastname: this.validateLastname,
        email: this.validateEmail,
        password: this.validatePassword,
        passwordRepeat: this.validatePasswordRepeat,
      };

      return Object.keys(validators).every(field => validators[field]());
    },

    validateFirstname() {
      if (this.formData.firstname.length === 0) {
        this.errors.firstname = "Vorname ist erforderlich.";
        return false;
      }
      return true;
    },

    validateLastname() {
      if (this.formData.lastname.length === 0) {
        this.errors.lastname = "Nachname ist erforderlich.";
        return false;
      }
      return true;
    },

    validateEmail() {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(this.formData.email)) {
        this.errors.email = "Bitte geben Sie eine gültige Email-Adresse ein.";
        return false;
      }
      return true;
    },

    validatePassword() {
      if (this.formData.password.length < 8) {
        this.errors.password = "Das Passwort muss mindestens 8 Zeichen lang sein.";
        return false;
      }
      return true;
    },

    validatePasswordRepeat() {
      if (this.formData.password !== this.formData.passwordRepeat) {
        this.errors.passwordRepeat = "Die Passwörter stimmen nicht überein.";
        return false;
      }
      return true;
    }
  }
};
</script>