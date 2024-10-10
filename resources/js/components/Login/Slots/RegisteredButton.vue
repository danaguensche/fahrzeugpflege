<template>
    <button @click="goToLogin" class="user-registered-login" id="login-button">{{ buttonLabel }}
        <slot></slot>
    </button>

</template>

<script>

export default {
    name: "RegisteredButton",

    props: {
        isActive: {
            type: Boolean,
            default: false
        },

        buttonLabel: {
            type: String,
            default: ""
        }
    },

    methods: {
        goToLogin() {
            this.login_isActive = !this.login_isActive;
            console.log(this.login_isActive);
        },

        setButtonLabel(name) {
            this.buttonLabel = name;
        },

        set_isActive() {
            this.isActive = false;
        }
    }
}

</script>
