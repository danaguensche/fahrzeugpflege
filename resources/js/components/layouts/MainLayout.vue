<template>
  <div class="main-layout">
    <MainSidebar />
    <div class="content-area">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import MainSidebar from '../sidebar/MainSidebar.vue';

export default {
  name: 'MainLayout',
  components: {
    MainSidebar,
  },
};
</script>

<style scoped>
.main-layout {
  display: flex;
  height: 100vh;
  background-image: url('../../../public/images/background.png');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}

.content-area {
  flex-grow: 1;
  padding-left: 30%; 
  overflow-y: auto;
  padding: 20px;
  transition: padding-left 0.6s ease;
}
</style>
