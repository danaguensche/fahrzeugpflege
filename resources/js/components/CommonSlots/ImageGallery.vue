<template>
    <div class="image-gallery-wrapper">
        <!-- Image Carousel -->
        <ImageCarousel 
            :images="images" 
            :editMode="editMode"
            @upload-image="openImageUploadDialog"
            @delete-image="handleImageDelete"
            @replace-image="handleImageReplace"
            :canEdit="canEdit">
        </ImageCarousel>

        <!-- Image Upload Dialog -->
        <v-dialog v-model="imageUploadDialog.show" max-width="700px" persistent v-if="canEdit">
            <v-card>
                <v-card-title class="d-flex align-center">
                    <v-icon left>mdi-upload</v-icon>
                    <span class="headline">{{ uploadDialogTitle }}</span>
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
    </div>
</template>

<script>
import axios from "axios";
import ImageCarousel from "../Details/ImageCarousel.vue";

export default {
    name: "ImageGallery",
    components: {
        ImageCarousel
    },
    props: {
        images: {
            type: Array,
            default: () => []
        },
        editMode: {
            type: Boolean,
            default: false
        },
        canEdit: {
            type: Boolean,
            default: true
        },
        uploadUrl: {
            type: String,
            required: true
        },
        deleteUrlTemplate: {
            type: String,
            default: "/api/images/{imageId}"
        },
        replaceUrlTemplate: {
            type: String,
            default: null
        },
        uploadDialogTitle: {
            type: String,
            default: "Bild hochladen"
        },
        maxFileSize: {
            type: Number,
            default: 5242880 // 5MB
        },
        acceptedTypes: {
            type: Array,
            default: () => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg']
        },
        entityId: {
            type: [String, Number],
            default: null
        },
        apiHeaders: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            imageUploadDialog: {
                show: false,
                selectedFiles: [],
                imagePreviews: [],
                uploading: false,
                uploadProgress: 0,
                errorMessage: '',
                isDragOver: false,
                dragCounter: 0
            }
        };
    },
    computed: {
        defaultHeaders() {
            return {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                ...this.apiHeaders
            };
        }
    },
    methods: {
        openUploadDialog() {
            this.openImageUploadDialog();
        },

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
            if (file.size > this.maxFileSize) {
                return {
                    valid: false,
                    message: `Jede Datei darf maximal ${this.formatFileSize(this.maxFileSize)} groß sein.`
                };
            }

            if (!this.acceptedTypes.includes(file.type)) {
                return {
                    valid: false,
                    message: `Nur Bilder im Format ${this.acceptedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ')} sind erlaubt`
                };
            }

            return { valid: true };
        },

        createImagePreviews(files) {
            this.imageUploadDialog.imagePreviews = [];
            for (const file of files) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUploadDialog.imagePreviews.push({ 
                        src: e.target.result, 
                        name: file.name, 
                        size: file.size 
                    });
                };
                reader.readAsDataURL(file);
            }
        },

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
                    this.uploadUrl,
                    formData,
                    {
                        headers: { 
                            'Content-Type': 'multipart/form-data',
                            ...this.defaultHeaders
                        },
                        onUploadProgress: progressEvent => {
                            if (progressEvent.lengthComputable) {
                                this.imageUploadDialog.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            }
                        }
                    }
                );

                this.imageUploadDialog.uploadProgress = 100;
                this.$emit('images-uploaded', response.data);
                this.$emit('success', 'Bilder erfolgreich hochgeladen');
                
                setTimeout(() => this.closeImageUploadDialog(), 500);

            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Fehler beim Hochladen der Bilder';
                this.handleImageUploadError(errorMessage);
                this.$emit('error', errorMessage);
            } finally {
                this.imageUploadDialog.uploading = false;
            }
        },

        handleImageDelete(imageId) {
            try {
                console.log('Attempting to delete image with ID:', imageId);
                if (imageId) {
                    this.deleteImageFromServer(imageId);
                } else {
                    console.error('Image ID is missing, cannot delete from server.');
                    this.$emit('error', 'Fehler: Bild-ID fehlt.');
                }
            } catch (error) {
                console.error('Error in handleImageDelete:', error);
                this.$emit('error', 'Fehler beim Löschen des Bildes');
            }
        },

        async deleteImageFromServer(imageId) {
            try {
                this.$emit('loading', true);
                
                const deleteUrl = this.deleteUrlTemplate.replace('{imageId}', imageId);
                
                const response = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        ...this.defaultHeaders
                    }
                });

                if (response.ok) {
                    this.$emit('image-deleted', imageId);
                    this.$emit('success', 'Bild erfolgreich gelöscht');
                } else {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
            } catch (error) {
                console.error('Error deleting image from server:', error);
                this.$emit('error', 'Fehler beim Löschen des Bildes vom Server');
            } finally {
                this.$emit('loading', false);
            }
        },

        async handleImageReplace(imageIndex, newImageFile) {
            if (!this.replaceUrlTemplate || !this.entityId) {
                this.$emit('error', 'Bild-Ersetzung ist nicht konfiguriert');
                return;
            }

            this.$emit('loading', true);
            
            try {
                const image = this.images[imageIndex];
                if (!image) {
                    throw new Error('Bild nicht gefunden');
                }

                const imageId = image.id;
                if (!imageId) {
                    throw new Error('Bild-ID nicht gefunden');
                }

                console.log('Replacing image with ID:', imageId);

                const formData = new FormData();
                formData.append('image', newImageFile);

                const replaceUrl = this.replaceUrlTemplate
                    .replace('{entityId}', this.entityId)
                    .replace('{imageId}', imageId);

                await axios.post(
                    replaceUrl,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            ...this.defaultHeaders
                        },
                    }
                );

                this.$emit('image-replaced', { imageIndex, imageId, newImageFile });
                this.$emit('success', 'Bild erfolgreich ersetzt');
                
            } catch (error) {
                console.error('Error replacing image:', error);
                const errorMessage = error.response?.data?.error || error.message || 'Fehler beim Ersetzen des Bildes';
                this.$emit('error', errorMessage);
            } finally {
                this.$emit('loading', false);
            }
        },

        // Error handling
        handleImageUploadError(message) {
            this.imageUploadDialog.errorMessage = message;
        },

        // Utility methods
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
};
</script>

<style scoped>
.image-gallery-wrapper {
    width: 100%;
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
</style>