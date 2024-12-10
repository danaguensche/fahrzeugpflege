<template>
  <form @submit.prevent="submitForm">
    <fieldset class="forms-fieldset">
      <div v-for="field in formFields" :key="field.id" class="forms-field">
        <input :type="field.type" :id="field.id" :placeholder="field.placeholder" v-model="formData[field.id]"
          class="forms-field-input" />
        <span class="alert error">
          {{ Array.isArray(errors[field.id]) ? errors[field.id][0] : errors[field.id] }}
        </span>
      </div>
    </fieldset>
    <div class="forms-buttons">
      <SubmitButton>Registrieren</SubmitButton>
    </div>
  </form>
</template>

<script>
import { ref } from 'vue';
import { useValidation } from '../../composables/useValidation.js';
import SubmitButton from './Slots/SubmitButton.vue';
import axios from 'axios';

export default {
  name: "SignUpForm",
  components: {
    SubmitButton,
  },
  data() {
    const initialData = {
      firstname: "",
      lastname: "",
      email: "",
      password: "",
      password_confirmation: "",
    };

    const { formData, errors, validateForm } = useValidation(initialData);
    const success = ref('');

    const formFields = [
      { id: 'firstname', name: 'Vorname *', type: 'text', placeholder: 'Vorname' },
      { id: 'lastname', name: 'Nachname *', type: 'text', placeholder: 'Nachname' },
      { id: 'email', name: 'E-Mail *', type: 'email', placeholder: 'Email' },
      { id: 'password', name: 'Passwort *', type: 'password', placeholder: 'Passwort' },
      { id: 'password_confirmation', name: 'Passwort wiederholen *', type: 'password', placeholder: 'Passwort wiederholen' },
    ];

    const submitForm = async () => {
      if (validateForm()) {
        try {
          const response = await axios.post('/signup', formData);
          if (response.data.success) {
            alert("Registrierung erfolgreich! Sie können sich jetzt anmelden.");
          } else {
            console.error('Unerwarteter Erfolgsfall:', response.data);
          }
        } catch (error) {
          if (error.response && error.response.data && error.response.data.errors) {
            errors.value = error.response.data.errors;
          } else {
            console.error('Registrierungsfehler:', error);
            errors.value.general = 'Ein Fehler ist bei der Registrierung aufgetreten. Bitte versuchen Sie es später erneut.';
          }
        }
      }
    };

    return {
      formData,
      errors,
      formFields,
      submitForm,
      success
    };
  }
};
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
</style>
