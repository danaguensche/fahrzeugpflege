<template>
    <v-card>
        <v-card-title>
            Persönliche Details
        </v-card-title>

        <v-card-text v-if="loading">
            Lade Daten...
        </v-card-text>

        <v-card-text v-else-if="error">
            {{ error }}
        </v-card-text>

        <v-table v-else>
            <tbody>
                <tr>
                    <td>Vorname</td>
                    <td>{{ firstName }}</td>
                </tr>
                <tr>
                    <td>Nachname</td>
                    <td>{{ lastName }}</td>
                </tr>
                <tr>
                    <td>E-Mail</td>
                    <td>{{ email }}</td>
                </tr>
                <tr>
                    <td>Postleitzahl</td>
                    <td>{{ postalCode }}</td>
                </tr>
                <tr>
                    <td>Stadt</td>
                    <td>{{ city }}</td>
                </tr>
                <tr>
                    <td>Straße und Hausnummer</td>
                    <td>{{ addressLine }}</td>
                </tr>
            </tbody>
        </v-table>
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
            city: '',
            loading: true,
            error: null
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
