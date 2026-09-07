<template>
    <div class="settings-page" :class="{ 'settings-page-sidebar-opened': isSidebarOpen }">
        <v-card class="settings-card">
            <v-card-title>Settings</v-card-title>
            <v-card-text>
                <p>Manage your application settings here.</p>
                <p>Configure user preferences, application behavior, and more.</p>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" @click="$router.push('/profile')">Go to Profile</v-btn>
                <v-btn color="secondary" @click="$router.push('/notifications')">Notification Settings</v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
export default {
    name: 'Settings',
    computed: {
        ...mapState(['isSidebarOpen']),
        ...mapGetters('auth', ['isAdminOrTrainer']),
    }
}
</script>

<style scoped>
.settings-page {
    margin-left: 150px;
    margin-right: 50px;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
}

.settings-page-sidebar-opened {
    margin-left: 330px;
    transition: margin-left 0.3s ease;
}

/* Tablet Styles */
@media only screen and (max-width: 1024px) {
    .settings-page {
        margin-left: 120px;
        margin-right: 30px;
    }
}

/* Mobile Styles */
@media only screen and (max-width: 768px) {
    .settings-page {
        margin-left: 160px;
        margin-right: 20px;
        font-size: 12px;
    }

    .settings-page-sidebar-opened {
        margin-left: 260px;
    }
}
</style>