<template>
  <MainSidebar></MainSidebar>
  <Users></Users>
</template>

<script>

import MainSidebar from '../sidebar/MainSidebar.vue';
import Users from '../Users/Users.vue';

export default {
  name: "UsersPage",
  components: {
    Users,
    MainSidebar,
  }
}

</script>
