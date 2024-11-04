<template>
    <section class="login-page">
        <div :class="['user-options-forms', 'user-options-forms-unregistered', { 'active': isActive }]">
            <div class="user-forms-signup">
                <h2 class="forms-title">Registrieren</h2>
                <SignUpForm></SignUpForm>
            </div>
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
