<template>
  <div id="sidebar">
    <SidebarOpened v-if="isSidebarOpen" />
    <SidebarClosed v-if="!isSidebarOpen" />
    <SidebarButtonOpened v-if="isSidebarOpen" @click="toggleSidebar" />
    <SidebarButtonClosed v-else @click="toggleSidebar" />
  </div>
</template>

<script>
import SidebarButtonClosed from "./SidebarButtonClosed.vue"
import SidebarButtonOpened from "./SidebarButtonOpened.vue"
import SidebarOpened from "./SidebarOpened.vue"
import SidebarClosed from "./SidebarClosed.vue"

export default {
  name: "MainSidebar",
  components: {
    SidebarOpened,
    SidebarButtonOpened,
    SidebarClosed,
    SidebarButtonClosed,
  },

  data: () => ({
    isSidebarOpen: true,
  }),

  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen
    },
  },
}
</script>
