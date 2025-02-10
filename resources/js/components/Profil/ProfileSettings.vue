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
    }
}
</script>