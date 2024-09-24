<template>
    <div class="sidebar-container-closed">
        <div class="sidebar-buttons-wrapper closed">
            <div v-for="(menuitem, index) in menuitems" :key="menuitem.id"
                :class="['sidebar-button closed', { 'profile-spacing closed': menuitem.name === 'Profil' }]">
                <a :href="getRoute(menuitem.name)">
                    <div class="icon-wrapper">
                        <img :src="iconPaths[index].name" class="icon closed" alt="Icon for {{ menuitem.name }}">
                    </div>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MenuItemsClosed',

    data: () => ({
        //Text-Inhalte der Sidebar
        menuitems: [
            { id: 1, name: 'Dashboard' },
            { id: 2, name: 'Kalender' },
            { id: 3, name: 'Fahrzeuge' },
            { id: 4, name: 'Aufträge' },
            { id: 5, name: 'Bericht' },
            { id: 6, name: 'Chat' },
            { id: 7, name: 'Profil' },
            { id: 8, name: 'Einstellungen' },
            { id: 9, name: 'Abmelden' },
        ],
        // Bildnamen
        iconPaths: [
            { id: 1, name: new URL('@/img/sidebar-img/dashboard-icon.png', import.meta.url).href },
            { id: 2, name: new URL('@/img/sidebar-img/calendar-icon.png', import.meta.url).href },
            { id: 3, name: new URL('@/img/sidebar-img/cars-icon.png', import.meta.url).href },
            { id: 4, name: new URL('@/img/sidebar-img/jobs-icon.png', import.meta.url).href },
            { id: 5, name: new URL('@/img/sidebar-img/reports-icon.png', import.meta.url).href },
            { id: 6, name: new URL('@/img/sidebar-img/chat-icon.png', import.meta.url).href },
            { id: 7, name: new URL('@/img/sidebar-img/profile-icon.png', import.meta.url).href },
            { id: 8, name: new URL('@/img/sidebar-img/settings-icon.png', import.meta.url).href },
            { id: 9, name: new URL('@/img/sidebar-img/logout-icon.png', import.meta.url).href },
        ]
    }),

    methods: {
        //Formatiert Einträge aus dem menuitems Array und gibt die Route zu der dazugehörigen Seite zurück
        getRoute(pageName) {
            let pageName_lowerCase = pageName.toLowerCase();
            let pageName_formatted = this.removeUmlauts(pageName_lowerCase);
            return `http://localhost:8000/${pageName_formatted}`;
        },

        //Entfernt Umlaute aus einem String
        removeUmlauts(str) {
            const umlaute = {
                'ä': 'ae',
                'ö': 'oe',
                'ü': 'ue',
                'ß': 'ss',
                'Ä': 'Ae',
                'Ö': 'Oe',
                'Ü': 'Ue'
            };

            return str.replace(/[äöüßÄÖÜ]/g, function (match) {
                return umlaute[match];
            });
        }
    },
}
</script>

<style scoped>
@import url(../../../css/sidebar/main-sidebar-closed.css);
</style>
