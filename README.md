
# PHP-Bin

A modern PHP-based web application that allows you to store and share text online for a set period of time.

## Features

- Store text online for a set period of time
- Syntax highlighting for multiple programming languages
- Line numbers for debugging purposes
- Download to text file capability
- Share links on social media platforms
- Quick print functionality
- Report abuse system
- Live recent post sidebar
- Cron-job to prune expired posts
- User registration and profile management
- Public and private paste options
- Search by syntax and content

## Requirements

- PHP >= 7.4
- MySQL >= 5.7 or MariaDB >= 10.3
- Modern web browser with JavaScript enabled
- Cron-Job or Scheduled Task for automatic pruning

## Installation

1. Create a database and import the database tables located in `/install/sql`
2. Edit `/include/config.php` with your database settings and site preferences
3. Edit `.htaccess` file if necessary for your server configuration
4. Set up a cron job to run `/include/cronjob.php` at regular intervals
5. Ensure proper file permissions

## Alternative to Cron Jobs

If your hosting service does not offer cron-jobs/scheduled tasks, you can include the cronjob script on different pages:

```php
include('include/cronjob.php');
```

This will run the pruning script when users visit pages where it's included, though it's less efficient than a proper cron job.

## Bug Tracking / Reporting Bugs

Please feel free to report any bugs by creating an issue in the GitHub repository or via email at [administrator@example.com](mailto:administrator@example.com).

When reporting bugs, please provide:
- Detailed description of the issue
- Steps to reproduce
- Screenshots if possible
- Browser and PHP version information

## Security

This application implements several security measures:
- CSRF protection on forms
- Input validation and sanitization
- Secure password hashing
- XSS prevention
- HTTPS encouraged for all resources

## Credits

PHP-Bin is maintained by Jeremy Stevens.
Copyright 2014-2023 Jeremy Stevens.
Licensed under GPL 2 (http://www.gnu.org/licenses/gpl.html)

## Current Version: 2.0.0

See CHANGELOG.md for the complete version history.
