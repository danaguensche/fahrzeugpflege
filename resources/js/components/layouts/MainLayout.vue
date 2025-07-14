<template>
  <div class="main-layout">
    <MainSidebar />
    <div class="content-area" :style="{ paddingLeft: contentPaddingLeft }">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import MainSidebar from '../sidebar/MainSidebar.vue';
import { mapState } from 'vuex';

export default {
  name: 'MainLayout',
  components: {
    MainSidebar,
  },
  computed: {
    ...mapState(['isSidebarOpen']),
    contentPaddingLeft() {
      return this.isSidebarOpen ? '275px' : '120px';
    },
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
  overflow-y: auto;
  padding: 20px;
  transition: padding-left 0.6s ease; /* Add transition for smooth animation */
}
</style>
