<template>
  <div class="calendar-container">
    <vue-cal
      :events="events"
      :time-from="8 * 60"
      :time-to="20 * 60"
      :time-step="30"
      :active-view="activeView"
      :key="activeView"
      :selected-date="selectedDate"
      :on-event-click="onEventClick"
      :event-content-renderer="renderEventContent"
      :min-event-width="100"
      :min-cell-width="100"
      :min-cell-height="100"
      :snap-to-time="15"
      :sticky-split-labels="true"
      :hide-weekends="false"
      :start-week-on-sunday="false"
      :cell-click-hold="false"
      :drag-to-create-event="false"
      :event-duration-resizable="false"
      :event-draggable="false"
      :editable-events="{ title: false, drag: false, resize: false, create: false }"
      :views="['month', 'week', 'day']"
      @view-change="updateView"
    >
      <template #event="{ event, view }">
        <div class="vuecal__event-title">{{ event.title }}</div>
        <div class="vuecal__event-content">{{ event.content }}</div>
      </template>
    </vue-cal>

    <div class="legend-container">
      <h4>Legende:</h4>
      <div class="legend-item">
        <span class="legend-color ausstehend"></span>
        <span>Ausstehend</span>
      </div>
      <div class="legend-item">
        <span class="legend-color in-bearbeitung"></span>
        <span>In Bearbeitung</span>
      </div>
      <div class="legend-item">
        <span class="legend-color abgeschlossen"></span>
        <span>Abgeschlossen</span>
      </div>
    </div>

    <div v-if="selectedEvent" class="event-details">
      <h3>{{ selectedEvent.title }}</h3>
      <p><strong>Status:</strong> {{ selectedEvent.status }}</p>
      <p><strong>Email:</strong> {{ selectedEvent.email }}</p>
      <p><strong>Start:</strong> {{ selectedEvent.start.format('DD.MM.YYYY HH:mm') }}</p>
      <p><strong>End:</strong> {{ selectedEvent.end.format('DD.MM.YYYY HH:mm') }}</p>
      <p v-if="selectedEvent.content"><strong>Description:</strong> {{ selectedEvent.content }}</p>
    </div>
  </div>
</template>

<script>
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

export default {
  components: { VueCal },
  data() {
    return {
      events: [],
      selectedDate: new Date(),
      selectedEvent: null,
      activeView: 'week',
    };
  },
  mounted() {
    this.fetchEvents();
  },
  methods: {
    fetchEvents() {
      // Mock data for demonstration. In a real application, you would fetch this from an API.
      // Example API call: axios.get('/api/calendar-events').then(response => { this.events = response.data; });
      this.events = [
        {
          start: '2025-07-14 10:00',
          end: '2025-07-14 11:00',
          title: 'Aufgabe 1',
          content: 'Beschreibung der Aufgabe 1',
          status: 'Ausstehend',
          email: 'user1@example.com',
          class: 'ausstehend',
        },
        {
          start: '2025-07-14 14:00',
          end: '2025-07-14 15:30',
          title: 'Aufgabe 2',
          content: 'Beschreibung der Aufgabe 2',
          status: 'In Bearbeitung',
          email: 'user2@example.com',
          class: 'in-bearbeitung',
        },
        {
          start: '2025-07-15 09:00',
          end: '2025-07-15 10:00',
          title: 'Aufgabe 3',
          content: 'Beschreibung der Aufgabe 3',
          status: 'Abgeschlossen',
          email: 'user3@example.com',
          class: 'abgeschlossen',
        },
        {
          start: '2025-07-15 11:00',
          end: '2025-07-15 12:00',
          title: 'Aufgabe 4',
          content: 'Beschreibung der Aufgabe 4',
          status: 'Ausstehend',
          email: 'user4@example.com',
          class: 'ausstehend',
        },
      ];
    },
    onEventClick(event, e) {
      this.selectedEvent = event;
      e.stopPropagation();
    },
    renderEventContent(event, view) {
      return `
        <div class="vuecal__event-title">${event.title}</div>
        <div class="vuecal__event-content">${event.email}</div>
      `;
    },
    updateView(newView) {
      this.activeView = newView.view;
    },
  },
};
</script>

<style>
.calendar-container {
  padding: 20px;
  background-color: #f8f8f8; /* Light background for the calendar area */
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  margin: 20px;
  position: relative; /* Added to ensure proper stacking context */
}

.vuecal {
  height: 700px; /* Adjust height as needed */
  width: 100%; /* Ensure it takes full width of its container */
}

/* Aggressive styles for VueCal navigation to ensure visibility and clickability */
.vuecal__menu {
  background-color: #f0f0f0 !important;
  border-bottom: 1px solid #e0e0e0 !important;
  padding: 10px !important;
  position: relative !important;
  z-index: 9999 !important; /* Ensure it's above other elements */
}

.vuecal__flex.vuecal__menu-buttons {
  display: flex !important;
  justify-content: space-around !important;
  align-items: center !important;
}

.vuecal__menu-button {
  padding: 8px 15px !important;
  border: 1px solid #ccc !important;
  border-radius: 4px !important;
  background-color: #fff !important;
  cursor: pointer !important;
  transition: background-color 0.3s ease !important;
  color: #333 !important; /* Ensure text color is visible */
  pointer-events: auto !important; /* Ensure clicks are registered */
  z-index: 99999 !important; /* Ensure buttons are on top */
}

.vuecal__menu-button:hover {
  background-color: #e0e0e0 !important;
}

.vuecal__menu-button.vuecal__active {
  background-color: #007bff !important;
  color: white !important;
  border-color: #007bff !important;
}

.vuecal__event.ausstehend {
  background-color: #ffcc80; /* Light orange */
  color: #333;
}
.vuecal__event.in-bearbeitung {
  background-color: #80deea; /* Light blue */
  color: #333;
}
.vuecal__event.abgeschlossen {
  background-color: #a5d6a7; /* Light green */
  color: #333;
}

.vuecal__event-title {
  font-weight: bold;
  margin-bottom: 5px;
}

.vuecal__event-content {
  font-size: 0.9em;
}

.event-details {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #f9f9f9;
}

.legend-container {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #f9f9f9;
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  align-items: center;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 5px;
}

.legend-color {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  display: inline-block;
}

.legend-color.ausstehend {
  background-color: #ffcc80; /* Light orange */
}
.legend-color.in-bearbeitung {
  background-color: #80deea; /* Light blue */
}
.legend-color.abgeschlossen {
  background-color: #a5d6a7; /* Light green */
}
</style>