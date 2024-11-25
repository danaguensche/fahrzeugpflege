<template>
    <button @click="switchForms" class="user-action" id="login-button">
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

    },

    methods: {
        switchForms() {
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