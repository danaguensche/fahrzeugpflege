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
                    @replace-image="handleImageReplace">
                </ImageCarousel>

                <!-- Fahrzeug information -->
                <v-card-text class="px-4 pt-4 pb-0">
                    <v-sheet>
                        <InformationHeader :title="'Fahrzeuginformationen'" :editMode="editMode">
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
                        <CustomerInfoList :customer="carDetails.data.customer" :customerId="carDetails.data.customer_id"
                            :labels="labels">
                        </CustomerInfoList>

                        <!-- Button wird nur angezeigt wenn noch kein Kunde eingetragen wurde -->
                        <v-btn class="mt-4" color="primary"
                            v-if="!carDetails.data.customer || carDetails.data.customer === 0"
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
                    <template v-if="editMode">
                        <CancelButton :cancelEdit="cancelEdit"></CancelButton>
                        <SaveButton :saveData="saveCarData"></SaveButton>
                    </template>

                    <!-- Ansichtsmodus Aktionen -->
                    <template v-else>
                        <EditButton :switchEditMode="switchEditMode"></EditButton>
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
        <v-dialog v-model="imageUploadDialog.show" max-width="700px" persistent>
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
                                    v-model="imageUploadDialog.selectedFile"
                                    accept="image/*"
                                    label="Bild auswählen"
                                    prepend-icon="mdi-camera"
                                    show-size
                                    @change="handleFileChange"
                                    :rules="fileRules"
                                    outlined
                                    class="mt-4"
                                ></v-file-input>
                            </v-col>

                            <!-- Image Preview -->
                            <v-col cols="12" v-if="imageUploadDialog.imagePreview">
                                <v-card outlined>
                                    <v-card-subtitle class="d-flex align-center">
                                        <v-icon left small>mdi-eye</v-icon>
                                        Vorschau:
                                    </v-card-subtitle>
                                    <v-img
                                        :src="imageUploadDialog.imagePreview"
                                        max-height="300"
                                        contain
                                        class="ma-2"
                                    ></v-img>
                                    
                                    <!-- Image Info -->
                                    <v-card-text v-if="imageUploadDialog.selectedFile" class="pt-0">
                                        <v-chip small color="primary" outlined class="mr-2">
                                            {{ formatFileSize(imageUploadDialog.selectedFile.size) }}
                                        </v-chip>
                                        <v-chip small color="grey" outlined>
                                            {{ imageUploadDialog.selectedFile.name }}
                                        </v-chip>
                                    </v-card-text>
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
                        @click="uploadImage"
                        :disabled="!imageUploadDialog.selectedFile || imageUploadDialog.uploading"
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
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
            // Image upload dialog data
            imageUploadDialog: {
                show: false,
                selectedFile: null,
                imagePreview: null,
                uploading: false,
                uploadProgress: 0,
                errorMessage: '',
                isDragOver: false,
                dragCounter: 0,
                maxFileSize: 10000000, // 10MB
                acceptedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
            }
        };
    },
    computed: {
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

            if (Array.isArray(img)) {
                return img.filter(Boolean).map(image => {
                    // Ensure correct image data structure
                    if (typeof image === 'string') {
                        return {
                            id: null,
                            path: image,
                            url: image
                        };
                    }
                    return {
                        id: image.id,
                        path: image.path,
                        url: image.url || `/storage/${image.path}`
                    };
                });
            }

            // If img is not an array, but has data
            if (typeof img === 'string') {
                return [{
                    id: null,
                    path: img,
                    url: img
                }];
            }

            return img ? [{
                id: img.id,
                path: img.path,
                url: img.url || `/storage/${img.path}`
            }] : [];
        },
        formattedCreatedAt() {
            return this.formatDate(this.carDetails.data?.created_at);
        },
        formattedUpdatedAt() {
            return this.formatDate(this.carDetails.data?.updated_at);
        },
        fileRules() {
            return [
                value => !value || value.size < this.imageUploadDialog.maxFileSize || `Bild darf nicht größer als ${this.formatFileSize(this.imageUploadDialog.maxFileSize)} sein`,
                value => !value || this.imageUploadDialog.acceptedTypes.includes(value.type) || `Nur Bilder im Format ${this.imageUploadDialog.acceptedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ')} sind erlaubt`
            ];
        }
    },
    async mounted() {
        try {
            await this.getCar();
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
                if (dataToSubmit.customer_id === '' || dataToSubmit.customer_id === 0 || dataToSubmit.customer_id === '0') {
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
            this.imageUploadDialog.selectedFile = null;
            this.imageUploadDialog.imagePreview = null;
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
                this.handleFileChange(files[0]);
            }
        },

        triggerFileInput() {
            if (this.imageUploadDialog.uploading) return;
            this.$refs.fileInput.click();
        },

        handleFileSelect(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                this.handleFileChange(files[0]);
            }
            // Reset input
            e.target.value = '';
        },

        // File validation and processing
        handleFileChange(file) {
            if (!file) {
                this.imageUploadDialog.selectedFile = null;
                this.imageUploadDialog.imagePreview = null;
                return;
            }

            // Validate file
            const validation = this.validateFile(file);
            if (!validation.valid) {
                this.handleImageUploadError(validation.message);
                this.imageUploadDialog.selectedFile = null;
                this.imageUploadDialog.imagePreview = null;
                return;
            }

            this.imageUploadDialog.selectedFile = file;
            this.imageUploadDialog.errorMessage = '';
            this.createImagePreview(file);
        },

        validateFile(file) {
            if (file.size > this.imageUploadDialog.maxFileSize) {
                return {
                    valid: false,
                    message: `Bild darf nicht größer als ${this.formatFileSize(this.imageUploadDialog.maxFileSize)} sein`
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

        createImagePreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imageUploadDialog.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        // Upload functionality
        async uploadImage() {
            if (!this.imageUploadDialog.selectedFile) {
                this.handleImageUploadError('Bitte wählen Sie ein Bild aus');
                return;
            }

            this.imageUploadDialog.uploading = true;
            this.imageUploadDialog.uploadProgress = 0;
            this.imageUploadDialog.errorMessage = '';

            const formData = new FormData();
            formData.append('images[]', this.imageUploadDialog.selectedFile);

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
                this.showSnackbar('Bild erfolgreich hochgeladen', 'success');
                setTimeout(() => this.closeImageUploadDialog(), 500);

            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Fehler beim Hochladen des Bildes';
                this.handleImageUploadError(errorMessage);
            } finally {
                this.imageUploadDialog.uploading = false;
            }
        },

        handleImageUploadError(message) {
            this.imageUploadDialog.errorMessage = message;
            this.showSnackbar(message, 'error');
        },

        handleImageDelete(index) {
            try {
                console.log('Attempting to delete image at index:', index);
                console.log('Image data:', this.images[index]);
                
                const image = this.images[index];
                
                if (image && image.id) {
                    this.deleteImageFromServer(image.id, index);
                } else {
                    this.images.splice(index, 1);
                    console.log('Image removed from local array (no ID)');
                    
                    if (this.$toast) {
                        this.$toast.success('Bild erfolgreich entfernt');
                    }
                }
                
            } catch (error) {
                console.error('Error deleting image:', error);
                if (this.$toast) {
                    this.$toast.error('Fehler beim Löschen des Bildes');
                }
            }
        },

        async deleteImageFromServer(imageId, index) {
            try {
                this.loading = true;
                
                // send DELETE query
                const response = await fetch(`/api/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        // add CSRF token
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    // delete image from local array 
                    this.images.splice(index, 1);
                    console.log('Image deleted successfully from server');
                    
                    if (this.$toast) {
                        this.$toast.success('Bild erfolgreich gelöscht');
                    }
                } else {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                
            } catch (error) {
                console.error('Error deleting image from server:', error);
                if (this.$toast) {
                    this.$toast.error('Fehler beim Löschen des Bildes vom Server');
                }
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
        }
    },
};
</script>

<style scoped>
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
</style>