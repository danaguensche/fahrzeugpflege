<template>
    <v-container class="card-container">
        <!-- Skeleton Loader wenn loading true ist -->
        <Loader v-if="loading"></Loader>

        <!-- Vollständige Ansicht der Daten wenn loading false ist -->
        <template v-else>
            <!-- Header -->
            <v-card class="card">
                <Header :title="headerTitle" :switchEditMode="switchEditMode" :icon="headerIcon"></Header>

                

                <!-- Fahrzeug information -->
                <v-card-text class="px-4 pt-4 pb-0">
                    <v-sheet>
                        <InformationHeader :title="'Fahrzeuginformationen'" :editMode="editMode" v-if="isAdminOrTrainer">
                        </InformationHeader>

                        <!-- Ansichtsmodus -->
                        <InfoList v-if="!editMode" :details="carDetails" :labels="labels" :infoKeys="vehicleInfoKeys"
                            :getIconForField="getIconForField">
                        </InfoList>

                        <!-- Bearbeitungsmodus -->
                        <InfoListEditMode v-else :personalInfoKeys="vehicleInfoKeys" :labels="labels"
                            :editedData="editedCarData" :getIconForField="getIconForField">
                        </InfoListEditMode>
                    </v-sheet>

                    <!-- Customer information -->
                    <v-sheet>
                        <DefaultHeader :title="'Kundeninformation'"></DefaultHeader>
                        <CustomerInfoList v-if="!editMode" :customer="carDetails.data.customer" :customerId="carDetails.data.customer_id"
                            :labels="labels">
                        </CustomerInfoList>

                        <v-autocomplete
                            v-else-if="isAdminOrTrainer"
                            v-model="editedCarData.customer"
                            :items="customers"
                            item-title="full_name"
                            item-value="id"
                            label="Kunde"
                            placeholder="Kunde auswählen oder suchen"
                            prepend-inner-icon="mdi-account"
                            variant="outlined"
                            density="comfortable"
                            hide-details="auto"
                            clearable
                            :loading="customersLoading"
                            :search-input.sync="customerSearch"
                            @update:search-input="searchCustomers"
                            return-object
                        >
                            <template v-slot:item="{ props, item }">
                                <v-list-item
                                    v-bind="props"
                                    :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                    :subtitle="item.raw.email"
                                ></v-list-item>
                            </template>
                            <template v-slot:selection="{ item }">
                                {{ item.raw.email }}
                            </template>
                        </v-autocomplete>

                        <!-- Button wird nur angezeigt wenn noch kein Kunde eingetragen wurde -->
                        <v-btn class="mt-4" color="primary"
                            v-if="(!carDetails.data.customer || carDetails.data.customer === 0) && isAdminOrTrainer"
                            @click="openCustomerAddDialog">
                            Kunde hinzufügen
                        </v-btn>
                    </v-sheet>

                    <v-sheet>
                    <DefaultHeader :title="'Fotos'"></DefaultHeader>
                    <PhotoGallery :carId="carDetails.data.id"></PhotoGallery>
                    <PhotoUpload :carId="carDetails.data.id"></PhotoUpload>
                </v-sheet>

                <!-- Metadaten -->
                    <MetaData :labels="labels" :formattedCreatedAt="formattedCreatedAt"
                        :formattedUpdatedAt="formattedUpdatedAt">
                    </MetaData>
                </v-card-text>

                <v-card-actions class="pa-4">
                    <BackButton></BackButton>
                    <v-spacer></v-spacer>

                    <!-- Bearbeitungsmodus Aktionen -->
                    <template v-if="editMode && isAdminOrTrainer">
                        <CancelButton :cancelEdit="cancelEdit"></CancelButton>
                        <SaveButton :saveData="saveCarData"></SaveButton>
                    </template>

                    <!-- Ansichtsmodus Aktionen -->
                    <template v-else>
                        <EditButton :switchEditMode="switchEditMode" v-if="isAdminOrTrainer"></EditButton>
                        <PrintButton></PrintButton>
                    </template>
                </v-card-actions>
            </v-card>
        </template>

        <!-- Kunde hinzufügen Dialog -->
        <CustomerAddDialog ref="customerAddDialog" @customer-added="handleCustomerAdded"
            @customer-selected="handleCustomerSelected" @error="handleCustomerAddError">
        </CustomerAddDialog>

        

        <!-- Snackbar für Benachrichtigungen -->
        <SnackBar v-if="snackbar.show" :text="snackbar.text" :color="snackbar.color" @close="snackbar.show = false">
        </SnackBar>
    </v-container>
