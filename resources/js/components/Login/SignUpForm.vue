<template>
  <!-- submit prevent verhindert, dass die Seite neu geladen wird, wenn das Formular abgeschickt wird -->
  <form @submit.prevent="submitForm">
    <fieldset class="forms-fieldset">
      <div v-for="field in formFields" :key="field.id" class="forms-field">
        <input :type="field.type" :id="field.id" :placeholder="field.placeholder" v-model="formData[field.id]"
          class="forms-field-input" />
        <span class="alert error">
          <!-- Wenn Fehlermeldung ein Array ist, wird nur das erste Element des Arrays angezeigt -->
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
        password_confirmation: "",
      },

      errors: {},

      formFields: [
        { id: 'firstname', name: 'firstname', type: 'text', placeholder: 'Vorname *' },
        { id: 'lastname', name: 'lastname', type: 'text', placeholder: 'Nachname *' },
        { id: 'email', name: 'email', type: 'email', placeholder: 'Email *' },
        { id: 'password', name: 'password', type: 'password', placeholder: 'Passwort *' },
        { id: 'password_confirmation', name: 'password_confirmation', type: 'password', placeholder: 'Passwort wiederholen *' },
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
        password_confirmation: this.validatePassword_confirmation,
      };

      //Validiert alle Formularfelder, indem für jedes Feld die entsprechende Validierungsmethode aufruft 
      //every() iteriert über das Array und checkt für jedes Feld ob für die entsprechende Funktion true oder false zurückgegeben wird
      //der Rückgabewert ergibt true wenn alle Validierungen erfolgreich waren
      //Wird in SubmitForm() aufgerufen und das Formular wird abgeschickt wenn der Rückgabewert true ist 
      //Object.keys() gibt einen Array zurückt -> dynamische Verarbeitung vom validators Objekt ohne die einzelnen Einträge auflisten zu müssen 
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

    validatePassword_confirmation() {
      if (this.formData.password !== this.formData.password_confirmation) {
        this.errors.password_confirmation = "Die Passwörter stimmen nicht überein.";
        return false;
      }
      return true;
    },

    async submitForm() {
      this.errors = {};
      if (this.validateForm()) {
        try {
          const response = await axios.post('/signup', this.formData);

          if (response.data.success) {
            alert("Registrierung erfolgreich! Sie können sich jetzt anmelden.");
            console.log('Registrierung erfolgreich', response.data);
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
