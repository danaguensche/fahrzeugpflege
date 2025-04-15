<template>
  <div v-if="isOpen" class="form-wrapper">
    <div class="single-form">
      <form class="page-form" @submit.prevent="submitForm" enctype="multipart/form-data">
        <!-- Close Button -->
        <div class="form-header">
          <CloseButton :isVisible="true" @click="closeForm"></CloseButton>
        </div>

        <!-- Form-Fields -->
        <h2 class="forms-title">Fahrzeug hinzufügen</h2>
        <div v-for="field in formFields" :key="field.id" class="forms-field">
          <input 
            v-if="field.type !== 'file'" 
            :type="field.type" 
            :id="field.id" 
            :placeholder="field.placeholder"
            v-model="formData[field.id]" 
            class="forms-field-input" 
            :required="field.required"
          />
          <input 
            v-else 
            type="file" 
            accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"
            :id="field.id" 
            @change="handleFileUpload" 
            class="forms-field-input" 
          />
          <span v-if="errors[field.id]" class="alert error">
            {{ errors[field.id] }}
          </span>
        </div>

        <!-- General Error Alert -->
        <span v-if="errors.general" class="alert error">
          {{ errors.general }}
        </span>

        <!-- Success Alert -->
        <span v-if="success.general" class="alert success">
          {{ success.general }}
        </span>
        <SubmitButton :disabled="isSubmitting">{{ isSubmitting ? 'Wird gespeichert...' : 'Speichern' }}</SubmitButton>
      </form>
    </div>
  </div>
</template>

<script>
import SubmitButton from '../../Login/Slots/SubmitButton.vue';
import CloseButton from '../../CommonSlots/CloseButton.vue';
import axios from 'axios';

