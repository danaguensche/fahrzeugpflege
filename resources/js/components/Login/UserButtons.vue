<template>
    <div class="user-options-container user-options-container-registered">
        <div class="user-options-text">
            <div class="user-options">
                <RegisteredButton :class="['user-action', { 'active': activeForm === 'login' }]" @click="switchToLogin">
                    Anmelden
                </RegisteredButton>
                <RegisteredButton :class="['user-action', { 'active': activeForm === 'signup' }]"
                    @click="switchToSignup">
                    Registrieren
                </RegisteredButton>
            </div>
        </div>
    </div>
</template>

<script>
import RegisteredButton from './Slots/RegisteredButton.vue';
import { mapState, mapActions } from 'vuex';

export default {
    name: "UserRegistered",
    components: {
        RegisteredButton
    },
    computed: {
        ...mapState(['activeForm'])
    },
    methods: {
        ...mapActions(['switchForm']),
        switchToLogin() {
            this.switchForm('login');
        },
        switchToSignup() {
            this.switchForm('signup');
        }
    }
}
</script>

<style>
@import url("../../../css/login/login.css");
</style>
