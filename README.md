# Modding Panel

A web-based modding panel with login authentication system designed for x10hosting.

## Features

- Secure login system with password hashing
- User dashboard
- Mod management (search, upload, manage)
- Session-based authentication
- Responsive design

## Installation on x10hosting

1. **Upload Files**: Upload all files to your x10hosting public_html directory

2. **Create Database**:
   - Go to x10hosting cPanel
   - Navigate to MySQL Databases
   - Create a new database named `modding_panel`
   - Create a database user and assign it to the database
   - Import the `database.sql` file using phpMyAdmin

3. **Configure Database Connection**:
   - Edit `config.php`
   - Update DB_HOST, DB_USER, DB_PASS, and DB_NAME with your x10hosting database credentials
   - Update SITE_URL with your x10hosting domain

4. **Default Login Credentials**:
   - Username: `admin`
   - Password: `admin123`
   - **IMPORTANT**: Change this password immediately after first login!

## Security Notes

- Change the default admin password immediately
- Keep config.php secure (don't commit with real credentials)
- Use HTTPS if available on your hosting
- Regularly update passwords

## File Structure

```
modding-panel/
├── index.php          # Login page
├── login.php          # Login authentication handler
├── dashboard.php      # Main dashboard
├── logout.php         # Logout handler
├── config.php         # Database configuration
├── database.sql       # Database schema
├── css/
│   └── style.css      # Stylesheet
└── README.md          # This file
```

## Technologies Used

- PHP 7.4+
- MySQL
- HTML5/CSS3
- Session-based authentication

## License

Open source - feel free to modify and use for your projects.