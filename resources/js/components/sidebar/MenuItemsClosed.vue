<template>
    <div v-for="(menuitem, index) in menuitems" :key="menuitem.id"
        :class="['sidebar-button closed', { 'profile-spacing closed': menuitem.name === 'Profil' }]">
        <a :href="getRoute(menuitem.name)">
            <img :src="getImageSrc(icons[index].name)" class="icon closed" />
        </a>
    </div>
</template>

<script>
export default {
    name: 'MenuItemsClosed',

    data: () => ({
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
        icons: [
            { id: 1, name: 'dashboard' },
            { id: 2, name: 'calendar' },
            { id: 3, name: 'cars' },
            { id: 4, name: 'jobs' },
            { id: 5, name: 'reports' },
            { id: 6, name: 'chat' },
            { id: 7, name: 'profile' },
            { id: 8, name: 'settings' },
            { id: 9, name: 'logout' },
        ]
    }),

    methods: {
        getImageSrc(iconName) {
            return import(`../../../img/sidebar-img/${iconName}-icon.png`);
        },

        getRoute(pageName) {
            let pageName_lowerCase = pageName.toLowerCase();
            let pageName_formatted = this.removeUmlauts(pageName_lowerCase);
            return `http://localhost:8000/${pageName_formatted}`;
        },

        removeUmlauts(str) {
            const umlautMap = {
                'ä': 'ae',
                'ö': 'oe',
                'ü': 'ue',
                'ß': 'ss',
                'Ä': 'Ae',
                'Ö': 'Oe',
                'Ü': 'Ue'
            };

            return str.replace(/[äöüßÄÖÜ]/g, function (match) {
                return umlautMap[match];
            });
        }
    }
}
</script>

<style scoped>
@import url(../../../css/sidebar/main-sidebar-closed.css);
</style>
