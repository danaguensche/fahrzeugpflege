<template>
  <div class="main-sidebar-container">
    <SidebarState></SidebarState>
    <ReduceButton v-if="isSidebarOpen" @click="toggleSidebar">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon.png">
    </ReduceButton>
    <ExpandButton v-else @click="toggleSidebar">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon-mirrored.png">
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

<style scoped>
.main-sidebar-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.sidebar-container,
.sidebar-container.closed {
  transition: width 0.6s ease;
  position: absolute;
  height: 100%;
}

/* Reduce Button */
.arrow {
  transform: scale(0.8);
}

.reduce-button:hover {
  background-color: darkslategray;
  transform: scale(1.05);
  transform: rotate(-180deg);

  .arrow {
    filter: brightness(175%);
  }
}

.expand-button:hover {
  background-color: darkslategray;
  transform: scale(1.05);
  transform: rotate(-180deg);

  .arrow {
    filter: brightness(175%);
  }
}


@media (max-width: 768px) {

  .reduce-button {
    left: 200px;
  }
}

@media (max-width: 768px) {
  .sidebar-container.closed {
    width: auto;
    transition: width 0.6s ease;
  }

}
</style>