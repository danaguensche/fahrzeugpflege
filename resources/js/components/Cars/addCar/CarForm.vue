<template>
  <div v-if="isOpen" class="form-wrapper profil">
    <v-card class="single-form blue-grey-lighten-3" max-width="700px">
      <v-card-title class="d-flex justify-space-between card-title">
        Fahrzeug hinzufügen
        <v-btn icon @click="closeForm" variant="plain">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text class="form-fields">
        <v-form @submit.prevent="submitForm" ref="form">
          <!-- Text Input Fields -->
          <v-text-field v-for="field in textFields" :key="field.id" v-model="formData[field.id]"
            :label="field.placeholder" :required="field.required" :error-messages="errors[field.id]" outlined
            density="comfortable" class="mb-3" width="95%"></v-text-field>

          <!-- File Upload für mehrere Bilder -->
          <v-file-input v-model="formData.images" label="Fahrzeugbilder" multiple
            accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" outlined density="comfortable"
            prepend-icon="mdi-camera" :error-messages="errors.images" show-size counter chips
            width="95%"></v-file-input>

          <!-- General Error Alert -->
          <v-alert v-if="errors.general" type="error" class="mt-3" density="compact" border="start">
            {{ errors.general }}
          </v-alert>

          <!-- Success Alert -->
          <v-alert v-if="success.general" type="success" class="mt-3" density="compact" border="start">
            {{ success.general }}
          </v-alert>

          <!-- Submit Button mit kleinerer Breite -->
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
import axios from 'axios';

export default {
  name: "CarForm",
  props: {
    isOpen: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      errors: {},
      success: {},
      isSubmitting: false,
      formData: {
        Kennzeichen: '',
        Fahrzeugklasse: '',
        Automarke: '',
        Typ: '',
        Farbe: '',
        Sonstiges: '',
        images: []
      },
      textFields: [
        { id: 'Kennzeichen', placeholder: 'Kennzeichen *', required: true },
        { id: 'Fahrzeugklasse', placeholder: 'Fahrzeugklasse', required: false },
        { id: 'Automarke', placeholder: 'Automarke', required: false },
        { id: 'Typ', placeholder: 'Typ', required: false },
        { id: 'Farbe', placeholder: 'Farbe', required: false },
        { id: 'Sonstiges', placeholder: 'Sonstiges', required: false },
      ],
    };
  },
  methods: {
    validateForm() {
      this.errors = {};
      this.success = {};
      if (!this.formData.Kennzeichen.trim()) {
        this.errors.Kennzeichen = 'Bitte geben Sie ein Kennzeichen an.';
        return false;
      }

      if (this.formData.images && this.formData.images.length > 0) {
        const maxSize = 16 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];

        for (let i = 0; i < this.formData.images.length; i++) {
          const image = this.formData.images[i];

          if (image.size > maxSize) {
            this.errors.images = `Bild "${image.name}" ist zu groß. Maximale Größe: 16 MB.`;
            return false;
          }

          if (!allowedTypes.includes(image.type)) {
            this.errors.images = `Bild "${image.name}" hat ein ungültiges Format. Erlaubt: JPG, PNG, GIF, SVG.`;
            return false;
          }
        }
      }

      return true;
    },

    async submitForm() {
      if (!this.validateForm()) return;

      this.isSubmitting = true;
      this.errors = {};
      this.success = {};

      try {
        // FormData für Dateiupload erstellen
        const formDataToSend = new FormData();

        // Text-Felder hinzufügen
        Object.keys(this.formData).forEach(key => {
          if (key !== 'images' && this.formData[key] !== null && this.formData[key] !== '') {
            formDataToSend.append(key, this.formData[key]);
          }
        });

        // Mehrere Bilder hinzufügen
        if (this.formData.images && this.formData.images.length > 0) {
          this.formData.images.forEach((file, index) => {
            formDataToSend.append(`images[${index}]`, file);
          });
        }

        // Neues Fahrzeug hinzufügen
        const response = await axios.post('/api/cars', formDataToSend, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        this.success.general = "Fahrzeug wurde erfolgreich hinzugefügt.";

        setTimeout(() => {
          this.resetForm();
          this.$emit('car-saved', response.data);
        }, 1500);

      } catch (error) {
        console.error('Fehler beim Speichern des Fahrzeuges:', error);

        if (error.response && error.response.status === 422) {
          const serverErrors = error.response.data.errors || error.response.data.error;
          if (serverErrors) {
            Object.keys(serverErrors).forEach(field => {
              this.errors[field] = Array.isArray(serverErrors[field])
                ? serverErrors[field][0]
                : serverErrors[field];
            });
          } else {
            this.errors.general = "Validierungsfehler beim Speichern.";
          }
        } else {
          this.errors.general = "Fehler beim Speichern des Fahrzeuges.";
        }
      } finally {
        this.isSubmitting = false;
      }
    },

    closeForm() {
      this.resetForm();
      this.$emit('close');
    },

    resetForm() {
      this.formData = {
        Kennzeichen: '',
        Fahrzeugklasse: '',
        Automarke: '',
        Typ: '',
        Farbe: '',
        Sonstiges: '',
        images: []
      };
      this.errors = {};
      this.success = {};

      if (this.$refs.form) {
        this.$refs.form.reset();
      }
    }
  }
}
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
  width: 700px
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
