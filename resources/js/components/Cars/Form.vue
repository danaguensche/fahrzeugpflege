<template>
    <div class="form-wrapper" @submit.prevent="submitForm">
        <form class="page-form">
            <h2 class="forms-title">Fahrzeug dokumentieren</h2>
            <div v-for="field in formFields" :key="field.id" class="forms-field">
                <input :type="field.type" :id="field.id" :placeholder="field.placeholder" v-model="formData[field.id]"
                    class="forms-field-input" />
            </div>
            <SubmitButton>Speichern</SubmitButton>

        </form>

    </div>
</template>

<script>

import SubmitButton from '../Login/Slots/SubmitButton.vue';
import axios from 'axios';

export default {
    name: "Form",

    components: {
        SubmitButton,
    },

    data() {
        return {
            formData: {
                Kennzeichen: '',
                Fahrzeugklasse: '',
                Automarke: '',
                Typ: '',
                Farbe: '',
                Sonstiges: '',
                Bild: ''
            },

            formFields: [
                { id: 'Kennzeichen', name: 'Kennzeichen', type: 'text', placeholder: 'Kennzeichen' },
                { id: 'Fahrzeugklasse', name: 'Fahrzeugklasse', type: 'text', placeholder: 'Fahrzeugklasse' },
                { id: 'Automarke', name: 'Automarke', type: 'text', placeholder: 'Automarke' },
                { id: 'Typ', name: 'Typ', type: 'text', placeholder: 'Typ' },
                { id: 'Farbe', name: 'Farbe', type: 'text', placeholder: 'Farbe' },
                { id: 'Sonstiges', name: 'Sonstiges', type: 'text', placeholder: 'Sonstiges' },
            ]
        }
    },

    methods: {
        async submitForm() {
            try {
                const response = await axios.post('/fahrzeuge', this.formData);
                console.log('Fahrzeug erfolgreich gespeichert:', response.data);
            } catch (error) {
                console.error('Fehler beim Speichern des Fahrzeugs:', error);
            }
        }
    }

}

</script>

<style scoped>
.form-wrapper {
    box-shadow: var(--box-shadow);
    padding: 70px;
    align-self: flex-end;
    /* visibility: hidden; */
}

.forms-fieldset {
    margin-bottom: 10px;
}
</style>