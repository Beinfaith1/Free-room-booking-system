# Free Room Management System

A full-stack web application that allows university students and staff to browse, reserve, and manage classroom bookings in real time. Built as a group project at Unity University.

---

## My Contributions

- **QA Lead** — wrote and executed test cases covering all core user flows (registration, login, booking, cancellation, admin access, conflict prevention)
- **Group Coordinator** — managed task distribution and progress across a 5-member team
- **Landing Page Design** — designed the UI layout and visual structure in Figma before development

---

## What This Project Does

This system replaces manual or paper-based classroom reservation with a simple web interface. Users can:

- Register and log in securely
- Browse available classrooms by date and period
- Book a classroom and view their upcoming reservations
- Cancel bookings from their personal dashboard

Admins get a separate panel to manage classrooms, view all bookings, and oversee users.

---

## Features

- **User authentication** — register, login, and role-based routing (student vs. admin)
- **Real-time availability** — classrooms shown with booked periods highlighted per date
- **Booking management** — create and cancel bookings with conflict prevention at the database level
- **Admin dashboard** — full oversight of rooms, bookings, and users
- **Responsive UI** — mobile-friendly layout built with Tailwind CSS

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML, Tailwind CSS, Vanilla JavaScript |
| Backend | PHP (REST API) |
| Database | MySQL |
| Design | Figma (landing page layout) |

---

## Project Structure

```
free-room-management-system/
├── index.html              # Landing page
├── login.html              # User login
├── signup.html             # User registration
├── dashboard.html          # Student booking dashboard
├── admin.html              # Admin management panel
├── signinadmin.html        # Admin login
├── dashboard.js            # Dashboard logic
├── styles.css              # Global styles
├── auth.css                # Auth page styles
├── dashboard.css           # Dashboard styles
├── js/
│   └── api.js              # All API calls (auth, bookings, session)
├── api/
│   ├── auth.php            # Register and login endpoints
│   ├── bookings.php        # Booking CRUD endpoints
│   ├── admin.php           # Admin-only endpoints
│   └── get.php             # General data fetching
├── config/
│   └── database.php        # PDO database connection
└── database.sql            # MySQL schema and sample data
```

---

## Database Schema

Three core tables:

- **users** — stores student and admin accounts with hashed passwords
- **classrooms** — stores room name and capacity
- **bookings** — links users to classrooms with date, period, and status; enforces uniqueness at the DB level to prevent double-booking

---

## How to Run Locally

**Requirements:** PHP 7.4+, MySQL, Apache (XAMPP or WAMP recommended)

1. Clone or download this repository into your server's `htdocs` folder (e.g. `C:/xampp/htdocs/free-room-management-system/`)
2. Import `database.sql` into MySQL via phpMyAdmin or CLI:
   ```bash
   mysql -u root -p < database.sql
   ```
3. Open `config/database.php` and update your DB credentials if needed
4. Start Apache and MySQL from XAMPP
5. Open your browser and go to: `http://localhost/free-room-management-system/`

**Default admin credentials:**
- Email: `admin@example.com`
- Password: `password`

---

## QA Test Cases

Test cases written and executed manually covering:

- User registration with valid and invalid inputs
- Duplicate email detection on signup
- Login with correct credentials → correct role redirect (student vs. admin)
- Login with wrong password and non-existent email
- Booking a classroom for an available date and period
- Attempting to double-book the same room and period (conflict prevention)
- Cancelling an active booking and verifying it disappears from dashboard
- Admin login and access to admin-only panel
- Blocking non-admin users from accessing admin routes
- Responsive layout verification on mobile and desktop viewports

---

## Known Limitations

- No email verification on registration
- Sessions are stored in `localStorage` (not secure for production)
- API endpoints have no token-based authentication — suitable for academic/local use only
- Hardcoded API base URL in `js/api.js` (set to `localhost`)

---

**Institution:** Unity University, Department of Computer Science  
**Date:** May 2025
