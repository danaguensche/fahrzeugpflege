<!-- Farbe für Icons ist #808080 -->

<template>
    <div class="sidebar-container-closed">
        <div v-for="(menuitem, index) in menuitems" :key="menuitem.id"
            :class="['sidebar-button closed', { 'profile-spacing closed': menuitem.name === 'Profil' }]">
            <div class="sidebar-buttons-wrapper" @click="redirectToView(menuitem)">
                <MenuButton class="menu-button-content">
                    <img :src="iconPaths[index].name" class="icon closed" alt="Icon for {{ menuitem.name }}">
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
import MenuButton from './Slots/MenuButton.vue';
import axios from 'axios';
import VuetifyAlert from '../Alerts/VuetifyAlert.vue';

export default {
    name: 'MenuItemsClosed',

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
                { id: 1, name: 'Dashboard' },
                { id: 2, name: 'Kalender' },
                { id: 3, name: 'Fahrzeuge' },
                { id: 4, name: 'Kunden' },
                { id: 5, name: 'Aufträge' },
                { id: 6, name: 'Berichte' },
                
                { id: 7, name: 'Profil' },
                { id: 8, name: 'Einstellungen' },
                { id: 9, name: 'Abmelden' },
            ],
            // Bildnamen
            iconPaths: [
                { id: 1, name: new URL('@/img/sidebar-img/dashboard-icon.png', import.meta.url).href },
                { id: 2, name: new URL('@/img/sidebar-img/calendar-icon.png', import.meta.url).href },
                { id: 3, name: new URL('@/img/sidebar-img/cars-icon.png', import.meta.url).href },
                { id: 4, name: new URL('@/img/sidebar-img/customer-icon.png', import.meta.url).href },
                { id: 5, name: new URL('@/img/sidebar-img/jobs-icon.png', import.meta.url).href },
                { id: 6, name: new URL('@/img/sidebar-img/reports-icon.png', import.meta.url).href },
                { id: 7, name: new URL('@/img/sidebar-img/profile-icon.png', import.meta.url).href },
                { id: 8, name: new URL('@/img/sidebar-img/settings-icon.png', import.meta.url).href },
                { id: 9, name: new URL('@/img/sidebar-img/logout-icon.png', import.meta.url).href },
            ]
        }
    },

    methods: {
        //Formatiert Einträge aus dem menuitems Array und gibt die Route zu der dazugehörigen Seite zurück
        getRoute(pageName) {
            let pageName_lowerCase = pageName.toLowerCase();
            let pageName_formatted = this.removeUmlauts(pageName_lowerCase);
            return `/${pageName_formatted}`;
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
    },
}
</script>
<style scoped>

.sidebar-container-closed {
    width: 110px;
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
    transform: scale(1.05);
}

/* Menu Button Styles */
.menu-button-content {
    display: flex;
    align-items: center;
    justify-content: center;
    
    width: 100%;
    background-color: var(--clr-light);
    border-radius: 10px;
    cursor: pointer;
    padding: 10px 15px;
    text-decoration: none;
    color: var(--clr-button-text);

    box-shadow: 0 2px 5px var(--clr-box-shadow);
    margin-top: 15px;

    transition: all 0.6s ease;
}

.sidebar-button.closed {
    border-radius: 10px;
}

.profile-spacing.closed {
    margin-top: 50px;
}


.icon.closed {
    width: 24px;
    height: 24px;
}

.sidebar-button.closed:hover .icon.closed {
    filter: brightness(200%);
    transform: scale(1.06);
}

.menu-button-content:hover {
    background-color: var(--clr-hover);
    transform: scale(1.06);
    color: var(--clr-light);
}

@media (max-width: 768px) {
    .sidebar-container.closed {
        width: auto;
        transition: width 0.3s ease;
    }

    .sidebar-button.closed {
        width: auto;
    }

    .icon.closed {
        width: auto;
        height: auto;
    }

    .sidebar-button.profile-spacing.closed {
        margin-top: 5vh;
    }
}
</style>
