<template>
    <v-card-text class="px-4 pt-0 pb-4" v-if="images.length > 0 || editMode">
        <!-- Header with Add Button in Edit Mode -->
        <div class="d-flex justify-space-between align-center mb-3">
            <h3 class="subtitle-1 font-weight-bold">Bilder</h3>
            <v-btn
                v-if="editMode"
                color="primary"
                small
                @click="$emit('upload-image')"
                :disabled="loading"
            >
                <v-icon left small>mdi-plus</v-icon>
                Bild hinzufügen
            </v-btn>
        </div>

        <!-- No Images Message -->
        <v-alert
            v-if="images.length === 0 && !editMode"
            type="info"
            text
            outlined
            class="mb-4"
        >
            <v-icon left>mdi-information-outline</v-icon>
            Keine Bilder verfügbar
        </v-alert>

        <!-- Add First Image Button -->
        <v-card
            v-if="images.length === 0 && editMode"
            outlined
            class="text-center pa-8 mb-4"
            style="border-style: dashed;"
        >
            <v-icon size="64" color="grey lighten-1" class="mb-4">mdi-image-plus</v-icon>
            <p class="grey--text">Noch keine Bilder hochgeladen</p>
            <v-btn
                color="primary"
                @click="$emit('upload-image')"
                :disabled="loading"
            >
                <v-icon left>mdi-upload</v-icon>
                Erstes Bild hochladen
            </v-btn>
        </v-card>

        <!-- Image Carousel -->
        <v-carousel
            v-if="images.length > 0"
            v-model="currentSlide"
            :show-arrows="images.length > 1"
            :hide-delimiters="images.length <= 1"
            height="400"
            class="rounded-lg"
        >
            <v-carousel-item
                v-for="(image, index) in images"
                :key="index"
                class="position-relative"
            >
                <v-img
                    :src="getImageUrl(image)"
                    height="400"
                    contain
                    class="white"
                    @error="handleImageError(index)"
                >
                    <template v-slot:placeholder>
                        <v-row class="fill-height ma-0" align="center" justify="center">
                            <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                        </v-row>
                    </template>
                </v-img>

                <!-- Edit Mode Overlay -->
                <div
                    v-if="editMode"
                    class="image-overlay d-flex justify-center align-center"
                >
                    <v-card class="pa-2" elevation="8">
                        <v-btn-toggle
                            v-model="selectedAction"
                            class="transparent"
                            mandatory
                        >
                            <v-btn
                                small
                                color="primary"
                                @click="openReplaceDialog(index)"
                                :disabled="loading"
                            >
                                <v-icon small>mdi-swap-horizontal</v-icon>
                                <span class="ml-1">Ersetzen</span>
                            </v-btn>
                            <v-btn
                                small
                                color="error"
                                @click="confirmDelete(index)"
                                :disabled="loading"
                            >
                                <v-icon small>mdi-delete</v-icon>
                                <span class="ml-1">Löschen</span>
                            </v-btn>
                        </v-btn-toggle>
                    </v-card>
                </div>

                <!-- Image Counter -->
                <v-chip
                    v-if="images.length > 1"
                    small
                    color="rgba(0,0,0,0.6)"
                    text-color="white"
                    class="position-absolute"
                    style="top: 16px; right: 16px;"
                >
                    {{ index + 1 }} / {{ images.length }}
                </v-chip>
            </v-carousel-item>
        </v-carousel>

        <!-- Image Navigation Thumbnails -->
        <div
            v-if="images.length > 1"
            class="d-flex justify-center mt-4"
            style="gap: 8px;"
        >
            <v-card
                v-for="(image, index) in images"
                :key="index"
                :class="['thumbnail-card', { 'active': currentSlide === index }]"
                @click="currentSlide = index"
                elevation="2"
                width="60"
                height="60"
            >
                <v-img
                    :src="getImageUrl(image)"
                    width="60"
                    height="60"
                    cover
                    class="thumbnail-img"
                >
                    <template v-slot:placeholder>
                        <v-row class="fill-height ma-0" align="center" justify="center">
                            <v-icon small color="grey lighten-5">mdi-image</v-icon>
                        </v-row>
                    </template>
                </v-img>
            </v-card>
        </div>

        <!-- Replace Image Dialog -->
        <v-dialog v-model="replaceDialog" max-width="500px">
            <v-card>
                <v-card-title>
                    <span class="headline">Bild ersetzen</span>
                </v-card-title>

                <v-card-text>
                    <v-file-input
                        v-model="replaceFile"
                        accept="image/*"
                        label="Neues Bild auswählen"
                        prepend-icon="mdi-camera"
                        show-size
                        @change="handleReplaceFileChange"
                        :rules="fileRules"
                    ></v-file-input>

                    <!-- Preview of new image -->
                    <v-img
                        v-if="replacePreview"
                        :src="replacePreview"
                        max-height="200"
                        contain
                        class="mt-4 rounded"
                    ></v-img>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey darken-1"
                        text
                        @click="closeReplaceDialog"
                    >
                        Abbrechen
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="replaceImage"
                        :disabled="!replaceFile || loading"
                        :loading="loading"
                    >
                        Ersetzen
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="400px">
            <v-card>
                <v-card-title class="headline">Bild löschen</v-card-title>
                <v-card-text>
                    Sind Sie sicher, dass Sie dieses Bild löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey darken-1"
                        text
                        @click="deleteDialog = false"
                    >
                        Abbrechen
                    </v-btn>
                    <v-btn
                        color="error"
                        @click="deleteImage"
                        :loading="loading"
                    >
                        Löschen
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card-text>
</template>

