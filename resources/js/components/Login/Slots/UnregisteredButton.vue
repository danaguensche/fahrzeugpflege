<template>
    <button @click="goToSignup" class="user-unregistered-signup" id="signup-button">Registrieren
        <slot></slot>
    </button>
</template>

<script>

export default {
    name: "UnregisteredButton",

    props: {
        signUp_isActive: Boolean
    },

    data(){
        return {
            signUp_isActive: false
        }
    },

    methods: {
        goToSignup() {
            this.signUp_isActive = !this.signUp_isActive;
            console.log(this.signUp_isActive);
        }
    }
}

</script>
