<template>
    <button class="reduce-button">
        <slot></slot>
    </button>
</template>

<script>

export default {
    name: 'ReduceButton'
}

</script>

<style scoped>
.reduce-button {
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    
    left: 240px;
    margin-top: 44px;

    
    width: 50px;
    height: 50px;

    background-color: whitesmoke;
    border: 0px;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

::selection {
    background-color: transparent;
}

</style>