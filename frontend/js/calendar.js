function calendar() {
    return {
        currentDate: new Date(),
        selectedDate: null,
        selectedEvent: null,
        showModal: false,
        showDeleteConfirm: false,
        weekDays: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        events: [],
        eventForm: {
            title: '',
            description: '',
            startTime: '',
            endTime: ''
        },
        isTransitioning: false,

        get currentMonthYear() {
            return this.currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });
        },

        isToday(date) {
            const today = new Date();
            return date.getDate() === today.getDate() &&
                   date.getMonth() === today.getMonth() &&
                   date.getFullYear() === today.getFullYear();
        },

        get calendarDays() {
            // Get today's date
            const today = new Date();
            
            // Get the start of the current week (Monday)
            const startOfWeek = new Date(this.currentDate);
            const dayOfWeek = this.currentDate.getDay();
            // Adjust to make Monday the first day (0 = Sunday, 1 = Monday, etc.)
            const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
            startOfWeek.setDate(this.currentDate.getDate() - daysToSubtract);
            
            // Calculate the end date (3 weeks from the start of the current week)
            const endDate = new Date(startOfWeek);
            endDate.setDate(startOfWeek.getDate() + 20); // 3 weeks = 21 days, but we already have the first day
            
            const days = [];
            
            // Generate all days from the start of the current week to the end date
            const currentDate = new Date(startOfWeek);
            while (currentDate <= endDate) {
                days.push({
                    date: new Date(currentDate),
                    dayOfMonth: currentDate.getDate(),
                    isCurrentMonth: currentDate.getMonth() === today.getMonth(),
                    isToday: this.isToday(currentDate),
                    events: this.getEventsForDate(new Date(currentDate))
                });
                
                // Move to the next day
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            return days;
        },

        init() {
            this.fetchEvents();
            this.showModal = false;
        },

        async fetchEvents() {
            try {
                const response = await fetch('http://localhost:8000/api/events', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.events = data.data;
                }
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        },

        getEventsForDate(date) {
            return this.events
                .filter(event => {
                    const eventDate = new Date(event.start_time);
                    return eventDate.toDateString() === date.toDateString();
                })
                .sort((a, b) => {
                    const timeA = new Date(a.start_time);
                    const timeB = new Date(b.start_time);
                    return timeA - timeB;
                });
        },

        previousWeek() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), this.currentDate.getDate() - 7);
        },

        nextWeek() {
            this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), this.currentDate.getDate() + 7);
        },

        selectDate(day) {
            this.selectedDate = day.date;
            
            // Set default times (9 AM to 10 AM)
            const startTime = new Date(day.date);
            startTime.setHours(9, 0, 0, 0);
            
            const endTime = new Date(day.date);
            endTime.setHours(10, 0, 0, 0);
            
            this.eventForm = {
                title: '',
                description: '',
                startTime: startTime.toISOString().slice(0, 16),
                endTime: endTime.toISOString().slice(0, 16)
            };
            
            this.selectedEvent = null;
            this.showModal = true;
            
            // Add a visual feedback for the selected date
            const selectedElement = document.querySelector(`[data-date="${day.date.toISOString().split('T')[0]}"]`);
            if (selectedElement) {
                selectedElement.classList.add('ring-2', 'ring-blue-500');
            }
        },

        showEventDetails(event) {
            this.selectedEvent = event;
            this.eventForm = {
                title: event.title,
                description: event.description || '',
                startTime: event.start_time.slice(0, 16),
                endTime: event.end_time.slice(0, 16)
            };
            this.showModal = true;
        },

        async saveEvent() {
            const eventData = {
                title: this.eventForm.title,
                description: this.eventForm.description,
                start_time: this.eventForm.startTime,
                end_time: this.eventForm.endTime
            };

            try {
                const url = this.selectedEvent 
                    ? `http://localhost:8000/api/events/${this.selectedEvent.id}`
                    : 'http://localhost:8000/api/events';
                
                const response = await fetch(url, {
                    method: this.selectedEvent ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(eventData)
                });

                if (response.ok) {
                    await this.fetchEvents();
                    this.closeModal();
                } else {
                    console.error('Error saving event:', await response.text());
                }
            } catch (error) {
                console.error('Error saving event:', error);
            }
        },

        closeModal() {
            this.showModal = false;
            this.showDeleteConfirm = false;
            this.selectedEvent = null;
            this.eventForm = {
                title: '',
                description: '',
                startTime: '',
                endTime: ''
            };
            
            // Remove visual feedback for the selected date
            const selectedElements = document.querySelectorAll('.ring-2.ring-blue-500');
            selectedElements.forEach(element => {
                element.classList.remove('ring-2', 'ring-blue-500');
            });
        },

        formatDateTimeLocal(date) {
            // Format date to YYYY-MM-DDThh:mm format in local timezone
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },
        
        logout() {
            localStorage.removeItem('token');
            window.location.href = 'login.html';
        },

        async confirmDelete() {
            if (!this.selectedEvent) return;

            try {
                const response = await fetch(`http://localhost:8000/api/events/${this.selectedEvent.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });

                if (response.ok) {
                    await this.fetchEvents();
                    this.closeModal();
                } else {
                    console.error('Error deleting event:', await response.text());
                }
            } catch (error) {
                console.error('Error deleting event:', error);
            }
        }
    };
}