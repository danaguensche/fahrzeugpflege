<template>
  <div id="app" class="app">
      <router-view></router-view>
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

.app {
  background-image: url('../../../public/images/background.png');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  height: 100vh;
}
</style>
