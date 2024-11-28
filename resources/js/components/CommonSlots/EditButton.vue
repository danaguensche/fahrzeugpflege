<template>
    <button class="edit-button">
        <img src="../../../img/icons/edit.png" class="edit-icon">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: "EditButton",

}
</script>

<style scoped>
.edit-button {
    align-self: flex-end;
    position: absolute;
    transform: scale(0.3);
    background-color: white;
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