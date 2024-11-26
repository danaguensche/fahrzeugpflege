<template>
<button class="search-button">
    <img src="../../../img/icons/search.png" class="search-icon">
</button>

</template>

<script>
export default{
    name: "SearchButton"
}
</script>

<style scoped>
.search-button {
    align-self: flex-end;
    position: absolute;
    transform: scale(0.4);
    background-color: white;
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