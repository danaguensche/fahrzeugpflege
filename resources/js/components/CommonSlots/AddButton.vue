<template>
    <!-- Tool-Tipps werden nur dann gezeigt, wenn tooltipText nicht null oder ein leerer String ist, damit das Anzeigen eines Tooltipps dynamisch bleibt -->
    <!-- :class="{ 'add button hover-tooltip': showTooltip }"  <- Custom Tool-Tipps -->
    <button class="add-button" :class="{ 'add-button hover-tooltip': showTooltip }" :title="tooltipText">
        <img src="../../../img/icons/add.png" class="add-icon">
        <div class="text-content">
            <slot></slot>
        </div>

    </button>
</template>

<script>
export default {
    name: "AddButton",

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
.add-button {
    position: absolute;
    background-color: transparent;
    margin-top: 25px;
    font-family: var(--font-family);

}

.text-content {
    margin-left: -30px;
}

.add-icon {
    filter: brightness(50%);
    transform: scale(0.4);
}

.add-icon:hover {
    cursor: pointer;
    filter: brightness(0%);
}


</style>

<style>
@import url("../../../css/tooltips.css");
</style>