<template>
    <div v-if="isOpen" class="form-wrapper profil">
        <form class="page-form" @submit.prevent="submitForm">
            <div class="single-form">
                <!-- Close Button  -->
                <div class="form-header">
                    <CloseButton :isVisible="true" @click="closeForm"></CloseButton>
                </div>

                <!-- Form Fields -->
                <h2 class="forms-title">Kunde hinzufügen</h2>
                <div v-for="field in formFields" :key="field.id" class="forms-field">
                    <!-- <p class="field-header">{{ field.name }}</p> -->
                    <input :type="field.type" :id="field.id" :placeholder="field.placeholder"
                        v-model="formData[field.id]" class="forms-field-input" />
                    <span class="alert error">
                        {{ errors[field.id] }}
                    </span>
                </div>

                <!-- Success Alert -->
                <div v-if="success" class="alert success">
                    {{ success }}
                </div>

                <!-- Submit Button -->
                <SubmitButton @click="submitForm">speichern</SubmitButton>
            </div>
        </form>
    </div>
</template>


<script>
import { ref } from 'vue';
import { useValidation } from '../../../composables/useValidation.js';
import SubmitButton from '../../Login/Slots/SubmitButton.vue';
import axios from 'axios';
import CloseButton from '../../CommonSlots/CloseButton.vue';

export default {
    name: "Form",
    components: {
        SubmitButton,
        CloseButton
    },
    data() {
        const initialData = {
            company: "",
            firstname: "",
            lastname: "",
            email: "",
            phonenumber: "",
            addressline: "",
            postalcode: "",
            city: "",
        };

        const { formData, errors, validateForm } = useValidation(initialData);
        const success = ref('');

        const formFields = [
            { id: 'company', name: 'Firma', type: 'text', placeholder: 'Firma' },
            { id: 'firstname', name: 'Vorname *', type: 'text', placeholder: 'Vorname *' },
            { id: 'lastname', name: 'Nachname *', type: 'text', placeholder: 'Nachname *' },
            { id: 'email', name: 'E-Mail *', type: 'email', placeholder: 'Email *' },
            { id: 'phonenumber', name: 'Telefonnummer *', type: 'text', placeholder: 'Telefonnummer *' },
            { id: 'addressline', name: 'Straße und Hausnummer *', type: 'text', placeholder: 'Straße und Hausnummer *' },
            { id: 'postalcode', name: 'PLZ *', type: 'text', placeholder: 'PLZ *' },
            { id: 'city', name: 'Stadt *', type: 'text', placeholder: 'Stadt *' },
        ];

        const submitForm = async () => {
            if (validateForm()) {
                try {
                    const response = await axios.post('/api/customers', formData);
                    console.log('Kunde wurde erfolgreich hinzugefügt.', response.data);
                    success.value = "Kunde wurde erfolgreich hinzugefügt.";
                    //Formular zurücksetzen
                    Object.keys(formData).forEach(key => formData[key] = '');
                } catch (error) {
                    console.error('Fehler beim Speichern des Kunden.', error);
                    errors.value.general = "Fehler beim Speichern des Kunden.";
                }
            }
        }; 



        return {
            formData,
            errors,
            formFields,
            submitForm,
            success
        };
    },
    props: {
        isOpen: {
            type: Boolean,
            required: true,
        }
    },

    methods: {
        closeForm() {
            this.$emit('close');
        },
    }
}
</script>

<style scoped>
.form-wrapper.profil {
    font-family: var(--font-family);
    display: flex;
    align-self: flex-start;
    justify-content: center;
    flex-direction: row;

}


.single-form {
    position: relative;
    margin-top: 5vh;
    margin-bottom: 5vh;
    padding: 90px;
    box-shadow: var(--box-shadow);
    z-index: 10;
    background-color: var(--background-color);
    width: 700px
}


.form-header {
    position: absolute;
    top: -45px;
    right: 80px;
}


.forms-title {
    margin-bottom: 50px;
}


.forms-field {
    margin-bottom: 15px;
}

.field-header {
    font-family: var(--font-family);
    margin-bottom: 1vh;
}

.forms-field-input {
    width: 100%;
    padding: 10px;
}
</style>
