<template>
    <v-card>
        <v-card-title>
            Persönliche Informationen
        </v-card-title>

        <v-table>
            <tbody>
                <tr>
                    <td class="text-left">Vorname</td>
                    <td class="text-left">{{ firstName }} test</td>
                </tr>
                <tr>
                    <td class="text-left">Nachname</td>
                    <td class="text-left">{{ lastName }} test</td>
                </tr>
                <tr>
                    <td class="text-left">Telefonnummer</td>
                    <td class="text-left">{{ phoneNumber }} test</td>
                </tr>
                <tr>
                    <td class="text-left">E-Mail</td>
                    <td class="text-left">{{ email }} test</td>
                </tr>
                <tr>
                    <td class="text-left">Straße und Hausnummer</td>
                    <td class="text-left">{{ addressLine }} test</td>
                </tr>
                <tr>
                    <td class="text-left">PLZ</td>
                    <td class="text-left">{{ postalCode }} test</td>
                </tr>
                <tr>
                    <td class="text-left">Ort</td>
                    <td class="text-left">{{ city }} test</td>
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
            phoneNumber: '',
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
            console.log('Token: ' + localStorage.getItem('token'));
            this.loading = true;
            this.error = null;
            axios.get('http://localhost:8000/api/employee', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            })
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