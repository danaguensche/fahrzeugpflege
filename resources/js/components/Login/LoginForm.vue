<template>
    <form method="POST" class="forms-form">
        <fieldset class="forms-fieldset">
            <div class="forms-field">
                <input type="email" id="email" placeholder="Email" class="forms-field-input" required autofocus />
            </div>
            <div class="forms-field">
                <input type="password" id="password" placeholder="Passwort" class="forms-field-input" required />
            </div>
        </fieldset>
        <div class="forms-buttons">
            <a href="#" class="forms-buttons-forgot">Passwort vergessen?</a>
            <SubmitButton :buttonLabel="'Anmelden'"></SubmitButton>


        </div>
    </form>

</template>

<script>
import SubmitButton from './Slots/SubmitButton.vue';


export default {
    name: "LoginForm",
    components: {
        SubmitButton,
    },
    data() {
        return {
            buttonLabel: "Anmelden"
        }
    },

    methods: {
        update_buttonLabel(newName){
            this.buttonLabel = newName
        }
    }
}

</script>
