<template>
    <div v-for="(menuitem, index) in menuitems" :key="menuitem.id"
        :class="['sidebar-button', { 'profile-spacing': menuitem.name === 'Profil' }]">
        <img :src="getImageSrc(icons[index].name)" class="icon" />
        <a href="#" class="sidebar_textContent">{{ menuitem.name }}</a>
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
            { id: 5, name: 'berichte' },
            { id: 6, name: 'chat' },
            { id: 7, name: 'profile' },
            { id: 8, name: 'settings' },
            { id: 9, name: 'logout' },
        ]
    }),

    methods: {
        getImageSrc(iconName) {
            return require(`../../../img/sidebar-img/${iconName}-icon.png`);
        }
    }
}
</script>

<style scoped>
@import url(../../../css/sidebar/main-sidebar-opened.css)
</style>