# The Loop - Weekly Objective Tracker

![The Loop Screenshot](screenshot.png)

A cyberpunk-themed weekly objective tracker with a clean, modern UI and smooth user experience.

## Features

- **User Authentication**: Login and signup system
- **Weekly Objectives**: Track objectives by day of the week
- **Progress Tracking**: Visual progress indicators
- **Responsive Design**: Works on all device sizes
- **Cyberpunk Theme**: Neon pink/cyan aesthetic with smooth animations

## Technologies

- PHP 8+
- MySQL
- Tailwind CSS
- Vanilla JavaScript

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/the-loop.git
   ```

2. Set up the database:
   - Import `config/schema.sql` to your MySQL server
   - Update database credentials in `config/db_connect.php`

3. Start the PHP development server:
   ```bash
   php -S localhost:8000
   ```

4. Open in browser:
   ```
   http://localhost:8000
   ```

## Configuration

Edit `config/db_connect.php` with your database credentials:
```php
$host = 'your_database_host';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';
```

## Screenshots

- Login/Signup Page
- Dashboard with Weekly Overview
- Objective Management

## License

MIT License
