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
.user-action {
    display: inline-block;
    margin-top: 1rem;
    margin-right: 0.27rem;
    margin-left: 0.27rem;
    /* border: 0.0625rem solid var(--primary-color); */
    border-radius: var(--border-radius);
    padding: 0.85rem 2rem;
    color: var(--text-color-light);
    text-transform: uppercase;
    transition: background-color var(--transition-speed), color var(--transition-speed), transform var(--transition-speed);
    text-align: center;
    text-decoration: none;
    background-color: var(--primary-color-hover);
    box-shadow: var(--box-shadow);
    width: 250px;
    font-size: 0.875rem;
    letter-spacing: 0.1rem;
}

.user-action:hover {
    background-color: var(--text-color-light);
    color: var(--primary-color);
    transform: scale(1.05);
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.15);
    cursor: pointer;
}
</style>