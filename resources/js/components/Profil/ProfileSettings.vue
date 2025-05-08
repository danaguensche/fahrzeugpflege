<!-- Wer diese Seite formatiert kommt in die Hölle -->

<template>
  <v-container class="card-container">
    <!-- Skeleton Loader wenn loading true ist -->
    <Loader v-if="loading"></Loader>

    <!-- Vollständige Ansicht der Daten wenn loading false ist -->
    <template v-else>
      <!-- Header der Karte -->
      <v-card class="card">
        <Header :title="'Accountdetails'" :switchEditMode="switchEditMode" :editMode="editMode" :icon="'mdi-account'">
        </Header>

        <!-- Persönliche Informationen -->
        <v-card-text class="px-4 pt-4 pb-0">
          <v-sheet>
            <InformationHeader :title="'Persönliche Informationen'" :editMode="editMode"></InformationHeader>

            <!-- Ansichtsmodus -->
            <v-list class="bg-transparent" v-if="!editMode">
              <template v-for="key in personalInfoKeys" :key="key">
                <v-list-item v-if="userData[key] !== undefined">

                  <template v-slot:prepend>
                    <v-icon :icon="getIconForField(key)" color="primary" class="mr-2"></v-icon>
                  </template>

                  <v-list-item-title class="font-weight-medium">{{ labels[key] }}</v-list-item-title>
                  <v-list-item-subtitle class="mt-1
                                          text-body-1">
                    <template v-if="userData[key] === null || userData[key] === ''">
                      <span class="text-grey">Keine Daten vorhanden</span>
                    </template>
                    <template v-else>
                      {{ userData[key] }}
                    </template>
                  </v-list-item-subtitle>
                </v-list-item>
                <v-divider v-if="key !== personalInfoKeys[personalInfoKeys.length - 1]"></v-divider>
              </template>
            </v-list>

            <!-- Bearbeitungsmodus -->
            <div class="pa-4" v-else>
              <v-form ref="personalInfoForm">
                <template v-for="key in personalInfoKeys" :key="key">
                  <v-row no-gutters class="mb-4">
                    <v-col cols="12">
                      <div class="d-flex
                                    align-center
                                    mb-1">
                        <v-icon :icon="getIconForField(key)" color="primary" class="mr-2"></v-icon>
                        <span class="font-weight-medium">{{ labels[key] }}</span>
                      </div>
                      <v-text-field v-model="editedUserData[key]" variant="outlined" density="comfortable"
                        hide-details="auto" :readonly="key === 'email'" :disabled="key === 'email'"
                        :hint="key === 'email' ? 'E-Mail kann nicht geändert werden' : ''"
                        :persistent-hint="key === 'email'"></v-text-field>
                    </v-col>
                  </v-row>
                </template>
              </v-form>
            </div>
          </v-sheet>

          <!-- Adressinformationen -->
          <v-sheet>
            <div class="pa-4
                      bg-primary-lighten-5">
              <span class="text-h6
                             font-weight-medium">
                Adressinformationen
              </span>
            </div>

            <!-- Ansichtsmodus -->
            <v-list class="bg-transparent" v-if="!editMode">
              <template v-for="key in addressInfoKeys" :key="key">
                <v-list-item v-if="userData[key] !== undefined">
                  <template v-slot:prepend>
                    <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                    </v-icon>
                  </template>
                  <v-list-item-title class="font-weight-medium">
                    {{ labels[key] }}
                  </v-list-item-title>
                  <v-list-item-subtitle class="mt-1 text-body-1">
                    <template v-if="!userData[key]">
                      <span class="text-grey">
                        Keine Daten vorhanden
                      </span>
                    </template>
                    <template v-else>
                      {{ userData[key] }}
                    </template>
                  </v-list-item-subtitle>
                </v-list-item>
                <v-divider v-if="key !== addressInfoKeys[addressInfoKeys.length - 1]"></v-divider>
              </template>
            </v-list>

            <!-- Bearbeitungsmodus -->
            <div class="pa-4" v-else>
              <v-form ref="addressInfoForm">
                <template v-for="key in addressInfoKeys" :key="key">
                  <v-row no-gutters class="mb-4">
                    <v-col cols="12">
                      <div class="d-flex 
                              align-center mb-1">
                        <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                        </v-icon>
                        <span class="font-weight-medium">
                          {{ labels[key] }}
                        </span>
                      </div>
                      <v-text-field v-model="editedUserData[key]" variant="outlined" density="comfortable"
                        hide-details="auto" class="w-50">
                      </v-text-field>
                    </v-col>
                  </v-row>
                </template>
              </v-form>
            </div>
          </v-sheet>

          <!-- Bearbeitungsoptionen -->
          <v-sheet v-if="editMode">
            <div class="pa-4 
                      d-flex
                      justify-end">
              <CancelButton :cancelEdit="cancelEdit"></CancelButton>
              <SaveButton :saveData="saveUserData"></SaveButton>
            </div>
          </v-sheet>

          <!-- Passwort ändern -->
          <v-sheet>
            <div class="pa-4
                          bg-primary-lighten-5
                          rounded-t-lg
                          d-flex
                          align-center">
              <v-icon size="24" class="mr-2" color="primary">
                mdi-lock
              </v-icon>
              <span class="text-h6
                      font-weight-medium">
                Passwort ändern
              </span>
            </div>
            <div class="pa-4">
              <v-form ref="passwordForm" v-model="passwordFormValid" @submit.prevent="changePassword">
                <v-row>
                  <v-col cols="12">
                    <v-text-field v-model="passwordData.currentPassword"
                      :append-icon="showCurrentPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      :type="showCurrentPassword ? 'text' : 'password'" label="Aktuelles Passwort" variant="outlined"
                      density="comfortable" :rules="[rules.required]"
                      @click:append="showCurrentPassword = !showCurrentPassword">
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="passwordData.newPassword"
                      :append-icon="showNewPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      :type="showNewPassword ? 'text' : 'password'" label="Neues Passwort" variant="outlined"
                      density="comfortable" :rules="[rules.required, rules.min]"
                      @click:append="showNewPassword = !showNewPassword">
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="passwordData.confirmPassword"
                      :append-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      :type="showConfirmPassword ? 'text' : 'password'" label="Neues Passwort bestätigen"
                      variant="outlined" density="comfortable" :rules="[rules.required, rules.passwordMatch]"
                      @click:append="showConfirmPassword = !showConfirmPassword">
                    </v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <div class="d-flex
                                  justify-end">
                      <v-btn color="primary" variant="elevated" type="submit" :loading="passwordLoading"
                        :disabled="!passwordFormValid" prepend-icon="mdi-lock-reset">
                        Passwort ändern
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>
              </v-form>
            </div>
          </v-sheet>
        </v-card-text>

        <v-card-actions class="pa-4">
          <BackButton></BackButton>
          <v-spacer></v-spacer>
          <EditButton :switchEditMode="switchEditMode" v-if="!editMode"></EditButton>
        </v-card-actions>

      </v-card>
    </template>

    <!-- Snackbar für Benachrichtigungen -->
    <SnackBar v-if="snackbar.show" :text="snackbar.text" :color="snackbar.color" @close="snackbar.show = false">
    </SnackBar>
  </v-container>
