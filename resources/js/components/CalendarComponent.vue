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
      @ready="onCalendarReady"
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
      <h3><router-link :to="'/auftraege/jobdetails/' + selectedEvent.job_id">{{ selectedEvent.title }}</router-link></h3>
      <p v-if="selectedEvent.content"><strong>Beschreibung:</strong> {{ selectedEvent.content }}</p>
      <p><strong>Status:</strong> {{ selectedEvent.status }}</p>
      <p><strong>Kunde:</strong> <router-link :to="'/kunden/kundendetails/' + selectedEvent.customer_id">{{ selectedEvent.customer_firstname }} {{ selectedEvent.customer_lastname }}</router-link></p>
      <p><strong>Email:</strong> <a :href="'mailto:' + selectedEvent.email">{{ selectedEvent.email }}</a></p>
      <p><strong>Car Kennzeichen:</strong> <router-link :to="'/fahrzeuge/fahrzeugdetails/' + selectedEvent.car_kennzeichen">{{ selectedEvent.car_kennzeichen }}</router-link></p>
      <p><strong>Services:</strong>
        <span v-for="(service, index) in selectedEvent.services_list" :key="index" class="service-tag">{{ service }}</span>
      </p>
      <p><strong>Start:</strong> {{ selectedEvent.start.format('DD.MM.YYYY HH:mm') }}</p>
      <p><strong>End:</strong> {{ selectedEvent.end.format('DD.MM.YYYY HH:mm') }}</p>
    </div>
  </div>
</template>

<script>
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';
import axios from 'axios';

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
    // this.fetchEvents(); // Will be called by @ready event
  },
  methods: {
    fetchEvents(startDate, endDate) {
      axios.get('/api/jobs', {
        params: {
          start: startDate.toISOString().slice(0, 10) + ' 00:00:00',
          end: endDate.toISOString().slice(0, 10) + ' 23:59:59',
        },
      })
        .then(response => {
          this.events = response.data.map(job => {
            const start = new Date(job.scheduled_at);
            const end = new Date(start.getTime() + 60 * 60 * 1000); // Assuming 1 hour duration
            const eventClass = job.status.replace(/_/g, '-');
            console.log(`Job Status: ${job.status}, Assigned Class: ${eventClass}`);
            return {
              start: start.toISOString().slice(0, 16).replace('T', ' '),
              end: end.toISOString().slice(0, 16).replace('T', ' '),
              title: job.title,
              content: job.description,
              status: job.status,
              email: job.customer ? job.customer.email : 'N/A',
              customer_firstname: job.customer ? job.customer.firstname : 'N/A',
              customer_lastname: job.customer ? job.customer.lastname : 'N/A',
              customer_id: job.customer ? job.customer.id : null,
              car_kennzeichen: job.car ? job.car.Kennzeichen : 'N/A',
              car_make: job.car ? job.car.make : 'N/A',
              car_model: job.car ? job.car.model : 'N/A',
              services_list: job.services ? job.services.map(service => service.title) : [],
              job_id: job.id,
              class: job.status.replace(/_/g, '-'),
            };
          });
        })
        .catch(error => {
          console.error("Error fetching events:", error);
        });
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
      this.fetchEvents(newView.startDate, newView.endDate);
    },
    onCalendarReady(view) {
      this.fetchEvents(view.startDate, view.endDate);
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
  margin-top: 25px;
  padding: 20px;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  background-color: #ffffff; /* Clean white background */
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* More prominent shadow */
  font-family: 'Rubik', sans-serif; /* Use consistent font */
}

.event-details h3 {
  font-size: 1.6em;
  color: #333; /* Darker heading color */
  margin-bottom: 15px;
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 10px;
}

.event-details p {
  font-size: 1em;
  line-height: 1.6;
  margin-bottom: 8px;
  color: #555; /* Slightly lighter text color */
}

.event-details p strong {
  color: #333;
}

.event-details a {
  color: #007bff; /* A clear blue for links */
  text-decoration: none;
  transition: color 0.2s ease-in-out;
}

.event-details a:hover {
  color: #0056b3; /* Darker blue on hover */
  text-decoration: underline;
}

.service-tag {
  display: inline-block;
  background-color: #e0e0e0; /* Light grey background */
  color: #333; /* Dark text */
  padding: 5px 10px;
  border-radius: 15px; /* Rounded corners for tag look */
  margin-right: 8px; /* Space between tags */
  margin-bottom: 5px; /* Space below tags */
  font-size: 0.9em;
  white-space: nowrap; /* Prevent text wrapping */
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