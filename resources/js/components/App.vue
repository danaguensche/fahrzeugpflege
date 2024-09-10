<template>
  <div id="app">
    <div id="sidebar">
      <h1>component</h1>
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
@import url("../../resources/css/main.css");
</style>
