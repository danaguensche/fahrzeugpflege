<template>
  <transition name="fade">
    <button v-if="isVisible" class="close-button" :class="{ 'hover-tooltip': showTooltip }" :title="tooltipText"
      @click="$emit('close')">
      <img src="../../../img/icons/close.png" class="close-icon">
      <slot></slot>
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
    },

    tooltipText: {
      type: String,
      default: null
    }
  },
  methods: {
    computed: {
      showTooltip() {
        if (this.tooltipText !== null && this.tooltipText !== '') {
          return true;
        }
      }
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
  background-color: transparent;
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

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

<style>
@import url("../../../css/tooltips.css");
</style>