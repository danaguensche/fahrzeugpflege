<template>
    <v-card>
        <v-card-title>
            Profil bearbeiten
        </v-card-title>

        <v-card-text>
            <v-text-field v-model="firstName" label="Vorname"></v-text-field>
            <v-text-field v-model="lastName" label="Nachname"></v-text-field>
            <v-text-field v-model="email" label="E-Mail Adresse"></v-text-field>
            <v-text-field v-model="addressLine" label="Straße und Hausnummer"></v-text-field>
            <v-text-field v-model="postalCode" label="PLZ"></v-text-field>
            <v-text-field v-model="city" label="Stadt"></v-text-field>
            <v-btn class="mt-2" type="submit" >Speichern</v-btn>
        </v-card-text>
    </v-card>
</template>

<script>
import axios from 'axios';

export default {
    name: "ProfileSettings",
    data() {
        return {
            firstName: '',
            lastName: '',
            email: '',
            addressLine: '',
            postalCode: '',
            city: ''
        };
    },

    mounted() {
        this.getUser();
    },

    methods: {
        getUser() {
            this.loading = true;
            this.error = null;
            axios.get('/api/employee')
                .then(response => {
                    const data = response.data.employee;
                    this.firstName = data.firstname;
                    this.lastName = data.lastname;
                    this.email = data.email;
                    this.addressLine = data.addressline;
                    this.postalCode = data.postalcode;
                    this.city = data.city;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Fehler beim Laden der Benutzerdaten:', error);
                    this.error = 'Fehler beim Laden der Benutzerdaten. Bitte versuchen Sie es später erneut.';
                    this.loading = false;
                });
        }
    }
}
</script>