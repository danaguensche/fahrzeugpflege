<template>
    <v-container class="card-container">
        <!-- Skeleton Loader wenn loading true ist -->
        <Loader v-if="loading"></Loader>

        <!-- Vollständige Ansicht der Daten wenn loading false ist -->
        <template v-else>
            <!-- Header -->
            <v-card class="card">
                <Header :title="headerTitle" :switchEditMode="switchEditMode" :icon="headerIcon"></Header>

                <!-- Image Carousel with Upload functionality -->
                <ImageCarousel 
                    :images="images" 
                    :editMode="editMode"
                    @upload-image="openImageUploadDialog"
                    @delete-image="handleImageDelete"
                    @replace-image="handleImageReplace"
                    :canEdit="isAdminOrTrainer">
                </ImageCarousel>

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

        <!-- Image Upload Dialog -->
        <v-dialog v-model="imageUploadDialog.show" max-width="700px" persistent v-if="isAdminOrTrainer">
            <v-card>
                <v-card-title class="d-flex align-center">
                    <v-icon left>mdi-upload</v-icon>
                    <span class="headline">Bild hochladen</span>
                </v-card-title>

                <v-card-text>
                    <v-container>
                        <v-row>
                            <!-- Drag & Drop Zone -->
                            <v-col cols="12">
                                <div
                                    class="drag-drop-zone"
                                    :class="{ 'drag-over': imageUploadDialog.isDragOver, 'disabled': imageUploadDialog.uploading }"
                                    @dragover.prevent="handleDragOver"
                                    @dragleave.prevent="handleDragLeave"
                                    @drop.prevent="handleDrop"
                                    @click="triggerFileInput"
                                >
                                    <input
                                        ref="fileInput"
                                        type="file"
                                        multiple
                                        accept="image/*"
                                        style="display: none;"
                                        @change="handleFileSelect"
                                    />

                                    <div class="drag-drop-content">
                                        <v-icon size="48" :color="imageUploadDialog.isDragOver ? 'primary' : 'grey lighten-1'">
                                            {{ imageUploadDialog.isDragOver ? 'mdi-cloud-upload' : 'mdi-cloud-upload-outline' }}
                                        </v-icon>
                                        
                                        <h3 class="mt-4 mb-2">
                                            {{ imageUploadDialog.isDragOver ? 'Dateien hier ablegen' : 'Bilder hochladen' }}
                                        </h3>
                                        
                                        <p class="grey--text">
                                            Ziehen Sie Bilder hierher oder klicken Sie zum Auswählen
                                        </p>
                                    </div>

                                    <!-- Loading Overlay -->
                                    <v-overlay v-if="imageUploadDialog.uploading" absolute>
                                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                    </v-overlay>
                                </div>
                            </v-col>

                            <!-- File Input Alternative -->
                            <v-col cols="12">
                                <v-file-input
                                    v-model="imageUploadDialog.selectedFiles"
                                    multiple
                                    accept="image/*"
                                    label="Bilder auswählen"
                                    prepend-icon="mdi-camera"
                                    show-size
                                    @change="handleFileChange"
                                    outlined
                                    class="mt-4"
                                ></v-file-input>
                            </v-col>

                            <!-- Image Preview -->
                            <v-col cols="12" v-if="imageUploadDialog.imagePreviews.length > 0">
                                <v-card outlined>
                                    <v-card-subtitle class="d-flex align-center">
                                        <v-icon left small>mdi-eye</v-icon>
                                        Vorschau:
                                    </v-card-subtitle>
                                    <v-row dense class="pa-2">
                                        <v-col v-for="(preview, index) in imageUploadDialog.imagePreviews" :key="index" cols="12" sm="6" md="4">
                                            <v-card outlined>
                                                <v-img
                                                    :src="preview.src"
                                                    height="150"
                                                    contain
                                                    class="ma-2"
                                                ></v-img>
                                                <v-card-text class="pt-0">
                                                    <v-chip small color="primary" outlined class="mr-2">
                                                        {{ formatFileSize(preview.size) }}
                                                    </v-chip>
                                                    <v-chip small color="grey" outlined>
                                                        {{ preview.name }}
                                                    </v-chip>
                                                </v-card-text>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-col>

                            <!-- Upload Progress -->
                            <v-col cols="12" v-if="imageUploadDialog.uploading">
                                <v-progress-linear
                                    :value="imageUploadDialog.uploadProgress"
                                    color="primary"
                                    height="25"
                                    rounded
                                >
                                    <template v-slot:default="{ value }">
                                        <strong>{{ Math.ceil(value) }}%</strong>
                                    </template>
                                </v-progress-linear>
                                <p class="text-center mt-2 grey--text">
                                    Bild wird hochgeladen...
                                </p>
                            </v-col>

                            <!-- Error Message -->
                            <v-col cols="12" v-if="imageUploadDialog.errorMessage">
                                <v-alert
                                    type="error"
                                    dismissible
                                    @input="imageUploadDialog.errorMessage = ''"
                                >
                                    {{ imageUploadDialog.errorMessage }}
                                </v-alert>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey darken-1"
                        text
                        @click="closeImageUploadDialog"
                        :disabled="imageUploadDialog.uploading"
                    >
                        Abbrechen
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="uploadImages"
                        :disabled="imageUploadDialog.selectedFiles.length === 0 || imageUploadDialog.uploading"
                        :loading="imageUploadDialog.uploading"
                    >
                        <v-icon left>mdi-upload</v-icon>
                        Hochladen
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

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
        CustomerAddDialog
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
            // Image upload dialog data
            imageUploadDialog: {
                show: false,
                selectedFiles: [],
                imagePreviews: [],
                uploading: false,
                uploadProgress: 0,
                errorMessage: '',
                isDragOver: false,
                dragCounter: 0,
                maxFileSize: 5242880, // 5MB
                acceptedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg']
            }
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

        // Image Upload Dialog Methods
        openImageUploadDialog() {
            this.imageUploadDialog.show = true;
            this.resetImageUploadForm();
        },

        closeImageUploadDialog() {
            this.imageUploadDialog.show = false;
            this.resetImageUploadForm();
        },

        resetImageUploadForm() {
            this.imageUploadDialog.selectedFiles = [];
            this.imageUploadDialog.imagePreviews = [];
            this.imageUploadDialog.uploading = false;
            this.imageUploadDialog.uploadProgress = 0;
            this.imageUploadDialog.errorMessage = '';
            this.imageUploadDialog.isDragOver = false;
            this.imageUploadDialog.dragCounter = 0;
        },

        // Drag & Drop handlers
        handleDragOver(e) {
            if (this.imageUploadDialog.uploading) return;
            e.preventDefault();
            this.imageUploadDialog.dragCounter++;
            this.imageUploadDialog.isDragOver = true;
        },

        handleDragLeave(e) {
            if (this.imageUploadDialog.uploading) return;
            e.preventDefault();
            this.imageUploadDialog.dragCounter--;
            if (this.imageUploadDialog.dragCounter === 0) {
                this.imageUploadDialog.isDragOver = false;
            }
        },

        handleDrop(e) {
            if (this.imageUploadDialog.uploading) return;
            e.preventDefault();
            this.imageUploadDialog.isDragOver = false;
            this.imageUploadDialog.dragCounter = 0;

            const files = Array.from(e.dataTransfer.files);
            if (files.length > 0) {
                this.handleFileChange(files);
            }
        },

        triggerFileInput() {
            if (this.imageUploadDialog.uploading) return;
            this.$refs.fileInput.click();
        },

        handleFileSelect(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                this.handleFileChange(files);
            }
            // Reset input
            e.target.value = '';
        },

        // File validation and processing
        handleFileChange(files) {
            if (!files || files.length === 0) {
                this.imageUploadDialog.selectedFiles = [];
                this.imageUploadDialog.imagePreviews = [];
                return;
            }

            const validFiles = [];
            for (const file of files) {
                const validation = this.validateFile(file);
                if (validation.valid) {
                    validFiles.push(file);
                } else {
                    this.handleImageUploadError(validation.message);
                }
            }

            this.imageUploadDialog.selectedFiles = validFiles;
            this.imageUploadDialog.errorMessage = '';
            this.createImagePreviews(validFiles);
        },

        validateFile(file) {
            if (file.size > this.imageUploadDialog.maxFileSize) {
                return {
                    valid: false,
                    message: `Jede Datei darf maximal ${this.formatFileSize(this.imageUploadDialog.maxFileSize)} groß sein.`
                };
            }

            if (!this.imageUploadDialog.acceptedTypes.includes(file.type)) {
                return {
                    valid: false,
                    message: `Nur Bilder im Format ${this.imageUploadDialog.acceptedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ')} sind erlaubt`
                };
            }

            return { valid: true };
        },

        createImagePreviews(files) {
            this.imageUploadDialog.imagePreviews = [];
            for (const file of files) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUploadDialog.imagePreviews.push({ src: e.target.result, name: file.name, size: file.size });
                };
                reader.readAsDataURL(file);
            }
        },

        // Upload functionality
        async uploadImages() {
            if (this.imageUploadDialog.selectedFiles.length === 0) {
                this.handleImageUploadError('Bitte wählen Sie ein oder mehrere Bilder aus');
                return;
            }

            this.imageUploadDialog.uploading = true;
            this.imageUploadDialog.uploadProgress = 0;
            this.imageUploadDialog.errorMessage = '';

            const formData = new FormData();
            for (const file of this.imageUploadDialog.selectedFiles) {
                formData.append('images[]', file);
            }

            try {
                const response = await axios.post(
                    `/api/cars/cardetails/${this.$route.params.kennzeichen}/images`,
                    formData,
                    {
                        headers: { 'Content-Type': 'multipart/form-data' },
                        onUploadProgress: progressEvent => {
                            if (progressEvent.lengthComputable) {
                                this.imageUploadDialog.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            }
                        }
                    }
                );

                // Refresh car details to get updated images
                await this.getCar();
                this.imageUploadDialog.uploadProgress = 100;
                this.showSnackbar('Bilder erfolgreich hochgeladen', 'success');
                setTimeout(() => this.closeImageUploadDialog(), 500);

            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Fehler beim Hochladen der Bilder';
                this.handleImageUploadError(errorMessage);
            } finally {
                this.imageUploadDialog.uploading = false;
            }
        },

        handleImageUploadError(message) {
            this.imageUploadDialog.errorMessage = message;
            this.showSnackbar(message, 'error');
        },

        handleImageDelete(imageId) {
            try {
                console.log('Attempting to delete image with ID:', imageId);
                if (imageId) {
                    this.deleteImageFromServer(imageId);
                } else {
                    console.error('Image ID is missing, cannot delete from server.');
                    this.showSnackbar('Fehler: Bild-ID fehlt.', 'error');
                }
            } catch (error) {
                console.error('Error in handleImageDelete:', error);
                this.showSnackbar('Fehler beim Löschen des Bildes', 'error');
            }
        },

        async deleteImageFromServer(imageId) {
            try {
                this.loading = true;
                const response = await fetch(`/api/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    await this.getCar(); // Refresh car details to update images
                    this.showSnackbar('Bild erfolgreich gelöscht', 'success');
                } else {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
            } catch (error) {
                console.error('Error deleting image from server:', error);
                this.showSnackbar('Fehler beim Löschen des Bildes vom Server', 'error');
            } finally {
                this.loading = false;
            }
        },

        async handleImageReplace(imageIndex, newImageFile) {
            this.imageUploadLoading = true;
            try {
                // Get image by index
                const image = this.images[imageIndex];
                if (!image) {
                    throw new Error('Bild nicht gefunden');
                }

                //Get image ID
                const imageId = image.id;
                if (!imageId) {
                    throw new Error('Bild-ID nicht gefunden');
                }

                console.log('Replacing image with ID:', imageId);

                const formData = new FormData();
                formData.append('image', newImageFile);

                await axios.post(
                    `/api/cars/${this.$route.params.kennzeichen}/images/${imageId}`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    }
                );

                // Refresh car details to get updated images
                await this.getCar();
                this.showSnackbar('Bild erfolgreich ersetzt', 'success');
            } catch (error) {
                console.error('Error replacing image:', error);
                const errorMessage = error.response?.data?.error || error.message || 'Fehler beim Ersetzen des Bildes';
                this.showSnackbar(errorMessage, 'error');
            } finally {
                this.imageUploadLoading = false;
            }
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