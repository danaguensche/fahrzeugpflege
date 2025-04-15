<template>
  <div class="car-details">

    <template v-if="loading">
      <v-card title="Fahrzeugdetails" variant="outlined" loading>
      </v-card>
    </template>
    <template v-else>
      <v-card>
        <v-card-title>
          Fahrzeugdetails
          <v-spacer></v-spacer>
        </v-card-title>
        <v-table>
          <tbody>
            <tr v-for="(value, key) in carDetails.data" :key="key" v-if="key !== 'image'">
              <template v-if="key === 'Kennzeichen'">
                <td class="text-left">{{ labels[key] || key }}</td>
                <td class="text-left">{{ formattedKennzeichen }}</td>
              </template>
              <template v-else-if="key === 'created_at'">
                <td class="text-left">{{ labels[key] || key }}</td>
                <td class="text-left">{{ formattedCreatedAt }}</td>
              </template>
              <template v-else-if="key === 'updated_at'">
                <td class="text-left">{{ labels[key] || key }}</td>
                <td class="text-left">{{ formattedUpdatedAt }}</td>
              </template>
              <template v-else-if="key === 'image'">
                <!-- nichts tun -->
              </template>
              <template v-else>
                <td class="text-left">{{ labels[key] || key }}</td>
                <td class="text-left">{{ value }}</td>
              </template>
            </tr>

          </tbody>
        </v-table>

        <v-img v-if="carDetails.data?.image" :src="carDetails.data.image" :width="400" cover>
          <template v-slot:placeholder>
            <div class="d-flex align-center justify-center fill-height">
              <v-progress-circular indeterminate color="grey lighten-2"></v-progress-circular>
            </div>
          </template>
        </v-img>
        <v-card-actions>
          <v-btn color="primary" @click="$router.push(`/fahrzeuge`)">Zurück zur Fahrzeugliste</v-btn>
        </v-card-actions>
      </v-card>
    </template>

  </div>
</template>

<script>
export default {
  data() {
    return {
      carDetails: {},
      labels: {
        id: "ID",
        Kennzeichen: "Kennzeichen",
        Fahrzeugklasse: "Fahrzeugklasse",
        Automarke: "Automarke",
        Typ: "Typ",
        Farbe: "Farbe",
        Sonstiges: "Sonstiges",
        customer_id: "Kunde",
        created_at: "Erstellt am",
        updated_at: "Zuletzt aktualisiert am"
      },
      loading: true,
      error: null
    };
  },
  computed: {

    formattedKennzeichen() {
      return this.$route.params.kennzeichen.replace(/\+/g, ' ') || 'Ungültiges Kennzeichen';
    },
    formattedDetails() {
      return JSON.stringify(this.carDetails, null, 2);
    },
    formattedCreatedAt() {
      return this.carDetails.data.created_at
        ? new Date(this.carDetails.data.created_at).toLocaleDateString('de-DE')
        : 'Unbekannt';
    },
    formattedUpdatedAt() {
      return this.carDetails.data.updated_at
        ? new Date(this.carDetails.data.updated_at).toLocaleDateString('de-DE')
        : 'Unbekannt';
    }
  },
  async mounted() {
    console.log("Empfangener Parameter:", this.$route.params.kennzeichen);
    console.log("Vollständige Route:", this.$route);
    await this.getCar();
  },
  methods: {
    async getCar() {
      try {
        const { data } = await axios.get(
          `/api/cars/cardetails/${this.$route.params.kennzeichen}`
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
  margin-left: 200px;
  padding: 20px;
  background-color: #f9f9f9;

}
</style>