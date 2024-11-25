<template>
    <div class="form-wrapper" @submit.prevent="submitForm">
        <form class="page-form">
            <h2 class="forms-title">Fahrzeug dokumentieren</h2>
            <fieldset class="forms-fieldset cars">
                <input v-model="formData.Kennzeichen" type="text" name="kennzeichen" id="kennzeichen"
                    placeholder="Kennzeichen" class="forms-field-input" />
            </fieldset>

            <fieldset class="forms-fieldset cars">
                <input v-model="formData.Fahrzeugklasse" type="text" name="fahrzeugklasse" id="fahrzeugklasse"
                    placeholder="Fahrzeugklasse" class="forms-field-input" />
            </fieldset>

            <fieldset class="forms-fieldset cars">
                <input v-model="formData.Automarke" type="text" name="automarke" id="automarke" placeholder="Automarke"
                    class="forms-field-input" />
            </fieldset>

            <fieldset class="forms-fieldset cars">
                <input v-model="formData.Typ" type="text" name="typ" id="typ" placeholder="Typ"
                    class="forms-field-input" />
            </fieldset>

            <fieldset class="forms-fieldset cars">
                <input v-model="formData.Farbe" type="text" name="farbe" id="farbe" placeholder="Farbe"
                    class="forms-field-input" />
            </fieldset>

            <fieldset class="forms-fieldset cars">
                <input v-model="formData.Sonstiges" type="text" name="sonstiges" id="sonstiges" placeholder="Sonstiges"
                    class="forms-field-input" />
            </fieldset>

            <SubmitButton>Speichern</SubmitButton>

        </form>

    </div>
</template>

<script>

import SubmitButton from '../Login/Slots/SubmitButton.vue';

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
        }
    },

    methods: {
        submitForm() {
            console.log("Abgeschickt!")
        }
    }
}

</script>

<style>

</style>