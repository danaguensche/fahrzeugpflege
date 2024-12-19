<template>
  <div v-if="isOpen" class="form-wrapper">
    <div class="single-form">
      <form class="page-form" @submit.prevent="submitForm">
        <!-- Close Button -->
        <div class="form-header">
          <CloseButton :isVisible="true" @click="closeForm"></CloseButton>
        </div>

        <!-- Form-Fields -->
        <h2 class="forms-title">Fahrzeug hinzufügen</h2>
        <div v-for="field in formFields" :key="field.id" class="forms-field">
          <!-- <p class="field-header">{{ field.name }}</p> -->
          <input v-if="field.type !== 'file'" :type="field.type" :id="field.id" :placeholder="field.placeholder"
            v-model="formData[field.id]" class="forms-field-input" />
          <input v-else type="file" :id="field.id" @change="handleFileUpload" class="forms-field-input" />
          <span v-if="errors[field.id]" class="alert error">
            {{ errors[field.id] }}
          </span>
        </div>

        <!-- Success Alert -->
        <span v-if="success.general" class="alert success">
          {{ success.general }}
        </span>
        <SubmitButton>Speichern</SubmitButton>
      </form>
    </div>
  </div>
</template>

<script>
import SubmitButton from '../../Login/Slots/SubmitButton.vue';
import CloseButton from '../../CommonSlots/CloseButton.vue';
import axios from 'axios';

export default {
  name: "Form",
  components: {
    SubmitButton,
    CloseButton
  },
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
        { id: 'Kennzeichen', name: 'Kennzeichen', type: 'text', placeholder: 'Kennzeichen *' },
        { id: 'Fahrzeugklasse', name: 'Fahrzeugklasse', type: 'text', placeholder: 'Fahrzeugklasse' },
        { id: 'Automarke', name: 'Automarke', type: 'text', placeholder: 'Automarke' },
        { id: 'Typ', name: 'Typ', type: 'text', placeholder: 'Typ' },
        { id: 'Farbe', name: 'Farbe', type: 'text', placeholder: 'Farbe' },
        { id: 'Sonstiges', name: 'Sonstiges', type: 'text', placeholder: 'Sonstiges' },
        { id: 'image', name: 'Bild', type: 'file' },
      ],
    };
  },
  methods: {
    validateForm() {
      this.errors = {};
      return this.validateKennzeichen();
    },

    validateKennzeichen() {
      if (!this.formData.Kennzeichen.trim()) {
        this.errors.Kennzeichen = `Bitte geben Sie ein Kennzeichen an.`;
        return false;
      }
      return true;
    },

    handleFileUpload(event) {
      this.formData.image = event.target.files[0];
    },

    async submitForm() {
      if (!this.validateForm()) {
        return;
      }

      try {
        const formData = new FormData();
        for (let key in this.formData) {
          formData.append(key, this.formData[key]);
        }

        const response = await axios.post('/fahrzeuge', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        console.log('Fahrzeug erfolgreich gespeichert:', response.data);
        this.success.general = "Fahrzeug wurde erfolgreich gespeichert";
        this.resetForm();

      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error('Fehler beim Speichern des Fahrzeugs:', error);
          this.errors.general = "Fehler beim Speichern des Fahrzeugs";
        }
      }
    },

    closeForm() {
      this.$emit('close');
    },

    resetForm() {
      for (let key in this.formData) {
        this.formData[key] = '';
      }
      this.formData.image = null;
    }
  },
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

.forms-fieldset {
  margin-bottom: 10px;
}

.forms-title {
  margin-bottom: 50px;
}


.forms-field {
  margin-bottom: 15px;
}

.field-header {
  font-family: var(--font-family);
  margin-bottom: 1vh;
}

.forms-field-input {
  width: 100%;
  padding: 10px;
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