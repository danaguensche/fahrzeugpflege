<template>
  <div class="car-details">
    <h1 v-if="loading">Lade Daten...</h1>
    <template v-else>
      <h1>Fahrzeugdetails</h1>
      <p>Kennzeichen: {{ formattedKennzeichen }}</p>
      <pre v-if="carDetails.Kennzeichen">{{ formattedDetails }}</pre>
      <p v-else>Keine Daten gefunden</p>
    </template>
  </div>
</template>

<script>
export default {
  data() {
    return {
      carDetails: {},
      loading: true,
      error: null
    };
  },
  computed: {
    formattedKennzeichen() {
      return this.$route.params.Kennzeichen || 'Ungültiges Kennzeichen';
    },
    formattedDetails() {
      return JSON.stringify(this.carDetails, null, 2);
    }
  },
  async mounted() {
    console.log("Empfangener Parameter:", this.$route.params.Kennzeichen);
    console.log("Vollständige Route:", this.$route);
    await this.getCar();
  },
  methods: {
    async getCar() {
      try {
        const { data } = await axios.get(
          `/api/cars/${encodeURIComponent(this.$route.params.Kennzeichen)}`
        );
        this.carDetails = data;
      } catch (error) {
        this.error = error.response?.data?.message || error.message;
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.car-details {
  margin-left: 300px;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>