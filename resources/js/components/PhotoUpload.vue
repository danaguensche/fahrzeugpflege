<template>
  <v-card>
    <v-card-title>Upload Photos</v-card-title>
    <v-card-text>
      <div
        class="drop-zone"
        @dragover.prevent
        @dragenter.prevent
        @drop.prevent="handleDrop"
      >
        <p>Drag & drop files here or click to select</p>
        <input type="file" multiple @change="handleChange" ref="fileInput" hidden>
        <v-btn @click="$refs.fileInput.click()">Select Files</v-btn>
      </div>
      <div v-if="files.length" class="preview-container">
        <div v-for="(file, index) in files" :key="index" class="preview-item">
          <img :src="file.preview" class="preview-img">
          <v-text-field label="Description" v-model="file.description"></v-text-field>
        </div>
      </div>
    </v-card-text>
    <v-card-actions>
      <v-btn @click="uploadFiles" :disabled="!files.length">Upload</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({ carId: Number, jobId: Number });
const files = ref([]);

const handleDrop = (e) => {
  addFiles(e.dataTransfer.files);
};

const handleChange = (e) => {
  addFiles(e.target.files);
};

const addFiles = (fileList) => {
  for (let i = 0; i < fileList.length; i++) {
    const file = fileList[i];
    files.value.push({
      file,
      preview: URL.createObjectURL(file),
      description: ''
    });
  }
};

const uploadFiles = async () => {
  for (const file of files.value) {
    const formData = new FormData();
    formData.append('image', file.file);
    formData.append('description', file.description);
    if (props.carId) formData.append('car_id', props.carId);
    if (props.jobId) formData.append('job_id', props.jobId);

    console.log('Uploading with job ID:', props.jobId);

    try {
      await axios.post('/api/images', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      // TODO: Add success notification
    } catch (error) {
      // TODO: Add error notification
      console.error(error);
    }
  }
  files.value = [];
};
</script>

<style scoped>
.drop-zone {
  border: 2px dashed #ccc;
  padding: 20px;
  text-align: center;
}
.preview-container {
  display: flex;
  flex-wrap: wrap;
  margin-top: 20px;
}
.preview-item {
  margin: 10px;
}
.preview-img {
  width: 150px;
  height: 150px;
  object-fit: cover;
}
</style>
