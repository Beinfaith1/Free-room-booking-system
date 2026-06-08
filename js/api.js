const API_BASE_URL = 'http://localhost/classroom-management/api';

// Authentication functions
async function register(name, email, password) {
    try {
        const response = await fetch(`${API_BASE_URL}/auth.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'register',
                name,
                email,
                password
            })
        });
        return await response.json();
    } catch (error) {
        console.error('Registration error:', error);
        throw error;
    }
}

async function login(email, password) {
    try {
        const response = await fetch(`${API_BASE_URL}/auth.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'login',
                email: email,
                password: password
            })
        });
        
        const data = await response.json();
        if (data.success) {
            // Store user data in localStorage
            setUserSession(data.user);
            // Redirect based on user role
            if (data.user.is_admin) {
                window.location.href = 'admin.html';
            } else {
                window.location.href = 'dashboard.html';
            }
            return data;
        } else {
            throw new Error(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
}

// Booking functions
async function getAvailableClassrooms(date) {
    try {
        const response = await fetch(`${API_BASE_URL}/bookings.php?date=${date}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching classrooms:', error);
        throw error;
    }
}

async function getUserBookings(userId) {
    try {
        const response = await fetch(`${API_BASE_URL}/bookings.php?user_id=${userId}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching bookings:', error);
        throw error;
    }
}

async function createBooking(userId, classroomId, date, period) {
    try {
        const response = await fetch(`${API_BASE_URL}/bookings.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userId,
                classroom_id: classroomId,
                date,
                period
            })
        });
        return await response.json();
    } catch (error) {
        console.error('Error creating booking:', error);
        throw error;
    }
}

async function deleteBooking(bookingId) {
    try {
        const response = await fetch(`${API_BASE_URL}/bookings.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                booking_id: bookingId
            })
        });
        return await response.json();
    } catch (error) {
        console.error('Error deleting booking:', error);
        throw error;
    }
}

// Session management
function setUserSession(userData) {
    localStorage.setItem('user', JSON.stringify(userData));
}

function getUserSession() {
    const userData = localStorage.getItem('user');
    return userData ? JSON.parse(userData) : null;
}

function clearUserSession() {
    localStorage.removeItem('user');
}

// Export functions
window.api = {
    register,
    login,
    getAvailableClassrooms,
    getUserBookings,
    createBooking,
    deleteBooking,
    setUserSession,
    getUserSession,
    clearUserSession
};