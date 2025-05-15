<!-- CustomerAddDialog.vue -->
<template>
  <v-dialog v-model="dialog" max-width="600px">
    <v-card>
      <!-- Existierender Kunde Schritt -->
      <template v-if="step === 'checkExisting'">
        <v-card-title class="headline">
          <span class="text-h5">Kunde bereits vorhanden?</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="existingCustomerForm" v-model="existingFormValid">
            <v-text-field v-model="existingCustomerSearch" label="E-Mail oder Nachname"
              :rules="[v => !!v || 'Suchbegriff ist erforderlich']" @keyup.enter="searchCustomer"></v-text-field>
          </v-form>

          <!-- Suchergebnisse -->
          <v-list v-if="searchResults.length > 0">
            <v-list-item v-for="customer in searchResults" :key="customer.id" @click="selectExistingCustomer(customer)">
              <v-list-item-content>
                <v-list-item-title>
                  {{ customer.firstname }} {{ customer.lastname }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ customer.email }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <div v-if="showNoResultsMessage" class="text-center mt-4">
            <p>Keine Kunden gefunden.</p>
            <v-btn color="primary" @click="step = 'createNew'">
              Neuen Kunden erstellen
            </v-btn>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDialog">
            Abbrechen
          </v-btn>
          <v-btn color="blue darken-1" text @click="searchCustomer" :disabled="!existingFormValid">
            Suchen
          </v-btn>
        </v-card-actions>
      </template>

      <!-- Neuer Kunde Schritt -->
      <template v-else-if="step === 'createNew'">
        <v-card-title class="headline">
          <span class="text-h5">Neuen Kunden hinzufügen</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="customerForm" v-model="valid" lazy-validation>
            <v-container>
              <v-row>
                <v-col cols="12" sm="6">
                  <v-text-field v-model="newCustomer.firstname" :rules="[v => !!v || 'Vorname ist erforderlich']"
                    label="Vorname" required></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field v-model="newCustomer.lastname" :rules="[v => !!v || 'Nachname ist erforderlich']"
                    label="Nachname" required></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="newCustomer.email" :rules="[
                    v => !!v || 'E-Mail ist erforderlich',
                    v => /.+@.+\..+/.test(v) || 'Ungültige E-Mail-Adresse'
                  ]" label="E-Mail" type="email" required></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="newCustomer.phonenumber" label="Telefonnummer" type="tel"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="newCustomer.addressline" label="Adresse"></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field v-model="newCustomer.postalcode" label="Postleitzahl"></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field v-model="newCustomer.city" label="Stadt"></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="blue darken-1" text @click="step = 'checkExisting'">
            Zurück
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDialog">
            Abbrechen
          </v-btn>
          <v-btn color="blue darken-1" text @click="saveCustomer" :disabled="!valid">
            Speichern
          </v-btn>
        </v-card-actions>
      </template>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CustomerAddDialog',
  data() {
    return {
      dialog: false,
      step: 'checkExisting',
      valid: false,
      existingFormValid: false,
      existingCustomerSearch: '',
      searchResults: [],
      showNoResultsMessage: false,
      newCustomer: {
        firstname: '',
        lastname: '',
        email: '',
        phonenumber: '',
        addressline: '',
        postalcode: '',
        city: '',
      },
    };
  },
  methods: {
    open() {
      this.dialog = true;
      this.step = 'checkExisting';
      this.resetForm();
    },
    closeDialog() {
      this.dialog = false;
      this.resetForm();
    },
    resetForm() {
      this.newCustomer = {
        firstname: '',
        lastname: '',
        email: '',
        phonenumber: '',
        addressline: '',
        postalcode: '',
        city: '',
      };
      this.existingCustomerSearch = '';
      this.searchResults = [];
      this.showNoResultsMessage = false;

      // Formular zurücksetzen, wenn sie existieren
      this.$nextTick(() => {
        if (this.$refs.customerForm) {
          this.$refs.customerForm.resetValidation();
        }
        if (this.$refs.existingCustomerForm) {
          this.$refs.existingCustomerForm.resetValidation();
        }
      });
    },
    async searchCustomer() {
      if (this.$refs.existingCustomerForm && !this.$refs.existingCustomerForm.validate()) {
        this.showNoResultsMessage = true;
        return;
      }

      try {
        const response = await axios.get('/api/customers/search', {
          params: { query: this.existingCustomerSearch }
        });

        // Erwartetes Format: { data: [...] }
        this.searchResults = Array.isArray(response.data.data) ? response.data.data : [];
        this.showNoResultsMessage = this.searchResults.length === 0;
      } catch (error) {
        this.$emit('error', error.response?.data?.message || 'Fehler bei der Kundensuche');
        this.searchResults = [];
        this.showNoResultsMessage = true;
      }
    },

    async saveCustomer() {
      // Formularvalidierung
      if (this.$refs.customerForm && !this.$refs.customerForm.validate()) {
        return;
      }

      try {
        // POST-Anfrage zum Erstellen eines neuen Kunden
        const response = await axios.post('/api/customers', this.newCustomer);

        if (response.data) {
          // Emit-Ereignis mit neuen Kundendaten
          this.$emit('customer-added', response.data);
          this.closeDialog();
        } else {
          throw new Error('Keine Daten vom Server erhalten');
        }
      } catch (error) {
        console.error('Fehler beim Hinzufügen des Kunden:', error);
        const errorMessage = error.response?.data?.message || 'Fehler beim Hinzufügen des Kunden';
        this.$emit('error', errorMessage);
      }
    }
  }
};
</script>