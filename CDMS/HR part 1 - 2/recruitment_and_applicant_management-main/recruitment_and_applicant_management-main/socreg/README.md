# SOCREG - Social Recognition System

A comprehensive HR Management System built with PHP and MySQL, focusing on employee recognition and engagement.

## System Overview

SOCREG is designed to streamline employee recognition, awards management, and HR processes. The system provides a centralized platform for managing employee achievements, department activities, and recognition programs.

## Main Components

### 1. Recognition Management
- **Dashboard** (`recognition-dashboard.php`)
  - Real-time recognition overview
  - Activity statistics
  - Recent recognitions

- **Awards System** (`awards.php`)
  - Award creation and management
  - Recipient tracking
  - Award history

- **Categories** (`categories.php`)
  - Recognition type management
  - Custom categories
  - Category analytics

- **Recognition Tracking** (`recognitions.php`)
  - Employee recognition records
  - Recognition history
  - Performance metrics

### 2. Department Management
- **Account Management** (`user_management/department-accounts.php`)
  - User account control
  - Role management
  - Access permissions

- **Activity Logging** (`user_management/department-log-history.php`)
  - User activity tracking
  - Action history
  - Department logs

- **Audit System** (`user_management/department-audit-trail.php`)
  - System change tracking
  - Security audits
  - Compliance monitoring

- **Transaction Records** (`user_management/department-transaction.php`)
  - Department operations
  - Transaction history
  - Activity monitoring

### 3. Core System Features
- **Authentication** (`login.php`, `logout.php`)
  - Secure login system
  - Session management
  - Access control

- **Notifications** (`notifications.php`)
  - Real-time alerts
  - User notifications
  - System updates

- **Error Management** (`error.php`)
  - Error handling
  - User feedback
  - System logging

## Technical Implementation

### Server Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- XAMPP (development)

### Database Setup
1. Create database in phpMyAdmin
2. Import schema
3. Configure `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'socreg');
```

### Installation Steps
1. Install XAMPP
2. Place project in `htdocs/SocReg/`
3. Start Apache and MySQL
4. Configure database
5. Access via `http://localhost/SocReg/`

## Security Implementation

### Authentication
- Password hashing
- Session management
- Access control

### Data Protection
- SQL injection prevention
- XSS protection
- Input validation
- CSRF protection

### Monitoring
- Activity logging
- Audit trails
- Error tracking

## User Interface

### Responsive Design
- Mobile-first approach
- Adaptive layouts
- Touch-friendly interface

### Navigation
- Collapsible sidebar
- Mobile menu
- Quick access

### Data Display
- Responsive tables
- Mobile-optimized views
- Dynamic content

## Development Guidelines

### Code Standards
- PSR-12 compliance
- Clean code practices
- Proper documentation

### File Structure
```
SOCREG/
├── includes/           # Core utilities
├── user_management/    # Department modules
├── config.php         # Configuration
├── login.php          # Authentication
├── finaltemplate.php  # Main template
└── [feature files]    # Module files
```

### Best Practices
- Regular backups
- Code documentation
- Version control
- Testing procedures

## Support and Maintenance

### Documentation
- User guides
- API documentation
- System manuals

### Updates
- Regular maintenance
- Security patches
- Feature updates

### Contact
- Technical support
- Bug reporting
- Feature requests

## License
MIT License - See LICENSE file for details 