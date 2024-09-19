<template>
    <div class="sidebar-textContent rubik-font">
        <div v-for="(menuitem, index) in menuitems" :key="menuitem.id"
            :class="['sidebar-button', { 'profile-spacing': menuitem.name === 'Profil' }]">
            <img :src="getImageSrc(icons[index].name)" class="icon" />
            <a :href="getRoute(menuitem.name)">{{ menuitem.name }}</a>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MenuItemsOpened',

    data: () => ({
        menuitems: [
            { id: 1, name: 'Dashboard' },
            { id: 2, name: 'Kalender' },
            { id: 3, name: 'Fahrzeuge' },
            { id: 4, name: 'Aufträge' },
            { id: 5, name: 'Berichte' },
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
    }
}
</script>

<style scoped>
@import url(../../../css/sidebar/main-sidebar-opened.css);
</style>
