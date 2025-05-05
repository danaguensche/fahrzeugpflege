<template>
<v-navigation-drawer
  v-model="isSidebarOpen"
  app
  class="sidebar-container"
  :class="{ closed: !isSidebarOpen }"
  :width="isSidebarOpen ? 250 : 100"
  :mini-variant="!isSidebarOpen"
  >
  
  </v-navigation-drawer>
</template>

<script>

import { mapState, mapMutations } from "vuex"

export default {
  name: "MainSidebar",
  components: {

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
.sidebar-container,
.sidebar-container.closed {
  transition: width 0.3s ease;
}


.sidebar-container.closed {
  width: 100px;
  transition: width 0.3s ease;


  height: 100vh;
  background-color: var(--clr-bg-sidebar);
  color: var(--clr-dark);
  padding: 2.5vh;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: fixed;
  border-radius: 10px;
  box-shadow: 2px 0 5px var(--clr-box-shadow);
  user-select: none;

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
    transition: width 0.3s ease;
  }

}
</style>