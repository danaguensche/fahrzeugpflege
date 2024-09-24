<template>
  <div id="app">
    <div id="sidebar">
      <router-view></router-view>
      <p>hello world</p>
    </div>
  </div>
</template>

<script>

export default {
  name: "App",
  components: {
  }
};

</script>

<style>
@import url("../../css/main.css");
</style>
