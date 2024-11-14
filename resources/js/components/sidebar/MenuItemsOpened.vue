<template>
    <div class="sidebar-container">
        <div v-for="(menuitem, index) in menuitems" :key="menuitem.id"
            :class="['sidebar-button', { 'profile-spacing': menuitem.name === 'Profil' }]">
            <div class="sidebar-buttons-wrapper" @click="redirectToView(menuitem)">
                <MenuButton @click="redirectToView(menuitem)" class="menu-button-content">
                    <img v-if="iconPaths[index]" :src="iconPaths[index].name" class="icon"
                        alt="Icon for {{ menuitem.name }}">
                    <span class="sidebar-textContent">{{ menuitem.name }}</span>
                </MenuButton>
            </div>
        </div>
    </div>

</template>

<script>
import { error } from 'laravel-mix/src/Log';
import axios from 'axios';

export default {
    name: 'MenuItemsOpened',

    data: () => ({

        //Text-Inhalte der Sidebar
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

        //Bildnamen
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
            let pageName_lowerCase = pageName.toLowerCase()
            let pageName_formatted = this.removeUmlaute(pageName_lowerCase)
            return `/${pageName_formatted}`
        },


        //Entfernt Umlaute aus einem String
        removeUmlaute(str) {
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
                return umlaute[match]
            })
        },

        redirectToView(menuitem) {
            if (menuitem.name === 'Abmelden') {
                this.logout();
            } else {
                this.$router.push(this.getRoute(menuitem.name));
            }
        },

        logout() {
            if (confirm("Wollen Sie sich abmelden?") == true) {
                axios.post('/logout', {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => {
                    localStorage.removeItem('auth_token');
                    this.$router.push('/login');
                }).catch(error => {
                    console.error('Logout fehlgeschlagen', error);
                    alert('Logout fehlgeschlagen. Bitte versuchen Sie es erneut.');
                });
            }
        }



    }
}
</script>
<style scoped>
@import url(../../../css/sidebar/main-sidebar-opened.css)
</style>
