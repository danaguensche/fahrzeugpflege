<template>
  <form @submit.prevent="submitForm">
    <fieldset v-for="field in formFields" :key="field.id" class="forms-field">
      <input class="forms-field-input" v-model="formData[field.id]" :type="field.type" :id="field.id"
        :placeholder="field.placeholder" />
      <span class="alert error" v-if="errors[field.id]">
        {{ Array.isArray(errors[field.id]) ? errors[field.id][0] : errors[field.id] }}
      </span>
    </fieldset>
    <div class="forms-buttons">
      <SubmitButton>Registrieren</SubmitButton>
    </div>
  </form>
  <VuetifyAlert v-model="alertVisible" maxWidth="500" alertTypeClass="alertTypeDefault"
    alertHeading="Registrierung erfolgreich!"
    alertParagraph="Sie haben sich erfolgreich registriert. Sie können sich jetzt anmelden." alertCloseButton="Okay">
  </VuetifyAlert>
</template>

<script>
import { ref } from 'vue';
import { mapGetters } from 'vuex';
import { useValidation } from '../../composables/useValidation.js';
import SubmitButton from './Slots/SubmitButton.vue';
import axios from 'axios';
import VuetifyAlert from '../Alerts/VuetifyAlert.vue';

export default {
  name: "SignUpForm",
  components: {
    SubmitButton,
    VuetifyAlert
  },
  data() {
    return {
      formData: {
        firstname: "",
        lastname: "",
        email: "",
        password: "",
        password_confirmation: "",
      },
      errors: {},
      alertVisible: false,
      formFields: [
        { id: 'firstname', name: 'Vorname *', type: 'text', placeholder: 'Vorname' },
        { id: 'lastname', name: 'Nachname *', type: 'text', placeholder: 'Nachname' },
        { id: 'email', name: 'E-Mail *', type: 'email', placeholder: 'Email' },
        { id: 'password', name: 'Passwort *', type: 'password', placeholder: 'Passwort' },
        { id: 'password_confirmation', name: 'Passwort wiederholen *', type: 'password', placeholder: 'Passwort wiederholen' },
      ],
      success: '',
    };
  },

  computed: {
    ...mapGetters('auth', ['isLoggedIn', 'getToken'])
  },
  methods: {
    async submitForm() {
      const { validateForm } = useValidation(this.formData);
      if (validateForm()) {
        try {
          const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          const response = await axios.post('/signup', this.formData, {
            headers: {
              'X-CSRF-TOKEN': csrfToken,
            },
          });
          if (response.data.success) {
            this.alertVisible = true;
          } else {
            console.error('Unerwarteter Erfolgsfall:', response.data);
          }
        } catch (error) {
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors;
          } else {
            console.error('Registrierungsfehler:', error);
            this.errors.general = 'Ein Fehler ist bei der Registrierung aufgetreten. Bitte versuchen Sie es später erneut.';
          }
        }
      }
    },
    clearSearch() {
      this.searchText = "";
    }
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
