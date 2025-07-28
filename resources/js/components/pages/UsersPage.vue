<template>
  <div class="users-page" :class="{ 'users-page-sidebar-opened': isSidebarOpen }">
    <DataTable
      endpoint="users"
      :headers="userHeaders"
      :fields="userFields"
      itemKey="id"
      detailsPage="user-details"
      detailsUrlBasePath="benutzer"
      deleteKey="ids"
    >
      <template v-slot:item.role="{ item }">
        <v-chip :color="getRoleColor(item.role)" dark>{{ item.role }}</v-chip>
      </template>
    </DataTable>
  </div>
</template>

<script>
import DataTable from '../Table/DataTable.vue';
import { mapState } from 'vuex';

export default {
  name: 'UsersPage',
  components: {
    DataTable,
  },
  computed: {
    ...mapState(['isSidebarOpen']),
  },
  data() {
    return {
      userHeaders: [
        { title: 'Auswählen', key: 'select', sortable: false, width: '60px' },
        { title: 'ID', key: 'id' },
        { title: 'First Name', key: 'firstname' },
        { title: 'Last Name', key: 'lastname' },
        { title: 'Email', key: 'email' },
        { title: 'Phone Number', key: 'phonenumber' },
        { title: 'Address', key: 'addressline' },
        { title: 'Postal Code', key: 'postalcode' },
        { title: 'City', key: 'city' },
        {
          title: 'Role',
          key: 'role',
          type: 'select',
          options: [
            { title: 'Admin', value: 'admin' },
            { title: 'Trainer', value: 'trainer' },
            { title: 'Trainee', value: 'trainee' },
          ],
        },
        { title: 'Löschen', key: 'delete', sortable: false, width: '60px' },
        { title: 'Bearbeiten', key: 'edit', sortable: false, width: '60px' }
      ],
      userFields: [
        'id',
        'firstname',
        'lastname',
        'email',
        'phonenumber',
        'addressline',
        'postalcode',
        'city',
        'role',
      ],
    };
  },
  methods: {
    getRoleColor(role) {
      switch (role) {
        case 'admin':
          return 'red';
        case 'trainer':
          return 'blue';
        case 'trainee':
          return 'green';
        default:
          return 'grey';
      }
    },
  },
};
</script>

<style scoped>
.users-page {
    margin-left: 150px;
    margin-right: 50px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.users-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}
</style>