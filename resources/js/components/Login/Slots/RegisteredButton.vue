<template>
    <button @click="goToLogin" class="user-registered-login" id="login-button">Anmelden
        <slot></slot>
    </button>

</template>

<script>

export default {
    name: "RegisteredButton",
    
    props: {
        login_isActive: Boolean
    },

    data(){
        return {
            login_isActive: false
        }
    },

    methods: {
        goToLogin() {
            this.login_isActive = !this.login_isActive;
            console.log(this.login_isActive);
        }
    }
}

</script>


