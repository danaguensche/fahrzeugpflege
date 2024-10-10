<template>
    <section class="registration rubik-font">
        <UserRegistered></UserRegistered>
        <div class="user-options-forms">
            <div class="user-forms-signup">
                <h2 class="forms-title">Registrieren</h2>
                <form method="POST" class="forms-form">
                    <SignUpForm></SignUpForm>

                </form>
            </div>
        </div>


    </section>
</template>

<script>

import SignUpForm from './SignUpForm.vue';
import UserRegistered from './UserRegistered.vue';

export default {
    name: "SignUp",
    components: {
        SignUpForm,
        UserRegistered
    },
    methods: {
        redirectToLogin() {
            window.location.href = "/login"
        }
    }
}

</script>
