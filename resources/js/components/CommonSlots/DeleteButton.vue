<template>
    <button class="delete-button">
        <img src="../../../img/icons/delete.png" class="delete-icon" :class="{ 'tooltip': showTooltip }"
            :title="tooltipText">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: "DeleteButton",

    props: {
        tooltipText: {
            type: String,
            default: null
        }
    },

    computed: {
        showTooltip() {
            if (this.tooltipText !== null && this.tooltipText !== '') {
                return true;
            }
        }
    }
}
</script>

<style scoped>
.delete-button {
    align-self: flex-end;
    position: absolute;
    transform: scale(0.3);
    background-color: transparent;
    margin-top: 25px;

}

.delete-icon {
    filter: brightness(50%);
}

.delete-icon:hover {
    cursor: pointer;
    filter: brightness(0%);
}
</style>

<style>
@import url("../../../css/tooltips.css");
</style>