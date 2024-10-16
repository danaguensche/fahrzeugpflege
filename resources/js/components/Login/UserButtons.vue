<template>
    <div class="user-options-container user-options-container-registered">
        <div class="user-options-text">
            <div class="user-options">
                <RegisteredButton :buttonLabel="'Anmelden'" :class="buttonClass" @click="switchToLogin">
                </RegisteredButton>
                <RegisteredButton :buttonLabel="'Registrieren'" :class="buttonClass" @click="switchToSignup">
                </RegisteredButton>
            </div>
        </div>
    </div>
</template>

<script>
import RegisteredButton from './Slots/RegisteredButton.vue';


export default {
    name: "UserRegistered",
    components: {
        RegisteredButton
    },
    data() {
        return {
            buttonClass: "user-action"
        };
    },
    methods: {
        switchToLogin() {
            console.log("Wechsel zum Login");
        },
        switchToSignup() {
            console.log("Wechsel zur Registrierung");
        }
    }
}
</script>