</template>

<script>
import axios from "axios";
import { mapGetters } from 'vuex';
import Loader from "../../Details/Loader.vue";
import ImageCarousel from "../../Details/ImageCarousel.vue";
import InformationHeader from "../../Details/InformationHeader.vue";
import Header from "../../Details/Header.vue";
import BackButton from "../../CommonSlots/BackButton.vue";
import SnackBar from "../../Details/SnackBar.vue";
import PrintButton from "../../CommonSlots/PrintButton.vue";
import EditButton from "../../Details/EditButton.vue";
import MetaData from "../../Details/MetaData.vue";
import CancelButton from "../../Details/CancelButton.vue";
import SaveButton from "../../Details/SaveButton.vue";
import InfoList from "../../Details/InfoList.vue";
import InfoListEditMode from "../../Details/InfoListEditMode.vue";
import DefaultHeader from "../../Details/DefaultHeader.vue";
import CustomerInfoList from "../../Details/CustomerInfoList.vue";
import CustomerAddDialog from "./CustomerAddDialog.vue";
import PhotoUpload from '../../PhotoUpload.vue';
import PhotoGallery from '../../PhotoGallery.vue';

export default {
    name: "CarDetails",
    components: {
        Loader,
        ImageCarousel,
        InformationHeader,
        Header,
        BackButton,
        SnackBar,
        PrintButton,
        EditButton,
        MetaData,
        CancelButton,
        SaveButton,
        InfoList,
        InfoListEditMode,
        DefaultHeader,
        CustomerInfoList,
        CustomerAddDialog,
        PhotoUpload,
        PhotoGallery
    },
    data() {
        return {
            carDetails: {
                data: {
                    customer: {
                        id: 0,
                        firstname: '',
                        lastname: '',
                        email: ''
                    },
                }
            },
            editedCarData: {},
            headerTitle: "Fahrzeugdetails",
            headerIcon: "mdi-car",
            labels: {
                id: "ID",
                Kennzeichen: "Kennzeichen",
                Fahrzeugklasse: "Fahrzeugklasse",
                Automarke: "Automarke",
                Typ: "Typ",
                Farbe: "Farbe",
                Sonstiges: "Sonstiges",
                customer_id: "Kunden-ID",
                customer: "Kunde",
                created_at: "Erstellt am",
                updated_at: "Zuletzt aktualisiert am",
            },
            loading: true,
            error: null,
            editMode: false,
            saveLoading: false,
            imageUploadLoading: false,
            customers: [],
            customersLoading: false,
            customerSearch: null,
            customerSearchTimeout: null,
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
            
        };
    },
    computed: {
        ...mapGetters('auth', ['isAdminOrTrainer']),
        vehicleInfoKeys() {
            return ['id', 'Kennzeichen', 'Fahrzeugklasse', 'Automarke', 'Typ', 'Farbe', 'Sonstiges'];
        },
        // Add this computed property to safely handle customer display
        customerDisplay() {
            const customer = this.carDetails.data?.customer;
            if (!customer || customer.id === 0) {
                return 'Kein Kunde zugewiesen';
            }
            return `${customer.firstname} ${customer.lastname}`;
        },
        
        // Add this to safely handle customer ID
        customerIdDisplay() {
            const customerId = this.carDetails.data?.customer_id;
            if (!customerId || customerId === 0) {
                return 'Keine Kunden-ID';
            }
            return customerId;
        },
        images() {
            const img = this.carDetails.data?.images;
            console.log("Raw images data:", img);

            if (!img) {
                return [];
            }

            const mapImage = (image) => {
                if (image && image.id) {
                    return {
                        id: image.id,
                        path: image.path, // Keep path for consistency if needed
                        url: image.url
                    };
                }
                return null;
            };

            if (Array.isArray(img)) {
                return img.filter(Boolean).map(mapImage).filter(Boolean);
            }

            const singleImage = mapImage(img);
            return singleImage ? [singleImage] : [];
        },
        formattedCreatedAt() {
            return this.formatDate(this.carDetails.data?.created_at);
        },
        formattedUpdatedAt() {
            return this.formatDate(this.carDetails.data?.updated_at);
        },
    },
    async mounted() {
        try {
            await this.getCar();
            await this.fetchCustomers(); 
        } catch (error) {
            this.error = error.message;
            this.showSnackbar(error.message, 'error');
        } finally {
            this.loading = false;
        }
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return 'Unbekannt';

            try {
                return new Date(dateString).toLocaleDateString('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch {
                return 'Ungültiges Datum';
            }
        },

        async getCar() {
            try {
                if (!this.$route?.params?.kennzeichen) {
                    throw new Error("Kein Kennzeichen angegeben");
                }

                const { data } = await axios.get(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`
                );

                if (!data) {
                    throw new Error("Keine Daten vom Server erhalten");
                }

                this.carDetails = data;
                this.editedCarData = { ...this.carDetails.data };
                if (this.carDetails.data.customer) {
                    this.editedCarData.customer = {
                        id: this.carDetails.data.customer.id,
                        full_name: `${this.carDetails.data.customer.firstname} ${this.carDetails.data.customer.lastname}`,
                        email: this.carDetails.data.customer.email
                    };
                } else {
                    this.editedCarData.customer = null;
                }
            } catch (error) {
                this.error = error.response?.data?.message || error.message;
                this.showSnackbar("Fehler beim Laden der Fahrzeugdetails: " + this.error, 'error');
            } finally {
                this.loading = false;
            }
        },

        switchEditMode() {
            this.editMode = !this.editMode;

            if (this.editMode) {
                this.editedCarData = { ...this.carDetails.data };
            }
        },

        getIconForField(key) {
            const iconMap = {
                id: "mdi-pound",
                Kennzeichen: "mdi-car-info",
                Fahrzeugklasse: "mdi-car-estate",
                Automarke: "mdi-car-side",
                Typ: "mdi-car-select",
                Farbe: "mdi-palette",
                Sonstiges: "mdi-text-box",
            };

            return iconMap[key] || "mdi-information-outline";
        },

        cancelEdit() {
            this.editMode = false;
            this.editedCarData = { ...this.carDetails.data };
        },

        async saveCarData() {
            this.saveLoading = true;
            this.error = null;

            try {
                // Prepare data for submission
                const dataToSubmit = { ...this.editedCarData };
                
                // Handle customer_id - convert empty/null values to null
                if (dataToSubmit.customer) {
                    dataToSubmit.customer_id = dataToSubmit.customer.id;
                } else {
                    dataToSubmit.customer_id = null;
                }

                console.log('Submitting car data:', dataToSubmit);

                await axios.put(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`,
                    dataToSubmit
                );

                // Reload the car details
                const { data } = await axios.get(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`
                );

                this.carDetails = data;
                this.editMode = false;
                this.showSnackbar("Fahrzeugdaten erfolgreich gespeichert", 'success');
            } catch (error) {
                console.error('Error saving car data:', error);
                console.error('Error response:', error.response?.data);
                
                let errorMessage = "Fehler beim Speichern der Fahrzeugdaten";
                
                if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.errors) {
                    // Handle validation errors
                    const validationErrors = Object.values(error.response.data.errors).flat();
                    errorMessage = validationErrors.join(', ');
                }
                
                this.showSnackbar(errorMessage, 'error');
            } finally {
                this.saveLoading = false;
            }
        },


        showSnackbar(text, color = 'success') {
            this.snackbar = {
                show: true,
                text,
                color,
            };
        },

        openCustomerAddDialog() {
            if (this.$refs.customerAddDialog) {
                this.$refs.customerAddDialog.open();
            }
        },

        async handleCustomerAdded(newCustomer) {
            try {
                const updatedCarData = {
                    ...this.carDetails.data,
                    customer_id: newCustomer.id
                };

                await axios.put(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`,
                    updatedCarData
                );
                await this.getCar();

                this.showSnackbar(`Neuer Kunde ${newCustomer.firstname} ${newCustomer.lastname} wurde erfolgreich erstellt und mit dem Fahrzeug verknüpft`, 'success');
            } catch (error) {
                const errorMessage = error.response?.data?.message || "Fehler beim Verknüpfen des Kunden mit dem Fahrzeug";
                this.handleCustomerAddError(errorMessage);
            }
        },

        async handleCustomerSelected(customer) {
            try {
                const updatedCarData = {
                    ...this.carDetails.data,
                    customer_id: customer.id
                };

                await axios.put(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}`,
                    updatedCarData
                );

                await this.getCar();
                this.showSnackbar(`Kunde ${customer.firstname} ${customer.lastname} wurde erfolgreich mit dem Fahrzeug verknüpft`, 'success');
            } catch (error) {
                const errorMessage = error.response?.data?.message || "Fehler beim Verknüpfen des Kunden mit dem Fahrzeug";
                this.handleCustomerAddError(errorMessage);
            }
        },

        handleCustomerAddError(errorMessage) {
            this.showSnackbar(errorMessage, 'error');
        },

        

        // Utility methods
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        async fetchCustomers(query = '') {
            this.customersLoading = true;
            console.log('Fetching customers with query:', query);
            try {
                const response = await axios.get(`/api/customers/search?query=${query}`);
                console.log('Customer API response:', response.data);
                this.customers = response.data.data.map(customer => ({
                    id: customer.id,
                    firstname: customer.firstname,
                    lastname: customer.lastname,
                    full_name: `${customer.firstname} ${customer.lastname}`,
                    email: customer.email
                }));
                console.log('Mapped customers:', this.customers);
            } catch (error) {
                console.error('Error fetching customers:', error.response || error);
                this.showSnackbar('Fehler beim Laden der Kunden', 'error');
            } finally {
                this.customersLoading = false;
            }
        },

        searchCustomers(query) {
            // Debounce the search to avoid too many API calls
            if (this.customerSearchTimeout) {
                clearTimeout(this.customerSearchTimeout);
            }
            this.customerSearchTimeout = setTimeout(() => {
                this.fetchCustomers(query);
            }, 300);
        }
    },
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

/* Drag & Drop Zone */
.drag-drop-zone {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #fafafa;
    position: relative;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.drag-drop-zone:hover:not(.disabled) {
    border-color: #1976d2;
    background-color: #f3f8ff;
}

.drag-drop-zone.drag-over {
    border-color: #1976d2;
    background-color: #e3f2fd;
    transform: scale(1.02);
}

.drag-drop-zone.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #f5f5f5;
}

.drag-drop-content {
    width: 100%;
}

.drag-drop-content h3 {
    font-weight: 500;
    color: #424242;
}

.drag-drop-content p {
    font-size: 14px;
    margin-bottom: 0;
}

/* General styling */
.v-file-input {
    margin-bottom: 16px;
}

.v-img {
    border-radius: 8px;
}

.v-alert {
    margin-top: 16px;
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