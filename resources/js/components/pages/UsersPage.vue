<template>
  <v-container>
    <v-card>
      <v-card-title>Users</v-card-title>
      <v-card-text>
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
      </v-card-text>
    </v-card>
  </v-container>
</template>


<script>
import DataTable from '../Table/DataTable.vue';

export default {
  name: 'UsersPage',
  components: {
    DataTable,
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
/* Add any specific styles for UsersPage here */
</style>
