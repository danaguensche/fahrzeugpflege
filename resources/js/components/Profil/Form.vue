<template>
    <div class="form-wrapper profil" @submit.prevent="submitForm">
        <form class="page-form">
            
            <div class="single-form">
                <h2 class="forms-title">Profil</h2>
                <div v-for="field in formFields_profil" :key="field.id" class="forms-field">
                    <input :type="field.type" :id="field.id" :placeholder="field.placeholder"
                        v-model="formData[field.id]" class="forms-field-input" />
                </div>
                <SubmitButton>speichern</SubmitButton>
            </div>

            <div class="single-form">
                <h2 class="forms-title">Adresse</h2>
                <div v-for="field in formFields_address" :key="field.id" class="forms-field">
                    <input :type="field.type" :id="field.id" :placeholder="field.placeholder"
                        v-model="formData[field.id]" class="forms-field-input" />
                </div>
                <SubmitButton>speichern</SubmitButton>
            </div>

            <div class="single-form">
                <h2 class="forms-title">Passwort ändern</h2>
                <div v-for="field in formFields_password" :key="field.id" class="forms-field">
                    <input :type="field.type" :id="field.id" :placeholder="field.placeholder"
                        v-model="formData[field.id]" class="forms-field-input" />
                    <span class="alert error">
                       {{errors[field.id]}}
                    </span>
                </div>
                <SubmitButton>Passwort ändern</SubmitButton>
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
            formData: {
                firstname: "",
                lastname: "",
                email: "",
                phonenumber: "",

                addressline: "",
                postalcode: "",
                city: "",

                oldpassword: "",
                newpassword: "",
                confirmpassword: ""
            },
            errors: {},
            formFields_profil: [
                { id: 'firstname', name: 'firstname', type: 'text', placeholder: 'Vorname' },
                { id: 'lastname', name: 'lastname', type: 'text', placeholder: 'Nachname' },
                { id: 'email', name: 'email', type: 'email', placeholder: 'Email' },
                { id: 'phonenumber', name: 'phonenumber', type: 'text', placeholder: 'Telefonnummer' },
            ],
            formFields_address: [
                { id: 'addressline', name: 'addressline', type: 'text', placeholder: 'Straße und Hausnummer' },
                { id: 'postalcode', name: 'postalcode', type: 'text', placeholder: 'PLZ' },
                { id: 'city', name: 'city', type: 'text', placeholder: 'Stadt' },
            ],
            formFields_password: [
                { id: 'oldpassword', name: 'oldpassword', type: 'password', placeholder: 'Geben Sie ihr Passwort ein' },
                { id: 'newpassword', name: 'newpassword', type: 'password', placeholder: 'Geben Sie ihr neues Passwort ein' },
                { id: 'confirmpassword', name: 'confirmpassword', type: 'password', placeholder: 'Bestätigen Sie ihr neues Passwort' }
            ]
        };
    },
    methods: {
        validatePassword() {
            if (this.formData.newpassword.length < 8) {
                this.errors.newpassword = "Das Passwort muss mindestens 8 Zeichen lang sein.";
                return false;
            } else {
                return true;
            }
        },
        validatePassword_confirmed() {
            if (this.formData.newpassword !== this.formData.confirmpassword) {
                this.errors.confirmpassword = "Die Passwörter stimmen nicht überein.";
                return false;
            } else {
                return true;
            }
        },
        validateOldPassword() {
            //Altes Passwort muss richtig sein
            return true;
        },
        validatePasswordChange() {
            const validators = {
                oldpassword: this.validateOldPassword,
                newpassword: this.validatePassword,
                confirmpassword: this.validatePassword_confirmed,
            };

            return Object.keys(validators).every(field => validators[field]());
        },

        submitForm() {
            this.errors = {};

            if(this.validatePasswordChange()){
                console.log("Passt!");
            }

            else {
                console.log("Passt nicht");
            }
        }
    }
}
</script>

<style scoped>
@import url('../../../css/profil/profil.css');
</style>