<template>
    <v-card>
        <v-card-title>
            Persönliche Informationen
        </v-card-title>

        <v-table>
            <tbody>
                <tr>
                    <td class="text-left">Vorname</td>
                    <td class="text-left">{{ firstName }}</td>
                </tr>
                <tr>
                    <td class="text-left">Nachname</td>
                    <td class="text-left">{{ lastName }}</td>
                </tr>
                <tr>
                    <td class="text-left">Telefonnummer</td>
                    <td class="text-left">{{ phoneNumber }}</td>
                </tr>
                <tr>
                    <td class="text-left">E-Mail</td>
                    <td class="text-left">{{ email }}</td>
                </tr>
                <tr>
                    <td class="text-left">Straße und Hausnummer</td>
                    <td class="text-left">{{ addressLine }}</td>
                </tr>
                <tr>
                    <td class="text-left">PLZ</td>
                    <td class="text-left">{{ postalCode }}</td>
                </tr>
                <tr>
                    <td class="text-left">Ort</td>
                    <td class="text-left">{{ city }}</td>
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
            axios.get('http://localhost:8000/api/employee/current', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            })
                .then(response => {
                    console.log(response.data);
                    let data = response.data.employees && response.data.employees.length > 0
                        ? response.data.employees[0]
                        : {};

                    this.firstName = data.firstname === null ? '' : data.firstname;
                    this.lastName = data.lastname === null ? '' : data.lastname;
                    this.email = data.email === null ? '' : data.email;
                    this.addressLine = data.addressline === null ? '' : data.addressline;
                    this.postalCode = data.postalcode === null ? '' : data.postalcode;
                    this.city = data.city === null ? '' : data.city;
                    this.loading = false;

                    console.log(data);
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