<script>
export default {
    name: "ImageCarousel",
    props: {
        images: {
            type: Array,
            default: () => []
        },
        editMode: {
            type: Boolean,
            default: false
        },
        loading: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            currentSlide: 0,
            selectedAction: null,
            replaceDialog: false,
            deleteDialog: false,
            replaceFile: null,
            replacePreview: null,
            replaceIndex: null,
            deleteIndex: null,
            fileRules: [
                value => !value || value.size < 10000000 || 'Bild darf nicht größer als 10 MB sein',
                value => !value || ['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(value.type) || 'Nur Bilder im Format JPEG, PNG, GIF oder WebP sind erlaubt'
            ]
        };
    },
    watch: {
        images: {
            handler(newImages) {
                // Reset current slide if it's out of bounds
                if (this.currentSlide >= newImages.length) {
                    this.currentSlide = Math.max(0, newImages.length - 1);
                }
            },
            immediate: true
        }
    },
    methods: {
        getImageUrl(image) {
            // Handle different image formats
            if (typeof image === 'string') {
                // If it's already a URL, return as is
                if (image.startsWith('http') || image.startsWith('data:')) {
                    return image;
                }
                // If it's a relative path, prepend base URL
                return `/api/images/${image}`;
            }
            
            // Handle image objects
            if (image && image.url) {
                return image.url;
            }
            
            // Handle image objects with path
            if (image && image.path) {
                return `/api/images/${image.path}`;
            }
            
            // Fallback
            return '/api/images/placeholder.jpg';
        },

        handleImageError(index) {
            console.error(`Failed to load image at index ${index}`);
            // You could emit an error event or show a placeholder
        },

        openReplaceDialog(index) {
            this.replaceIndex = index;
            this.replaceDialog = true;
        },

        closeReplaceDialog() {
            this.replaceDialog = false;
            this.replaceFile = null;
            this.replacePreview = null;
            this.replaceIndex = null;
        },

        handleReplaceFileChange(file) {
            if (file) {
                // Validate file
                const isValidSize = file.size < 10000000; // 10MB
                const isValidType = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type);

                if (!isValidSize || !isValidType) {
                    this.replaceFile = null;
                    this.replacePreview = null;
                    return;
                }

                this.createReplacePreview(file);
            } else {
                this.replacePreview = null;
            }
        },

        createReplacePreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.replacePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        replaceImage() {
            if (this.replaceFile && this.replaceIndex !== null) {
                this.$emit('replace-image', this.replaceIndex, this.replaceFile);
                this.closeReplaceDialog();
            }
        },

        confirmDelete(index) {
            this.deleteIndex = index;
            this.deleteDialog = true;
        },

        deleteImage() {
            if (this.deleteIndex !== null) {
                this.$emit('delete-image', this.deleteIndex);
                this.deleteDialog = false;
                this.deleteIndex = null;
            }
        }
    }
};
</script>

<style scoped>
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.v-carousel-item:hover .image-overlay {
    opacity: 1;
}

.thumbnail-card {
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.thumbnail-card:hover {
    opacity: 1;
    transform: scale(1.05);
}

.thumbnail-card.active {
    opacity: 1;
    border: 2px solid #1976d2;
}

.thumbnail-img {
    border-radius: 4px;
}

.position-absolute {
    position: absolute;
}

.position-relative {
    position: relative;
}
</style>