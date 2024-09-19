<template>
  <div id="app">
    <div id="sidebar">
      <router-view></router-view>
      <MainSidebar />
    </div>
  </div>
</template>

<script>
import MainSidebar from "../components/sidebar/MainSidebar.vue";

export default {
  name: "App",
  components: {
    MainSidebar
  }
};

</script>

<style>
@import url("../../css/main.css");
</style>
