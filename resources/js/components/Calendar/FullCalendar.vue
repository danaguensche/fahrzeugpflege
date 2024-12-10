<template>
    <AddButton class="add" tooltip-text="Termin hinzufügen"></AddButton>


    <div>
        <VueCal class="calendar" :class="{ 'calendar-sidebar-opened': isSidebarOpen }" locale="de" :time="false"
            hide-weekends hide-view-selector:time="false" :transitions="false" active-view="week"
            :disable-views="['years']" today-button :selected-date="selectedDate" :events="events">
        </VueCal>
    </div>



</template>

<script>
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

import axios from 'axios';

import { mapState } from "vuex"
import AddButton from '../CommonSlots/AddButton.vue';
import EditButton from '../CommonSlots/EditButton.vue';

export default {
    components: {
        VueCal,
        AddButton,
        EditButton
    },

    computed: {
        ...mapState(['isSidebarOpen'])
    },

    data() {
        return {
            selectedDate: new Date(new Date().getFullYear(), 11, 31),

            events: [
                {
                    start: new Date(),
                    end: new Date(),
                    // You can also define event dates with Javascript Date objects:
                    // start: new Date(2018, 11 - 1, 16, 10, 30),
                    // end: new Date(2018, 11 - 1, 16, 11, 30),
                    title: '',
                    content: '<i class="icon material-icons"></i>',
                    class: ''
                }
            ]
        }
    },
    methods: {
        loadEvents() {
            axios.get('api/events').then(response => {
                this.calendarOptions.events = response.data
            }).catch(error => {
                console.error("Fehler beim Laden des Events: ", error)
            })
        }
    },
    mounted() {
        this.loadEvents()
    }
}
</script>

<style>
.calendar {
    position: fixed;
    top: 5%;
    bottom: 5%;
    left: 15%;
    right: 10%;
    transition: left 0.3s ease;
    font-family: var(--font-family);
    box-shadow: 2px 0 5px var(--clr-box-shadow);
    height: 700px
}

.calendar-sidebar-opened {
    left: 25%;
    transition: left 0.3s ease;
}

.add {
    margin-left: 90%;
}


/* Theme */

.vuecal__menu,
.vuecal__cell-events-count {
    background-color: var(--primary-color);
}

.vuecal__title-bar {
    background-color: var(--primary-color-hover);
}

.vuecal__cell--today,
.vuecal__cell--current {
    background-color: #dceaea;

}

.vuecal:not(.vuecal--day-view) .vuecal__cell--selected {
    background-color: #82b5b5;
}

.vuecal__cell--selected:before {
    background-color: var(--primary-color-hover);
    border-color: var(--primary-color);
}


/* Cells and buttons get highlighted when an event is dragged over it. */
.vuecal__cell--highlighted:not(.vuecal__cell--has-splits),
.vuecal__cell-split--highlighted {
    /* background-color: rgba(195, 255, 225, 0.5); */
}

.vuecal__arrow.vuecal__arrow--highlighted,
.vuecal__view-btn.vuecal__view-btn--highlighted {
    /* background-color: rgba(136, 236, 191, 0.25); */
}

.vuecal__view-btn {
    /* background-color: #ff8f61; */
    color: white;
    text-shadow: white;
}

.vuecal__flex.vuecal__title {
    color: white;
}

@media screen and (min-width: 1800px) {
    .calendar {
        height: 1100px;
    }
}
</style>