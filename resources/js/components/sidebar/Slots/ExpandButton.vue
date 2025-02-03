<template>
    <button class="expand-button">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: 'ExpandButton'
}
</script>

<style scoped>
.expand-button {
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;

    left: 80px;
    margin-top: 44px;

    width: 50px;
    height: 50px;

    z-index: 0;
    background-color: whitesmoke;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

::selection {
    background-color: transparent;
}

.expand-button:hover {
    background-color: darkslategray;
    transform: scale(1.05) rotate(180deg);
}



@media (max-width: 768px) {
    .expand-button {
        left: 85px;
    }
}
</style>