<template>
    <button class="add-button">
        <img src="../../../img/icons/add.png" class="add-icon">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: "AddButton",

}
</script>

<style scoped>
.add-button {
    align-self: flex-end;
    position: absolute;
    transform: scale(0.3);
    background-color: white;
    margin-top: 25px;

}

.add-icon {
    filter: brightness(50%);
}

.add-icon:hover {
    cursor: pointer;
    filter: brightness(0%);
}
</style>