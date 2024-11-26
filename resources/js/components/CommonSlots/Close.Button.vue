<template>
    <transition name="fade">
      <button v-if="isVisible" class="close-button" @click="$emit('close')">
        <img src="../../../img/icons/close.png" class="close-icon">
      </button>
    </transition>
  </template>
  
  <script>
  export default {
    name: "CloseButton",
    props: {
      isVisible: {
        type: Boolean,
        default: false
      }
    }
  }
  </script>
  
  <style scoped>
  .close-button {
    align-self: flex-end;
    margin-right: 45px;
    position: absolute;
    transform: scale(0.3);
    background-color: white;
    margin-top: 25px;
    transition: 0.6s ease;
  }
  
  .close-icon {
    filter: brightness(50%);
  }
  
  .close-icon:hover {
    cursor: pointer;
    filter: brightness(0%);
  }
  
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  
  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }
  </style>
  