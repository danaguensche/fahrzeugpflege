<template>
  <div class="calendar-container">
    <div class="calendar-page" :class="{ 'calendar-page-sidebar-opened': isSidebarOpen }">
      <vue-cal
        class="vuecal"
        :events="events"
        :time-from="8 * 60"
        :time-to="20 * 60"
        :time-step="30"
        :active-view="activeView"
        :key="activeView"
        :selected-date="selectedDate"
        :on-event-click="onEventClick"
        :event-content-renderer="renderEventContent"
        :cell-content-renderer="renderCellContent"
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
        :disable-views="['years', 'year']"
        @view-change="updateView"
        @ready="onCalendarReady"
        @cell-click="onDayClick"
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
        <div class="legend-item">
          <span class="legend-color im-rueckblick"></span>
          <span>Im Rückblick</span>
        </div>
      </div>

      <div v-if="selectedEvent" class="event-details" id="event-details-section">
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
  </div>
</template>

<script>
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';
import axios from 'axios';
import { mapState } from 'vuex';

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
  computed: {
    ...mapState(['isSidebarOpen']),
    calendarMarginLeft() {
      return this.isSidebarOpen ? '260px' : '110px';
    },
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
          this.events = response.data.items.map(job => {
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
      this.$nextTick(() => {
        const element = document.getElementById('event-details-section');
        if (element) {
          element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
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
    onDayClick(date) {
      if (this.activeView === 'month') {
        this.activeView = 'week';
        this.selectedDate = date;
      }
    },
    renderCellContent(cell, view) {
      if (view.id === 'month' && cell.events.length) {
        return `<div class="event-count">${cell.events.length} Aufgaben</div>`;
      }
    },
  },
};
</script>

<style>
.calendar-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  padding: 20px;
  box-sizing: border-box;
}

.calendar-page {
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.5s ease;
  margin-left: 150px;
  padding: 30px;
  width: calc(100% - 160px);
  max-width: 1400px;
  margin-right: auto;
  height: calc(100vh - 70px);
}

.calendar-page-sidebar-opened {
  margin-left: 320px;
  width: calc(100% - 340px);
}

.vuecal {
  height: calc(100vh - 250px);
  min-height: 600px;
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

/* Responsive Design */
@media (max-width: 1200px) {
  .calendar-page {
    margin-left: 20px;
    width: calc(100% - 40px);
    padding: 20px;
  }
  
  .calendar-page-sidebar-opened {
    margin-left: 280px;
    width: calc(100% - 300px);
  }
  
  .vuecal {
    height: calc(100vh - 200px);
    min-height: 500px;
  }
}

@media (max-width: 768px) {
  .calendar-container {
    padding: 10px;
  }
  
  .calendar-page {
    margin-left: 10px;
    width: calc(100% - 20px);
    padding: 15px;
  }
  
  .calendar-page-sidebar-opened {
    margin-left: 10px;
    width: calc(100% - 20px);
  }
  
  .vuecal {
    height: calc(100vh - 120px);
    min-height: 400px;
  }
}

/* VueCal Navigation Styles */
.vuecal__menu {
  background-color: #f8f9fa !important;
  border-bottom: 2px solid #e9ecef !important;
  padding: 15px !important;
  position: relative !important;
  z-index: 1000 !important;
}

.vuecal__flex.vuecal__menu-buttons {
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  gap: 10px !important;
}

.vuecal__menu-button {
  padding: 10px 20px !important;
  border: 2px solid #dee2e6 !important;
  border-radius: 8px !important;
  background-color: #ffffff !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  color: #495057 !important;
  font-weight: 500 !important;
  font-size: 14px !important;
  pointer-events: auto !important;
  z-index: 1001 !important;
}

.vuecal__menu-button:hover {
  background-color: #f8f9fa !important;
  border-color: #6c757d !important;
  transform: translateY(-1px) !important;
}

.vuecal__menu-button.vuecal__active {
  background-color: #007bff !important;
  color: white !important;
  border-color: #007bff !important;
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3) !important;
}

/* Event Styles */
.vuecal__event.ausstehend {
  background: linear-gradient(135deg, #ffcc80 0%, #ffb74d 100%);
  color: #333;
  border-left: 4px solid #ff9800;
}

.vuecal__event.in-bearbeitung {
  background: linear-gradient(135deg, #80deea 0%, #4dd0e1 100%);
  color: #333;
  border-left: 4px solid #00bcd4;
}

.vuecal__event.abgeschlossen {
  background: linear-gradient(135deg, #a5d6a7 0%, #81c784 100%);
  color: #333;
  border-left: 4px solid #4caf50;
}
.vuecal__event.im-rueckblick {
  background: linear-gradient(135deg, #ffecb3 0%, #ffe082 100%);
  color: #333;
  border-left: 4px solid #e9c455;
}
.vuecal__event-title {
  font-weight: 600;
  margin-bottom: 4px;
  font-size: 0.9em;
}

.vuecal__event-content {
  font-size: 0.8em;
  opacity: 0.9;
}

/* Legend Styles */
.legend-container {
  margin-top: 25px;
  padding: 20px;
  border: 1px solid #e9ecef;
  border-radius: 10px;
  background-color: #f8f9fa;
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.legend-container h4 {
  margin: 0;
  color: #495057;
  font-size: 1.1em;
  font-weight: 600;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.95em;
  color: #495057;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
  display: inline-block;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.legend-color.ausstehend {
  background: linear-gradient(135deg, #ffcc80 0%, #ffb74d 100%);
}

.legend-color.in-bearbeitung {
  background: linear-gradient(135deg, #80deea 0%, #4dd0e1 100%);
}

.legend-color.im-rueckblick {
  background: linear-gradient(135deg, #ffecb3 0%, #ffe082 100%);
}

.legend-color.abgeschlossen {
  background: linear-gradient(135deg, #a5d6a7 0%, #81c784 100%);
}

/* Event Details Styles */
.event-details {
  z-index: 9999;
  position: relative;
  margin-top: 30px;
  padding: 25px;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  background-color: #ffffff;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  font-family: 'Rubik', sans-serif;
}

.event-details h3 {
  font-size: 1.5em;
  color: #212529;
  margin-bottom: 20px;
  border-bottom: 2px solid #f8f9fa;
  padding-bottom: 12px;
  font-weight: 600;
}

.event-details p {
  font-size: 1em;
  line-height: 1.6;
  margin-bottom: 12px;
  color: #495057;
}

.event-details p strong {
  color: #212529;
  font-weight: 600;
}

.event-details a {
  color: #007bff;
  text-decoration: none;
  transition: color 0.2s ease;
  font-weight: 500;
}

.event-details a:hover {
  color: #0056b3;
  text-decoration: underline;
}

.service-tag {
  display: inline-block;
  background-color: #e9ecef;
  color: #495057;
  padding: 6px 12px;
  border-radius: 20px;
  margin-right: 8px;
  margin-bottom: 6px;
  font-size: 0.85em;
  font-weight: 500;
  white-space: nowrap;
  transition: background-color 0.2s ease;
}

.service-tag:hover {
  background-color: #dee2e6;
}

/* Event Count Badge */
.event-count {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: white;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75em;
  font-weight: 600;
  margin-top: 5px;
  box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
}

.vuecal__cell-date {
  font-size: 1.1em;
  font-weight: 600;
  color: #495057;
}

/* Scrollbar Styling */
.vuecal::-webkit-scrollbar {
  width: 8px;
}

.vuecal::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.vuecal::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.vuecal::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>