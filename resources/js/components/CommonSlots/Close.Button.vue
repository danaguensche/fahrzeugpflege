<template>
    <button class="close-button">
        <img src="../../../img/icons/close.png" class="close-icon">
    </button>
    
    </template>
    
    <script>
    export default{
        name: "CloseButton"
    }
    </script>
    
    <style scoped>
    .close-button {
        align-self: flex-end;
        margin-right: 55px;
        position: absolute;
        transform: scale(0.3);
        background-color: white;
        margin-top: 25px;
    
    }
    
    .close-icon {
        filter: brightness(50%);
    }
    
    .close-icon:hover {
        cursor: pointer;
        filter: brightness(0%);
    }
    </style>