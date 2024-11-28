<template>
    <button class="edit-button">
        <img src="../../../img/icons/edit.png" class="edit-icon" :class="{ 'hover-tooltip': showTooltip }"
            :title="tooltipText">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: "EditButton",

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
.edit-button {
    align-self: flex-end;
    position: absolute;
    transform: scale(0.3);
    background-color: transparent;
    margin-top: 25px;

}

.edit-icon {
    filter: brightness(50%);
}

.edit-icon:hover {
    cursor: pointer;
    filter: brightness(0%);
}
</style>

<style>
@import url("../../../css/tooltips.css");
</style>