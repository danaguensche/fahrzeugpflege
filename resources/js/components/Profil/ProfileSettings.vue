<template>
    <v-card>
        <v-card-title>
            Persönliche Informationen
        </v-card-title>
        <v-table>
            <tbody>
                <tr v-for="(value, key) in userData" :key="key">
                    <td class="text-left">{{ labels[key] }}</td>
                    <td class="text-left">{{ value }}</td>
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
            userData: {}, 
            labels: {
                firstName: "Vorname",
                lastName: "Nachname",
                phoneNumber: "Telefonnummer",
                email: "E-Mail",
                addressLine: "Straße und Hausnummer",
                postalCode: "PLZ",
                city: "Ort"
            }
        };
    },
    mounted() {
        this.getUser();
    },
    methods: {
        async getUser() {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    console.error("Kein Token gefunden!");
                    return;
                }

                const response = await axios.get("http://localhost:8000/api/users/me", {
                    headers: { Authorization: `Bearer ${token}` }
                });

                console.log("API-Antwort:", response.data);
                this.userData = {
                    firstName: response.data.data.firstName || "",
                    lastName: response.data.data.lastName || "",
                    phoneNumber: response.data.data.phoneNumber || "",
                    email: response.data.data.email || "",
                    addressLine: response.data.data.addressLine || "",
                    postalCode: response.data.data.postalCode || "",
                    city: response.data.data.city || ""
                };

                console.log("Benutzerdaten geladen:", this.userData);
            } catch (error) {
                console.error("Fehler beim Laden der Benutzerdaten:", error);
            }
        }
    }
};
</script>
