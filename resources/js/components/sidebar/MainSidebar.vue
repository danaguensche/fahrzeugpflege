<template>
  <div id="sidebar">
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

<style>
@import url(../../../css/sidebar/reduce-button.css);
@import url(../../../css/sidebar/expand-button.css);
</style>