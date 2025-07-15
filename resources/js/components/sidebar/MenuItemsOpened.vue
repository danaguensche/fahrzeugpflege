<template>
    <div class="sidebar-container">
        <div v-for="(menuitem, index) in filteredMenuItems" :key="menuitem.id"
            :class="['sidebar-button', { 'profile-spacing': menuitem.name === 'Profil' }]">
            <div class="sidebar-buttons-wrapper">
                <MenuButton @click="redirectToView(menuitem)" class="menu-button-content">
                    <img v-if="iconPaths[index]" :src="iconPaths[index].name" class="icon"
                        alt="Icon for {{ menuitem.name }}">
                    <span class="sidebar-textContent">{{ menuitem.name }}</span>
                </MenuButton>
            </div>
        </div>
    </div>
    <VuetifyAlert v-model="isAlertVisible" maxWidth="500" alertTypeClass="alertTypeConfirmation"
            alertHeading="Abmelden"
            alertParagraph="Wollen Sie sich abmelden?"
            alertOkayButton="Abmelden" alertCloseButton="Abbrechen" @confirmation="logout">
        </VuetifyAlert>
</template>


<script>
import VuetifyAlert from '../Alerts/VuetifyAlert.vue';
import MenuButton from './Slots/MenuButton.vue';
import axios from 'axios';
import { mapState } from 'vuex';

export default {
    name: 'MenuItemsOpened',

    components: {
        MenuButton,
        VuetifyAlert
    },

    data() {
        return {
            isAlertVisible: false,
            alertHandlers: {
                confirmationClicked: this.logout,
                closeAlertClicked: this.updateAlertVisibility
            },
            //Text-Inhalte der Sidebar
            menuitems: [
                { id: 1, name: 'Dashboard', roles: ['trainee', 'trainer', 'admin'] },
                { id: 2, name: 'Kalender', roles: ['trainee', 'trainer', 'admin'] },
                { id: 3, name: 'Fahrzeuge', roles: ['trainee', 'trainer', 'admin'] },
                { id: 4, name: 'Kunden', roles: ['trainer', 'admin'] },
                { id: 5, name: 'Aufträge', roles: ['trainee', 'trainer', 'admin'] },
                { id: 6, name: 'Berichte', roles: ['trainer', 'admin'] },
                { id: 7, name: 'Benutzer', roles: ['admin'] },
                { id: 8, name: 'Profil', roles: ['trainee', 'trainer', 'admin'] },
                { id: 9, name: 'Einstellungen', roles: ['trainer', 'admin'] },
                { id: 10, name: 'Abmelden', roles: ['trainee', 'trainer', 'admin'] },
            ],

            //Bildnamen
            iconPaths: [
                { id: 1, name: new URL('@/img/sidebar-img/dashboard-icon.png', import.meta.url).href },
                { id: 2, name: new URL('@/img/sidebar-img/calendar-icon.png', import.meta.url).href },
                { id: 3, name: new URL('@/img/sidebar-img/cars-icon.png', import.meta.url).href },
                { id: 4, name: new URL('@/img/sidebar-img/customer-icon.png', import.meta.url).href },
                { id: 5, name: new URL('@/img/sidebar-img/jobs-icon.png', import.meta.url).href },
                { id: 6, name: new URL('@/img/sidebar-img/reports-icon.png', import.meta.url).href },
                { id: 7, name: new URL('@/img/sidebar-img/user-icon.png', import.meta.url).href }, /* New icon for Users */
                { id: 8, name: new URL('@/img/sidebar-img/profile-icon.png', import.meta.url).href },
                { id: 9, name: new URL('@/img/sidebar-img/settings-icon.png', import.meta.url).href },
                { id: 10, name: new URL('@/img/sidebar-img/logout-icon.png', import.meta.url).href },
            ]
        }
    },
    computed: {
        ...mapState(['userRole']),
        filteredMenuItems() {
            return this.menuitems.filter(item => item.roles.includes(this.userRole));
        }
    },
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
                this.updateAlertVisibility();
            } else {
                this.$router.push(this.getRoute(menuitem.name));
            }
        },

        logout() {
            this.isAlertVisible = false;
                axios.post('/logout', {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => {
                    localStorage.removeItem('apiToken');
                    this.$router.push('/login');
                }).catch(error => {
                    console.error('Logout fehlgeschlagen', error);
                    alert('Logout fehlgeschlagen. Bitte versuchen Sie es erneut.');
                });
        },
        updateAlertVisibility() {
            this.isAlertVisible = !this.isAlertVisible;
        }



    }
}
</script>
<style scoped>
.sidebar-container,
.sidebar-container.closed {
    transition: width 0.3s ease;
}

.sidebar-container {
    width: 260px;
    height: 100vh;
    background-color: var(--clr-bg-sidebar);
    color: var(--clr-dark);
    padding: 2.5vh;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: fixed;
    border-radius: 10px;
    box-shadow: 2px 0 5px var(--clr-box-shadow);
    user-select: none;
    transition: width 0.6s ease;
}

.sidebar-buttons-wrapper {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    width: 200px;
    transform: scale(1.05);
}


.sidebar-button.profile-spacing {
    margin-top: 50px;
}


.menu-button-content:hover {
    background-color: var(--clr-hover);
    transform: scale(1.05);
    color: var(--clr-light);
}

.menu-button-content:hover .sidebar-textContent {
    color: var(--clr-hover-text);
}

.menu-button-content:hover .icon {
    filter: brightness(200%);
}

.icon {
    width: 24px;
    height: 24px;
    margin-right: 15px;
    flex-shrink: 0;
}

.menu-button-content {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    background-color: var(--clr-light);
    border-radius: 10px;
    cursor: pointer;
    font-family: Arial, sans-serif;
    font-size: 16px;
    padding: 10px 15px;
    text-decoration: none;
    color: var(--clr-button-text);
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px var(--clr-box-shadow);
    margin-top: 15px;
    text-align: left;
}


.sidebar-textContent {
    margin-left: 2vh;
    flex-grow: 1;
    font-size: 16px;
    color: var(--clr-link);
    font-family: "Rubik", sans-serif;
    text-decoration: none;
    transition: color 0.3s ease;
    text-align: left;
}

.rubik-font {
    font-family: "Rubik", sans-serif;
}

@media (max-width: 768px) {
    .sidebar-container {
        width: 220px;
        transition: width 0.3s ease;

    }

    .sidebar-buttons-wrapper {
        width: 160px;
    }

    .sidebar-textContent {
        font-size: 14px;
    }

    .sidebar-button.profile-spacing {
        margin-top: 5vh;
    }
}
</style>
