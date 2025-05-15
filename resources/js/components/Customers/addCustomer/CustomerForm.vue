<template>
  <div v-if="isOpen" class="form-wrapper profil">
    <v-card class="single-form blue-grey-lighten-3" max-width="700px">
      <v-card-title class="d-flex justify-space-between card-title">
        Kunde hinzufügen
        <v-btn icon @click="closeForm" variant="plain">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text class="form-fields">
        <v-form @submit.prevent="submitForm" ref="form">
          <!-- Text Input Fields -->
          <v-text-field v-for="field in formFields" :key="field.id" v-model="formData[field.id]"
            :label="field.placeholder" :required="field.required" :error-messages="errors[field.id]" outlined
            density="comfortable" class="mb-3" width="95%"></v-text-field>

          <!-- General Error Alert -->
          <v-alert v-if="errors.general" type="error" class="mt-3" density="compact" border="start">
            {{ errors.general }}
          </v-alert>

          <!-- Success Alert -->
          <v-alert v-if="success" type="success" class="mt-3" density="compact" border="start">
            {{ success }}
          </v-alert>

          <!-- Submit Button -->
          <div class="d-flex justify-left mt-4">
            <v-btn type="submit" color="darkslategray" class="submit-button" :loading="isSubmitting"
              :disabled="isSubmitting">
              {{ isSubmitting ? 'Wird gespeichert...' : 'Speichern' }}
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useValidation } from '../../../composables/useValidation.js';
import axios from 'axios';

export default {
  name: "CustomerForm",
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  data() {
    const initialData = {
      company: "",
      firstname: "",
      lastname: "",
      email: "",
      phonenumber: "",
      addressline: "",
      postalcode: "",
      city: "",
    };

    const { formData, errors, validateForm } = useValidation(initialData);
    const success = ref('');
    const isSubmitting = ref(false);

    const formFields = [
      { id: 'company', name: 'Firma', type: 'text', placeholder: 'Firma' },
      { id: 'firstname', name: 'Vorname *', type: 'text', placeholder: 'Vorname *' },
      { id: 'lastname', name: 'Nachname *', type: 'text', placeholder: 'Nachname *' },
      { id: 'email', name: 'E-Mail *', type: 'email', placeholder: 'Email *' },
      { id: 'phonenumber', name: 'Telefonnummer *', type: 'text', placeholder: 'Telefonnummer *' },
      { id: 'addressline', name: 'Straße und Hausnummer *', type: 'text', placeholder: 'Straße und Hausnummer *' },
      { id: 'postalcode', name: 'PLZ *', type: 'text', placeholder: 'PLZ *' },
      { id: 'city', name: 'Stadt *', type: 'text', placeholder: 'Stadt *' },
    ];

    const submitForm = async () => {
      if (validateForm()) {
        isSubmitting.value = true;
        try {
          const response = await axios.post('/api/customers', formData);
          console.log('Kunde wurde erfolgreich hinzugefügt.', response.data);
          success.value = "Kunde wurde erfolgreich hinzugefügt.";
          //Formular zurücksetzen
          Object.keys(formData).forEach(key => formData[key] = '');
        } catch (error) {
          console.error('Fehler beim Speichern des Kunden.', error);
          errors.value.general = "Fehler beim Speichern des Kunden.";
        } finally {
          isSubmitting.value = false;
        }
      }
    };

    return {
      formData,
      errors,
      formFields,
      submitForm,
      success,
      isSubmitting
    };
  },
  methods: {
    closeForm() {
      this.$emit('close');
    },
  }
};
</script>

<style scoped>
.single-form {
  position: relative;
  margin-top: 5vh;
  margin-bottom: 5vh;
  padding: 20px;
  box-shadow: var(--box-shadow);
  z-index: 10;
  background-color: var(--background-color);
  width: 700px;
}

.form-fields {
  padding: 40px;
}

.card-title {
  margin-top: 25px;
  margin-left: 25px;
}

.submit-button {
  background-color: darkslategray;
  color: white;
  font-weight: bold;
  text-transform: uppercase;
  padding: 10px;
  width: 170px;
}

.form-wrapper.profil {
  font-family: var(--font-family);
  display: flex;
  align-self: flex-start;
  justify-content: center;
  flex-direction: row;
}
</style>
