<template>
    <v-sheet class="mb-3">
        <v-file-input v-model="imageFiles" label="Bilder hochladen" accept="image/*" prepend-icon="mdi-camera"
            @change="previewImages" multiple chips show-size :loading="uploadingImages"
            :disabled="uploadingImages"></v-file-input>

        <v-row v-if="imagePreviewUrls.length > 0" class="mt-2">
            <v-col v-for="(preview, index) in imagePreviewUrls" :key="index" cols="12" sm="6" md="4" lg="3">
                <v-card outlined>
                    <v-img :src="preview" height="150" contain></v-img>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn icon @click="removePreviewImage(index)">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-sheet>
</template>

<script>
export default {
    name: 'ImageUpload',
    props: {
        images: {
            type: Array,
            default: () => []
        }
    },
}
</script>