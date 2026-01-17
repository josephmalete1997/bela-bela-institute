# Bela-Bela Institute Management System

A comprehensive PHP-based educational institute management system for course management, student enrollment, task tracking, and blog functionality.

## Features

- **User Management**: Student and admin authentication with role-based access control
- **Course Management**: Create and manage courses with intakes and enrollments
- **Task System**: Assign and track tasks with review workflow
- **Application System**: Handle student applications and approvals
- **Blog/Articles**: Content management system for articles
- **Notifications**: In-app notification system
- **Payment Tracking**: Track student payments
- **Student Portal**: Dedicated portal for students to view tasks and submit work

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Apache/Nginx web server
- PDO extension enabled
- mbstring extension enabled
- JSON extension enabled

## Installation

1. **Clone or download the project**
   ```bash
   cd /path/to/your/webroot
   git clone <repository-url> bela-bela-institute
   ```

2. **Install dependencies (optional - for development)**
   ```bash
   composer install
   ```

3. **Configure database**
   - Create a MySQL database
   - Import the SQL schema files from the `sql/` directory
   - Run `sql/schema.sql` for the main schema
   - Run `sql/blog_schema.sql` if you need the blog functionality

4. **Configure environment**
   - Copy `.env.example` to `.env` (if available) or configure `app/config.php` directly
   - Update database credentials in `app/config.php`:
     ```php
     "db" => [
       "host" => "localhost",
       "name" => "your_database_name",
       "user" => "your_username",
       "pass" => "your_password",
       "charset" => "utf8mb4",
     ],
     ```

5. **Set permissions**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 public/uploads/
   chmod -R 755 student/avatars/
   chmod -R 755 logs/
   ```

6. **Configure web server**
   - Point your web server document root to the project directory
   - Ensure mod_rewrite is enabled (for Apache)
   - Configure virtual host if needed

## Configuration

### Environment Variables

The system supports environment variables via `.env` file. Key settings:

- `DB_HOST`: Database host (default: localhost)
- `DB_NAME`: Database name
- `DB_USER`: Database username
- `DB_PASS`: Database password
- `APP_ENV`: Environment (production/development)
- `APP_DEBUG`: Enable debug mode (true/false)
- `APP_BASE_URL`: Base URL for the application
- `SESSION_LIFETIME`: Session lifetime in seconds (default: 7200)

### Security Settings

Configured in `app/config.php`:

- `password_min_length`: Minimum password length (default: 8)
- `rate_limit_login`: Max login attempts per window (default: 5)
- `rate_limit_window`: Rate limit window in seconds (default: 900)

## Database Schema

The system uses the following main tables:

- `users`: User accounts (students, admins)
- `courses`: Course definitions
- `intakes`: Course intake periods
- `applications`: Student applications
- `enrollments`: Student enrollments
- `tasks`: Course tasks
- `task_reviews`: Task review submissions
- `notifications`: User notifications
- `articles`: Blog articles
- `payments`: Payment records

See `sql/` directory for complete schema.

## Usage

### Admin Access

1. Navigate to `/public/login.php`
2. Login with admin credentials
3. Access admin dashboard at `/admin/dashboard.php`

### Student Access

1. Register at `/public/register.php`
2. Login at `/public/login.php`
3. Access student portal at `/student/index.php`

### Password Reset

- Students can reset passwords via `/public/forgot_password.php`
- Reset tokens expire after 1 hour

## Security Features

- **CSRF Protection**: All forms use CSRF tokens
- **Password Hashing**: Uses PHP `password_hash()` with bcrypt
- **Rate Limiting**: Login attempts are rate-limited
- **Input Validation**: All user inputs are validated and sanitized
- **SQL Injection Prevention**: Uses prepared statements throughout
- **XSS Prevention**: Output is escaped using `htmlspecialchars()`
- **Session Security**: Secure session configuration with HttpOnly cookies

## File Structure

```
bela-bela-institute/
├── admin/              # Admin panel pages
│   ├── api/           # Admin API endpoints
│   └── layout/        # Admin layout templates
├── app/               # Core application files
│   ├── auth.php       # Authentication functions
│   ├── bootstrap.php  # Application bootstrap
│   ├── config.php     # Configuration
│   ├── csrf.php       # CSRF protection
│   ├── db.php         # Database connection
│   ├── helpers.php    # Helper functions
│   └── middleware.php # Middleware functions
├── api/               # Public API endpoints
├── includes/          # Shared includes
├── public/            # Public pages (login, register, etc.)
├── student/           # Student portal
├── sql/               # Database schema files
├── uploads/           # File uploads
└── logs/              # Application logs
```

## Development

### Code Style

- Use `declare(strict_types=1);` at the top of PHP files
- Follow PSR-12 coding standards
- Use prepared statements for all database queries
- Escape all output using `e()` helper function

### Logging

Logs are stored in `logs/` directory:
- `error_YYYY-MM-DD.log`: Error logs
- `info_YYYY-MM-DD.log`: Info logs
- `php_errors.log`: PHP error log (when debug is off)

### Testing

1. Set `APP_DEBUG=true` in config for development
2. Check logs in `logs/` directory
3. Monitor error logs for issues

## Troubleshooting

### Database Connection Issues

- Verify database credentials in `app/config.php`
- Ensure database exists and user has proper permissions
- Check PHP PDO extension is enabled

### Session Issues

- Ensure `logs/` directory is writable
- Check session save path permissions
- Verify session configuration in `app/bootstrap.php`

### File Upload Issues

- Check directory permissions on `uploads/`
- Verify PHP `upload_max_filesize` and `post_max_size` settings
- Ensure `uploads/` directories exist

## Contributing

1. Follow the existing code style
2. Use prepared statements for database queries
3. Validate and sanitize all user inputs
4. Add appropriate error handling
5. Update documentation as needed

## License

MIT License - See LICENSE file for details

## Support

For issues and questions, please contact the development team or create an issue in the repository.

## Changelog

### Recent Improvements

- Added environment variable support (.env)
- Implemented password reset functionality
- Added rate limiting for login attempts
- Enhanced input validation and sanitization
- Improved session security
- Added comprehensive logging system
- Enhanced password strength requirements
- Added proper error handling
