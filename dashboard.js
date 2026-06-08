// Sample data for classrooms and bookings
const classrooms = [
    { id: 1, name: 'Room 101', capacity: 30 },
    { id: 2, name: 'Room 102', capacity: 25 },
    { id: 3, name: 'Room 103', capacity: 40 },
    { id: 4, name: 'Room 104', capacity: 35 },
    { id: 5, name: 'Room 105', capacity: 20 },
    { id: 6, name: 'Room 106', capacity: 45 }
];

// Store bookings in memory (for testing purposes)
let bookings = [];

// DOM Elements
const navLinks = document.querySelectorAll('.nav-links a');
const pages = document.querySelectorAll('.page');
const datePicker = document.getElementById('booking-date');
const classroomsList = document.getElementById('classrooms-list');
const bookingsList = document.getElementById('bookings-list');

// Set minimum date to today
const today = new Date().toISOString().split('T')[0];
datePicker.min = today;

// Navigation
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        if (link.classList.contains('logout')) return;
        
        e.preventDefault();
        const targetPage = link.getAttribute('data-page');
        
        // Update active states
        navLinks.forEach(l => l.parentElement.classList.remove('active'));
        link.parentElement.classList.add('active');
        
        // Show target page
        pages.forEach(page => {
            page.classList.remove('active');
            if (page.id === targetPage) {
                page.classList.add('active');
            }
        });
    });
});

// Display Classrooms
function displayClassrooms(date) {
    classroomsList.innerHTML = '';
    
    classrooms.forEach(classroom => {
        const card = document.createElement('div');
        card.className = 'classroom-card';
        
        const periods = generatePeriods(classroom.id, date);
        
        card.innerHTML = `
            <h3>${classroom.name}</h3>
            <p>Capacity: ${classroom.capacity} students</p>
            <div class="period-grid">
                ${periods.map((period, index) => `
                    <button class="period-btn ${period.available ? 'available' : 'booked'}"
                            ${!period.available ? 'disabled' : ''}
                            data-room="${classroom.id}"
                            data-period="${index + 1}">
                        Period ${index + 1}
                    </button>
                `).join('')}
            </div>
        `;
        
        classroomsList.appendChild(card);
    });
    
    // Add event listeners to period buttons
    document.querySelectorAll('.period-btn.available').forEach(btn => {
        btn.addEventListener('click', handleBooking);
    });
}

// Generate periods for a classroom
function generatePeriods(roomId, date) {
    const periods = Array(8).fill().map(() => ({ available: true }));
    
    // Mark booked periods
    bookings.forEach(booking => {
        if (booking.roomId === roomId && booking.date === date) {
            periods[booking.period - 1].available = false;
        }
    });
    
    return periods;
}

// Handle booking
function handleBooking(e) {
    const roomId = parseInt(e.target.dataset.room);
    const period = parseInt(e.target.dataset.period);
    const date = datePicker.value;
    
    if (!date) {
        alert('Please select a date first');
        return;
    }
    
    const classroom = classrooms.find(room => room.id === roomId);
    
    // Create new booking
    const booking = {
        id: Date.now(),
        roomId,
        roomName: classroom.name,
        date,
        period
    };
    
    bookings.push(booking);
    displayClassrooms(date);
    displayBookings();
    
    alert(`Successfully booked ${classroom.name} for Period ${period} on ${date}`);
}

// Display Bookings
function displayBookings() {
    bookingsList.innerHTML = '';
    
    if (bookings.length === 0) {
        bookingsList.innerHTML = '<p>No bookings found</p>';
        return;
    }
    
    bookings.forEach(booking => {
        const card = document.createElement('div');
        card.className = 'booking-card';
        
        card.innerHTML = `
            <div class="booking-info">
                <h3>${booking.roomName}</h3>
                <p>Date: ${booking.date}</p>
                <p>Period: ${booking.period}</p>
            </div>
            <button class="cancel-btn" data-booking-id="${booking.id}">Cancel Booking</button>
        `;
        
        bookingsList.appendChild(card);
    });
    
    // Add event listeners to cancel buttons
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', handleCancel);
    });
}

// Handle booking cancellation
function handleCancel(e) {
    const bookingId = parseInt(e.target.dataset.bookingId);
    bookings = bookings.filter(booking => booking.id !== bookingId);
    
    displayBookings();
    displayClassrooms(datePicker.value);
    
    alert('Booking cancelled successfully');
}

// Initialize date picker with today's date
datePicker.value = today;

// Initial display
displayClassrooms(today);
displayBookings();

// Update classrooms when date changes
datePicker.addEventListener('change', (e) => {
    displayClassrooms(e.target.value);
});