export default {
  name: "CarForm",
  components: {
    SubmitButton,
    CloseButton
  },
  props: {
    isOpen: {
      type: Boolean,
      required: true
    },
    carToEdit: {
      type: Object,
      default: null
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
        image: null
      },
      formFields: [
        { id: 'Kennzeichen', name: 'Kennzeichen', type: 'text', placeholder: 'Kennzeichen *', required: true },
        { id: 'Fahrzeugklasse', name: 'Fahrzeugklasse', type: 'text', placeholder: 'Fahrzeugklasse', required: false },
        { id: 'Automarke', name: 'Automarke', type: 'text', placeholder: 'Automarke', required: false },
        { id: 'Typ', name: 'Typ', type: 'text', placeholder: 'Typ', required: false },
        { id: 'Farbe', name: 'Farbe', type: 'text', placeholder: 'Farbe', required: false },
        { id: 'Sonstiges', name: 'Sonstiges', type: 'text', placeholder: 'Sonstiges', required: false },
        { id: 'image', name: 'Bild', type: 'file', required: false },
      ],
    };
  },
  watch: {
    carToEdit(car) {
      if (car) {
        // Wenn ein Fahrzeug zum Bearbeiten übergeben wird, Formular füllen
        this.formData = {
          Kennzeichen: car.Kennzeichen || '',
          Fahrzeugklasse: car.Fahrzeugklasse || '',
          Automarke: car.Automarke || '',
          Typ: car.Typ || '',
          Farbe: car.Farbe || '',
          Sonstiges: car.Sonstiges || '',
          image: null // Bild muss neu hochgeladen werden
        };
      }
    }
  },
  methods: {
    validateForm() {
      this.errors = {};
      
      // Kennzeichen ist Pflichtfeld
      if (!this.formData.Kennzeichen.trim()) {
        this.errors.Kennzeichen = 'Bitte geben Sie ein Kennzeichen an.';
        return false;
      }
      
      // Bildvalidierung
      if (this.formData.image) {
        const maxSize = 16 * 1024 * 1024; // 16 MB wie im Controller definiert
        if (this.formData.image.size > maxSize) {
          this.errors.image = 'Die Bilddatei darf maximal 16 MB groß sein.';
          return false;
        }
        
        // Prüfen des Bildtyps
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
        if (!allowedTypes.includes(this.formData.image.type)) {
          this.errors.image = 'Erlaubte Bildformate: JPEG, PNG, GIF, SVG.';
          return false;
        }
      }
      
      return true;
    },

    handleFileUpload(event) {
      this.formData.image = event.target.files[0] || null;
    },

    async submitForm() {
      if (!this.validateForm()) return;
      
      this.isSubmitting = true;
      this.errors = {};
      this.success = {};
      
      try {
        // FormData für Dateiupload erstellen
        const formDataToSend = new FormData();
        Object.keys(this.formData).forEach(key => {
          if (this.formData[key] !== null && this.formData[key] !== '') {
            formDataToSend.append(key, this.formData[key]);
          }
        });
        
        let response;
        
        if (this.carToEdit) {
          // Update existierendes Fahrzeug
          response = await axios.post(`/api/cars/${this.carToEdit.id}`, formDataToSend, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'X-HTTP-Method-Override': 'PUT' // Für Laravel PUT mit FormData
            }
          });
          this.success.general = "Fahrzeug wurde erfolgreich aktualisiert.";
        } else {
          // Neues Fahrzeug hinzufügen
          response = await axios.post('/api/cars', formDataToSend, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
          this.success.general = "Fahrzeug wurde erfolgreich hinzugefügt.";
        }
        
        console.log('Erfolgreich gespeichert:', response.data);
        
        // Formular zurücksetzen
        this.resetForm();
        
        // Event emittieren, um Liste zu aktualisieren
        this.$emit('car-saved');
      } catch (error) {
        console.error('Fehler beim Speichern des Fahrzeuges:', error);
        
        if (error.response && error.response.status === 422) {
          // Validierungsfehler vom Server
          const serverErrors = error.response.data.error;
          Object.keys(serverErrors).forEach(field => {
            this.errors[field] = serverErrors[field][0];
          });
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
      Object.keys(this.formData).forEach(key => {
        this.formData[key] = '';
      });
      this.formData.image = null;
      this.errors = {};
      
      // Dateieingabefeld zurücksetzen
      const fileInput = document.getElementById('image');
      if (fileInput) fileInput.value = '';
    }
  },
  mounted() {
    // Wenn ein Fahrzeug zum Bearbeiten übergeben wurde, Formular füllen
    if (this.carToEdit) {
      this.formData = {
        Kennzeichen: this.carToEdit.Kennzeichen || '',
        Fahrzeugklasse: this.carToEdit.Fahrzeugklasse || '',
        Automarke: this.carToEdit.Automarke || '',
        Typ: this.carToEdit.Typ || '',
        Farbe: this.carToEdit.Farbe || '',
        Sonstiges: this.carToEdit.Sonstiges || '',
        image: null
      };
    }
  }
}
</script>

<style scoped>
.form-header {
  position: absolute;
  top: -45px;
  right: 80px;
}

.single-form {
  position: relative;
  margin-top: 5vh;
  margin-bottom: 5vh;
  padding: 90px;
  box-shadow: var(--box-shadow);
  z-index: 10;
  background-color: var(--background-color);
  width: 700px;
}

.forms-title {
  margin-bottom: 50px;
}

.forms-field {
  margin-bottom: 15px;
}

.alert {
  display: block;
  padding: 10px;
  margin: 10px 0;
  border-radius: 4px;
}

.error {
  background-color: #ffebee;
  color: #c62828;
  border: 1px solid #ffcdd2;
}

.success {
  background-color: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.forms-field-input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

input[type="file"]::file-selector-button {
  border-radius: var(--border-radius);
  padding: 0 16px;
  height: 40px;
  cursor: pointer;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.16);
  box-shadow: 0px 1px 0px rgba(0, 0, 0, 0.05);
  margin-right: 16px;
  transition: background-color 200ms;
}

input[type="file"]::file-selector-button:hover {
  background-color: #f3f4f6;
}

input[type="file"]::file-selector-button:active {
  background-color: #e5e7eb;
}
</style>