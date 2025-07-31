<template>
  <v-row>
    <v-col v-for="image in images" :key="image.id" cols="12" sm="6" md="4" lg="3">
      <v-card>
        <v-img :src="`/storage/${image.path}`" height="200px"></v-img>
        <v-card-text>
          <p>{{ image.description }}</p>
          <p>Uploaded by: {{ image.user.name }}</p>
          <p>Uploaded at: {{ new Date(image.created_at).toLocaleString() }}</p>
          <v-icon v-if="image.path.endsWith('.jpg') || image.path.endsWith('.jpeg')">mdi-image</v-icon>
          <v-icon v-if="image.path.endsWith('.png')">mdi-file-image</v-icon>
          <v-icon v-if="image.path.endsWith('.webp')">mdi-image-multiple</v-icon>
        </v-card-text>
        <v-card-actions>
          <v-btn v-if="canDelete(image)" @click="deleteImage(image.id)">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({ carId: Number, jobId: Number });
const images = ref([]);
const user = ref(null);

const fetchImages = async () => {
  try {
    const response = await axios.get('/api/images', { params: { car_id: props.carId, job_id: props.jobId } });
    images.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/users/me');
    user.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const canDelete = (image) => {
  if (!user.value) return false;
  return user.value.id === image.user_id || user.value.role === 'trainer' || user.value.role === 'admin';
};

const deleteImage = async (id) => {
  try {
    await axios.delete(`/api/images/${id}`);
    images.value = images.value.filter(image => image.id !== id);
    // TODO: Add success notification
  } catch (error) {
    // TODO: Add error notification
    console.error(error);
  }
};

onMounted(() => {
  fetchImages();
  fetchUser();
});
</script>
