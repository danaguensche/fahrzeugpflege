<template>
    <button class="search-button" :class="{ 'hover-tooltip': showTooltip }" :title="tooltipText">
        <img src="../../../img/icons/search.png" class="search-icon">
        <slot></slot>
    </button>

</template>

<script>
export default {
    name: "SearchButton",

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
.search-button {
    align-self: flex-end;
    position: absolute;
    transform: scale(0.3);
    background-color: transparent;
    margin-top: 25px;

}

.search-icon {
    filter: brightness(50%);
}

.search-icon:hover {
    cursor: pointer;
    filter: brightness(0%);
}
</style>

<style>
@import url("../../../css/tooltips.css");
</style>