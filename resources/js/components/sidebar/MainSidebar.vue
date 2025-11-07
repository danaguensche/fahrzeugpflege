<template>
  <div class="main-sidebar-container" :class="{
    'sidebar-open': isSidebarOpen,
    'sidebar-closed': !isSidebarOpen
  }">
    <SidebarState></SidebarState>
    <ReduceButton v-if="isSidebarOpen" @click="toggleSidebar" class="reduce-button">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon.png" alt="Close">
    </ReduceButton>
    <ExpandButton v-else @click="toggleSidebar" class="expand-button">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon-mirrored.png" alt="Open">
    </ExpandButton>
  </div>
</template>
<script>
import { mapState, mapMutations } from "vuex"
import ReduceButton from "./Slots/ReduceButton.vue";
import ExpandButton from "./Slots/ExpandButton.vue";
import SidebarState from "./SidebarState.vue";
export default {
  name: "MainSidebar",
  components: {
    SidebarState,
    ReduceButton,
    ExpandButton,
  },
  computed: {
    ...mapState(['isSidebarOpen'])
  },
  methods: {
    ...mapMutations(['toggleSidebar'])
  },
}
</script>
<style>
.main-sidebar-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 300px;
  height: 100%;
  z-index: 1000;
  transition: transform 1s ease, width 0.6s ease;
}


.arrow {
  transform: scale(0.8);
}

.reduce-button:hover {
  background-color: darkslategray;
  transform: scale(1.05) rotate(-180deg);

  .arrow {
    filter: brightness(175%);
  }
}

.expand-button:hover {
  background-color: darkslategray;
  transform: scale(1.05) rotate(180deg);

  .arrow {
    filter: brightness(175%);
  }
}

.sidebar-open {
  width: 300px;
  transform: translateX(0);
  transition: transform 1s ease, width 0.3s ease;
}

.sidebar-closed {
  width: 110px;
  transform: translateX(-1%);
  transition: transform 1s ease, width 0.3s ease;
}


@media (max-width: 768px) {
  .main-sidebar-container {
    width: 100%;
  }

  .main-sidebar-container.sidebar-closed {
    transform: translateX(-100%);
  }
}
</style>