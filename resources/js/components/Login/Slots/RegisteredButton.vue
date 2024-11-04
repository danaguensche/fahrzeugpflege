<template>
    <button @click="switchForms" class="user-registered-login" id="login-button">{{ buttonLabel }}
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
        switchForms() {
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

<style>
@import url("../../../../css/login/user-options-buttons.css");
</style>