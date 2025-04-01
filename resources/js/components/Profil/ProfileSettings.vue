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
            userId: null,
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
        this.loadUserId();
    },

    methods: {
        loadUserId() {
            const storedUserId = localStorage.getItem('userId');

            if (storedUserId) {
                this.userId = storedUserId;
                this.getUser();
            } else {
                axios.get('http://localhost:8000/api/user', {
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                })
                    .then(response => {
                        this.userId = response.data.id;
                        localStorage.setItem('userId', this.userId);
                        this.getUser();
                    })
                    .catch(error => {
                        console.error('Fehler beim Abrufen der Benutzer-ID:', error);
                    });
            }
        },

        getUser() {
            if (!this.userId) {
                console.error('Keine Benutzer-ID gefunden!');
                return;
            }

            axios.get(`http://localhost:8000/api/employee/${this.userId}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    let data = response.data.employee || {};

                    this.firstName = data.firstname || '';
                    this.lastName = data.lastname || '';
                    this.phoneNumber = data.phonenumber || '';
                    this.email = data.email || '';
                    this.addressLine = data.addressline || '';
                    this.postalCode = data.postalcode || '';
                    this.city = data.city || '';

                    console.log('Benutzerdaten geladen:', response.data);
                })
                .catch(error => {
                    console.error('Fehler beim Laden der Benutzerdaten:', error);
                });
        }
    }
}
</script>