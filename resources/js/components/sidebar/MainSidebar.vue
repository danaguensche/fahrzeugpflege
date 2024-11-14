<template>
  <div id="sidebar">
    <SidebarOpened v-if="isSidebarOpen" />
    <SidebarClosed v-if="!isSidebarOpen" />
    <SidebarButtonOpened v-if="isSidebarOpen" @click="toggleSidebar">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon.png">
      
    </SidebarButtonOpened>
    <SidebarButtonClosed v-else @click="toggleSidebar">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon-mirrored.png">
    </SidebarButtonClosed>
  </div>
</template>

<script>
import SidebarButtonClosed from "./Slots/SidebarButtonClosed.vue"
import SidebarButtonOpened from "./Slots/SidebarButtonOpened.vue"
import SidebarOpened from "./SidebarOpened.vue"
import SidebarClosed from "./SidebarClosed.vue"
import { mapState, mapMutations } from "vuex"

export default {
  name: "MainSidebar",
  components: {
    SidebarOpened,
    SidebarButtonOpened,
    SidebarClosed,
    SidebarButtonClosed,
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
@import url("../../../css/sidebar/sidebar-button-closed.css");
@import url("../../../css/sidebar/sidebar-button-opened.css");
</style>