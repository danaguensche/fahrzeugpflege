<template>
    <div class="form-wrapper profil" @submit.prevent="submitForm">
        <form class="page-form">

            <div class="single-form">
                <h2 class="forms-title">Kunde hinzufügen</h2>
                <div v-for="field in formFields" :key="field.id" class="forms-field">
                    <p class="field-header">{{ field.name }}</p>
                    <input :type="field.type" :id="field.id" :placeholder="field.placeholder"
                        v-model="customer[field.id]" class="forms-field-input" />
                </div>
                <SubmitButton>speichern</SubmitButton>
            </div>
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
            customer: {
                company: "",
                firstname: "",
                lastname: "",
                email: "",
                phonenumber: "",

                addressline: "",
                postalcode: "",
                city: "",
            },
            errors: {},
            formFields: [
                { id: 'company', name: 'Firma', type: 'text', placeholder: 'Firma' },
                { id: 'firstname', name: 'Vorname *', type: 'text', placeholder: 'Vorname' },
                { id: 'lastname', name: 'Nachname *', type: 'text', placeholder: 'Nachname' },
                { id: 'email', name: 'E-Mail *', type: 'email', placeholder: 'Email' },
                { id: 'phonenumber', name: 'Telefonnummer *', type: 'text', placeholder: 'Telefonnummer' },

                { id: 'addressline', name: 'Straße und Hausnummer *', type: 'text', placeholder: 'Straße und Hausnummer' },
                { id: 'postalcode', name: 'PLZ *', type: 'text', placeholder: 'PLZ' },
                { id: 'city', name: 'Stadt *', type: 'text', placeholder: 'Stadt' },
            ],
        };
    },
    methods: {
        async submitForm() {
            try {
                const response = await axios.post('/kunden', this.customer);
                console.log('Kunde wurde erfolgreich hinzugefügt.', response.data);
            } catch (error) {
                console.error('Fehler beim Speichern des Kunden.', error);
            }
        }
    }
}
</script>

<style scoped>
.form-wrapper.profil {
    font-family: var(--font-family);
    display: flex;
    align-self: flex-start;
    justify-content: right;
    flex-direction: row;
    /* box-shadow: var(--box-shadow); */

}

.field-header{
    font-family: var(--font-family);
    margin-bottom: 1vh;
}

.single-form {
    margin-top: 5vh;
    margin-bottom: 5vh;
    padding: 100px;
    box-shadow: var(--box-shadow);
    width: 700px;
}

.forms-title {
    margin-bottom: 50px;
}

.forms-field {
    margin-bottom: 15px;
}

.alert.error {
    color: red;
    font-size: 0.9em;
}
</style>