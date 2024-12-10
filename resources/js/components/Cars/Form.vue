<template>
    <div class="form-wrapper">
      <form class="page-form" @submit.prevent="submitForm">
        <h2 class="forms-title">Fahrzeug dokumentieren</h2>
        <div v-for="field in formFields" :key="field.id" class="forms-field">
          <input v-if="field.type !== 'file'" :type="field.type" :id="field.id" :placeholder="field.placeholder"
            v-model="formData[field.id]" class="forms-field-input" />
          <input v-else :id="field.id" :name="field.name" type="file" @change="handleFileUpload" />
          <span v-if="errors[field.id]" class="alert error">
            {{ Array.isArray(errors[field.id]) ? errors[field.id][0] : errors[field.id] }}
          </span>
        </div>
        <span v-if="success.general" class="alert success">
          {{ success.general }}
        </span>
        <SubmitButton>Speichern</SubmitButton>
      </form>
    </div>
  </template>
  
  <script>
  import SubmitButton from '../Login/Slots/SubmitButton.vue';
  import axios from 'axios';
  import { ref } from 'vue';
  
  export default {
    name: "Form",
    components: {
      SubmitButton,
    },
    data() {
      return {
        errors: {},
        success: {},
        formData: {
          Kennzeichen: '',
          Fahrzeugklasse: null,
          Automarke: null,
          Typ: null,
          Farbe: null,
          Sonstiges: null,
          image: null
        },
        formFields: [
          { id: 'Kennzeichen', name: 'Kennzeichen', type: 'text', placeholder: 'Kennzeichen' },
          { id: 'Fahrzeugklasse', name: 'Fahrzeugklasse', type: 'text', placeholder: 'Fahrzeugklasse' },
          { id: 'Automarke', name: 'Automarke', type: 'text', placeholder: 'Automarke' },
          { id: 'Typ', name: 'Typ', type: 'text', placeholder: 'Typ' },
          { id: 'Farbe', name: 'Farbe', type: 'text', placeholder: 'Farbe' },
          { id: 'Sonstiges', name: 'Sonstiges', type: 'text', placeholder: 'Sonstiges' },
          { id: 'image', name: 'image', type: 'file' },
        ],
      };
    },
    methods: {
      validateForm() {
        this.errors = {};
        return this.validateKennzeichen();
      },
  
      validateKennzeichen() {
        if (this.formData.Kennzeichen.length === 0) {
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
              // Anfrage wird damit gesendet um Datei Upload möglich zu machen
              'Content-Type': 'multipart/form-data'
            }
          });
          
          console.log('Fahrzeug erfolgreich gespeichert:', response.data);
          this.success.general = "Fahrzeug wurde erfolgreich gespeichert";
          
        } catch (error) {
          if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.error('Fehler beim Speichern des Fahrzeugs:', error);
            this.errors.general = "Fehler beim Speichern des Fahrzeugs";
          }
        }
      },
    }
  }
  </script>
  
  <style scoped>
  .form-wrapper {
      box-shadow: var(--box-shadow);
      padding: 70px;
      align-self: flex-end;
  }
  
  .forms-fieldset {
      margin-bottom: 10px;
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
  