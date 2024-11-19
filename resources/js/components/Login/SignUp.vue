<template>
    <section class="login-page">
        <div :class="['user-options-forms-registered', 'user-options-forms-unregistered', { 'active': isActive }]">
                <h2 class="forms-title">Registrieren</h2>
                <SignUpForm></SignUpForm>
            </div>
    </section>
</template>

<script>
import SignUpForm from './SignUpForm.vue';
import { mapState } from 'vuex';

export default {
    name: "SignUp",
    components: {
        SignUpForm
    },
    computed: {
        ...mapState(['activeForm']),
        isActive() {
            return this.activeForm === 'signup';
        }
    }
}
</script>

<style>
@import url("../../../css/login/login.css");
</style>
