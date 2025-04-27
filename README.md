# Calendar Application

A modern, responsive calendar application built with PHP, JavaScript, and Alpine.js. This project allows users to manage events with a clean, intuitive interface.

![Calendar App Screenshot](screenshot.png)

## Features

- **User Authentication**: Secure login and registration system
- **Event Management**: Create, read, update, and delete events
- **Week View**: Navigate through weeks with a clean interface
- **Responsive Design**: Works on desktop and mobile devices
- **Event Details**: View and edit event details including title, description, and time

## Technologies Used

- **Frontend**:
  - HTML5, CSS3, JavaScript
  - [Alpine.js](https://alpinejs.dev/) - Lightweight JavaScript framework
  - [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework

- **Backend**:
  - PHP 8.0+
  - MySQL/MariaDB
  - RESTful API architecture

## Project Structure

```
calendar/
│   ├── database/           # Database migrations and models
│   ├── public/             # entrypoint of application
│   ├── src                 # Buisiness Logic
│   ├── vendor              # Composer modules
├── frontend/               # Frontend code
│   ├── css/                # CSS styles
│   ├── js/                 # JavaScript files
│   ├── index.html          # Main calendar page
│   └── login.html          # Login page
└── README.md               # Project documentation
```

## Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL/MariaDB
- Web server (Apache, Nginx, etc.)

### Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/calendar.git
   cd calendar
   ```

2. Set up the database:
   ```bash
   php backend/database/setup.php (Will setup db initially)
   [ optional ] php backend/database/test.php  (Will create test user)
   
   # Import the database schema
   mysql -u root -p calendar < backend/database/schema.sql
   ```

3. Configure the database connection:
    ```bash
    cd backend
    cp .env.example .env
    ```
   - Open `backend/.env`
   - Update the database credentials
   - Set JWT Token

4. Start the PHP development server:
   ```bash
   cd backend
   php -S localhost:8000
   ```

5. Open the application in your browser:
   ```
   http://localhost:8000/frontend/index.html
   ```

## Usage

1. **Login**: Use your credentials to log in to the application
2. **View Calendar**: Navigate through weeks using the Previous/Next buttons
3. **Create Event**: Click on a day to create a new event
4. **Edit Event**: Click on an existing event to edit its details
5. **Delete Event**: Open an event and click the Delete button

## API Endpoints

- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/events` - Get all events
- `POST /api/events` - Create a new event
- `PUT /api/events/{id}` - Update an event
- `DELETE /api/events/{id}` - Delete an event

## Future Enhancements

- [ ] Event categories and color coding
- [ ] Recurring events
- [ ] Alexa and Siri features
- [ ] Email or other channel notifications
- [ ] Calendar sharing between users
- [ ] Admin Panel for Configuration Values

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- [Alpine.js](https://alpinejs.dev/) for the lightweight JavaScript framework
- [Tailwind CSS](https://tailwindcss.com/) for the utility-first CSS framework
- [Heroicons](https://heroicons.com/) for the beautiful icons