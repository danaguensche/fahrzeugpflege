<template>
    <div class="form-wrapper">
        <form class="page-form">
            <fieldset>
                <input v-model="formData.Kennzeichen" type="text" name="kennzeichen" id="kennzeichen"
                    placeholder="Kennzeichen"  required autofocus />
                 </fieldset>

                 <fieldset>
                <input v-model="formData.Fahrzeugklasse" type="text" name="fahrzeugklasse" id="fahrzeugklasse"
                    placeholder="Fahrzeugklasse"  required autofocus />
                 </fieldset>

                 <fieldset>
                <input v-model="formData.Automarke" type="text" name="automarke" id="automarke"
                    placeholder="Automarke"  required autofocus />
                 </fieldset>

                 <fieldset>
                <input v-model="formData.Typ" type="text" name="typ" id="typ"
                    placeholder="Typ"  required autofocus />
                 </fieldset>

                 <fieldset>
                <input v-model="formData.Farbe" type="text" name="farbe" id="farbe"
                    placeholder="Farbe"  required autofocus />
                 </fieldset>

                 <fieldset>
                <input v-model="formData.Sonstiges" type="text" name="sonstiges" id="sonstiges"
                    placeholder="Sonstiges"  required autofocus />
                 </fieldset>

                 <SubmitButton :buttonLabel="buttonLabel"></SubmitButton>

        </form>

    </div>
</template>

<script>

import SubmitButton from '../Login/Slots/SubmitButton.vue';

export default {
    name: "Form",

    components: {
        SubmitButton
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

            buttonLabel: "Absenden"
        }
    }
}

</script>

<style>
@import url("../../../css/cars/cars.css");
</style>