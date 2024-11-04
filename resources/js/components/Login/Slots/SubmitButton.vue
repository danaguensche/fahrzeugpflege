<template>
    <button type="submit" class="forms-buttons-action">{{ buttonLabel }}<slot></slot></button>
</template>

<script>

export default {
    name: "SubmitButton",

    props: {
        buttonLabel: {
            type: String,
            default: ""
        }
    },

    methods: {
        set_buttonLabel(name) {
            this.buttonLabel = name;
        }
    }
}

</script>

<style>
@import url("../../../../css/login/submit-button.css");
</style>