<template>
  <div id="sidebar">
    <SidebarOpened v-if="isSidebarOpen" />
    <SidebarClosed v-if="!isSidebarOpen" />

    <ReduceButton v-if="isSidebarOpen" @click="toggleSidebar">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon.png">
    </ReduceButton>
    <ExpandButton v-else @click="toggleSidebar">
      <img class="arrow" src="../../../img/sidebar-img/arrow-icon-mirrored.png">
    </ExpandButton>
    
  </div>
</template>

<script>

import SidebarOpened from "./SidebarOpened.vue"
import SidebarClosed from "./SidebarClosed.vue"
import { mapState, mapMutations } from "vuex"
import ReduceButton from "./Slots/ReduceButton.vue";
import ExpandButton from "./Slots/ExpandButton.vue";

export default {
  name: "MainSidebar",
  components: {
    SidebarOpened,
    SidebarClosed,

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
@import url(../../../css/sidebar/reduce-button.css);
@import url(../../../css/sidebar/expand-button.css);
</style>