</template>

<script>
import axios from 'axios';
import Loader from '../Details/Loader.vue';
import Header from '../Details/Header.vue';
import InformationHeader from '../Details/InformationHeader.vue';
import SnackBar from '../Details/SnackBar.vue';
import PrintButton from '../CommonSlots/PrintButton.vue';
import EditButton from '../Details/EditButton.vue';
import BackButton from '../CommonSlots/BackButton.vue';
import CancelButton from '../Details/CancelButton.vue';
import SaveButton from '../Details/SaveButton.vue';

export default {
  name: "ProfileSettings",
  components: {
    Loader,
    Header,
    InformationHeader,
    SnackBar,
    PrintButton,
    EditButton,
    BackButton,
    CancelButton,
    SaveButton
  },
  data() {
    return {
      loading: true,
      userId: null,
      userData: {},
      editedUserData: {},
      editMode: false,
      saveLoading: false,
      labels: {
        firstName: "Vorname",
        lastName: "Nachname",
        phoneNumber: "Telefonnummer",
        email: "E-Mail",
        addressLine: "Straße und Hausnummer",
        postalCode: "PLZ",
        city: "Ort"
      },
      error: null,
      // Passwort-Änderung
      passwordData: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      passwordFormValid: false,
      passwordLoading: false,
      showCurrentPassword: false,
      showNewPassword: false,
      showConfirmPassword: false,
      rules: {
        required: v => !!v || 'Dieses Feld ist erforderlich',
        min: v => v.length >= 8 || 'Mindestens 8 Zeichen erforderlich',
        passwordMatch: v => v === this.passwordData.newPassword || 'Passwörter stimmen nicht überein'
      },
      // Snackbar
      snackbar: {
        show: false,
        text: '',
        color: 'success'
      }
    };
  },

  computed: {
    personalInfoKeys() {
      return ['firstName', 'lastName', 'phoneNumber', 'email'];
    },
    addressInfoKeys() {
      return ['addressLine', 'postalCode', 'city'];
    }
  },

  async created() {
    // Sicherstellen, dass die initial notwendigen Daten vorhanden sind
    if (!this.userData) {
      this.userData = {};
    }
    if (!this.editedUserData) {
      this.editedUserData = {};
    }
  },

  async mounted() {
    try {
      await this.getUser();
    } catch (error) {
      console.error("Fehler beim Laden der Komponente:", error);
      this.error = error.message;
      this.showSnackbar(error.message, 'error');
    } finally {
      this.loading = false;
    }
  },

  methods: {
    async getUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          console.error("Kein Token gefunden!");
          throw new Error("Kein Authentifizierungs-Token gefunden");
        }

        const response = await axios.get("http://localhost:8000/api/users/me", {
          headers: { Authorization: `Bearer ${token}` }
        });

        console.log("API-Antwort:", response.data);
        this.userData = {
          firstName: response.data.data.firstName || "",
          lastName: response.data.data.lastName || "",
          phoneNumber: response.data.data.phoneNumber || "",
          email: response.data.data.email || "",
          addressLine: response.data.data.addressLine || "",
          postalCode: response.data.data.postalCode || "",
          city: response.data.data.city || ""
        };

        // Kopie für Bearbeitungsmodus erstellen
        this.editedUserData = { ...this.userData };

        console.log("Benutzerdaten geladen:", this.userData);
      } catch (error) {
        console.error("Fehler beim Laden der Benutzerdaten:", error);
        this.error = error.response?.data?.message || error.message;
        throw error;
      }
    },

    getIconForField(key) {
      const iconMap = {
        firstName: "mdi-account",
        lastName: "mdi-account-details",
        email: "mdi-email",
        phoneNumber: "mdi-phone",
        addressLine: "mdi-map-marker",
        postalCode: "mdi-mail",
        city: "mdi-city"
      };

      return iconMap[key] || "mdi-information-outline";
    },

    switchEditMode() {
      this.editMode = !this.editMode;

      if (this.editMode) {
        // Daten für die Bearbeitung kopieren
        this.editedUserData = { ...this.userData };
      }
    },

    cancelEdit() {
      this.editMode = false;
      // Änderungen verwerfen und auf Original zurücksetzen
      this.editedUserData = { ...this.userData };
    },

    async saveUserData() {
      this.saveLoading = true;
      this.error = null;

      try {
        const token = localStorage.getItem('token');
        if (!token) {
          throw new Error("Kein Authentifizierungs-Token gefunden");
        }

        const dataToSend = {
          firstname: this.editedUserData.firstName,
          lastname: this.editedUserData.lastName,
          phonenumber: this.editedUserData.phoneNumber,
          addressline: this.editedUserData.addressLine,
          postalcode: this.editedUserData.postalCode,
          city: this.editedUserData.city
        };
        await axios.put(
          "http://localhost:8000/api/users/me",
          dataToSend,
          {
            headers: { Authorization: `Bearer ${token}` }
          }
        );

        // Aktualisierte Daten übernehmen
        this.userData = { ...this.editedUserData };

        // Bearbeitungsmodus beenden
        this.editMode = false;

        this.showSnackbar('Daten erfolgreich gespeichert', 'success');
      } catch (error) {
        console.error("Fehler beim Speichern der Benutzerdaten:", error);
        const errorMessage = error.response?.data?.message || "Fehler beim Speichern der Benutzerdaten";
        this.showSnackbar(errorMessage, 'error');
      } finally {
        this.saveLoading = false;
      }
    },

    goBack() {
      try {
        this.$router.back();
      } catch (e) {
        console.error("Fehler beim Navigieren zurück:", e);
        // Fallback, falls $router.back() nicht funktioniert
        window.history.back();
      }
    },

    async changePassword() {
      if (!this.$refs.passwordForm.validate()) {
        return;
      }

      this.passwordLoading = true;

      try {
        const token = localStorage.getItem('token');
        if (!token) {
          throw new Error("Kein Authentifizierungs-Token gefunden");
        }

        await axios.post("http://localhost:8000/api/users/change-password",
          {
            currentPassword: this.passwordData.currentPassword,
            newPassword: this.passwordData.newPassword
          },
          {
            headers: { Authorization: `Bearer ${token}` }
          }
        );

        // Passwort-Formular zurücksetzen
        this.passwordData = {
          currentPassword: '',
          newPassword: '',
          confirmPassword: ''
        };
        this.$refs.passwordForm.reset();

        this.showSnackbar('Passwort erfolgreich geändert', 'success');
      } catch (error) {
        console.error("Fehler beim Ändern des Passworts:", error);
        const errorMessage = error.response?.data?.message || "Fehler beim Ändern des Passworts";
        this.showSnackbar(errorMessage, 'error');
      } finally {
        this.passwordLoading = false;
      }
    },

    showSnackbar(text, color = 'success') {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    }
  }
};
</script>

<style scoped>
.card-container {
  width: 100%;
  height: calc(100vh - 40px);
  padding: 20px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
}

.card {
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  transition: all 0.3s ease;
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

@media (max-width: 575.98px) {
  .card-container {
    padding: 10px;
    height: calc(100vh - 20px);

  }

  .card {
    font-size: 14px;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .card-container {
    padding: 15px;
    height: calc(100vh - 30px);
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .card-container {
    max-width: calc(100% - 80px);
  }
}

@media (min-width: 992px) and (max-width: 1199.98px) {
  .card-container {
    max-width: calc(100% - 250px);
  }
}

@media (min-width: 1200px) {
  .card-container {
    max-width: calc(100% - 280px);
  }
}

.v-card-text {
  flex: 1;
  overflow-y: auto;
}

@media (max-width: 767.98px) {
  .v-card-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .v-card-actions button {
    margin-bottom: 8px;
    width: 100%;
  }

  .v-spacer {
    display: none;
  }
}
</style>