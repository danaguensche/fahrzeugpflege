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
            <tr v-for="(value, key) in carDetails.data" :key="key">
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
              <template v-else-if="key !== 'images' && key !== 'customer'">
                <td class="text-left">{{ labels[key] || key }}</td>
                <td class="text-left">{{ value }}</td>
              </template>
              <template v-else-if="key === 'customer'">
                <td class="text-left">{{ labels[key] || key }}</td>
                <td class="text-left">
                  <span v-if="carDetails.data.customer">
                    <a :href="linkToCustomer" >{{ carDetails.data.customer.firstname }} {{ carDetails.data.customer.lastname }} </a>
                  </span>
                  <span v-else>
                    Kein Kunde zugeordnet
                  </span>
                </td>
              </template>
              <template v-else-if="key === 'customer_id'">
                <td class="text-left">{{ labels[key] || key }}</td>
                <td class="text-left">
                  {{ value }}
                </td>
              </template>
            </tr>
          </tbody>
        </v-table>

        <template v-if="images.length > 0">
          <v-carousel cover>
            <v-carousel-item v-for="(image, index) in images" :key="index" :src="image">
              <template v-slot:placeholder>
                <div class="d-flex align-center justify-center fill-height">
                  <v-progress-circular indeterminate color="grey lighten-2"></v-progress-circular>
                </div>
              </template>
            </v-carousel-item>
          </v-carousel>
        </template>
        <template v-else>
          <p>Keine Bilder vorhanden</p>
        </template>

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
      carDetails: {
        data:{
          customer: {
            id: 0,
            firstname: '',
            lastname: '',
            email: ''
          },
        }
      },
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
      error: null
    };
  },
  computed: {
    images() {
      const img = this.carDetails.data?.images;
      
      if (!img) {
        return [];
      }
      
      if (Array.isArray(img)) {
        return img.filter(Boolean);
      }
      
      return img ? [img] : [];
    },
    formattedKennzeichen() {
      return this.$route.params.kennzeichen.replace(/\+/g, ' ') || 'Ungültiges Kennzeichen';
    },
    formattedDetails() {
      return JSON.stringify(this.carDetails, null, 2);
    },
    formattedCreatedAt() {
      return this.carDetails.data?.created_at
        ? new Date(this.carDetails.data.created_at).toLocaleDateString('de-DE')
        : 'Unbekannt';
    },
    formattedUpdatedAt() {
      return this.carDetails.data?.updated_at
        ? new Date(this.carDetails.data.updated_at).toLocaleDateString('de-DE')
        : 'Unbekannt';
    },
    linkToCustomer() {
      return `/kunden/${this.carDetails.data.customer.id}`;
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
        console.log("Fahrzeugdetails:", this.carDetails);
        console.log("Kundendetails:", this.carDetails.data.customer);
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

.v-carousel {
  margin-top: 20px;
  width: 800px;
  height: 500px;
}

.v-carousel-item {
  display: flex;
  justify-content: center;
  align-items: center;
}

.v-carousel-item img {
  max-width: 500px;
  max-height: 400px;
  object-fit: cover;
  border-radius: 8px;
}
</style>