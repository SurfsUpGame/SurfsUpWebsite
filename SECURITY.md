# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability within this project, please follow these steps:

1. **DO NOT** create a public GitHub issue.
2. Send an email to surfsupgame@protonmail.me with:
   - A description of the vulnerability
   - Steps to reproduce the issue
   - Potential impact
   - Any suggested fixes (optional)

We take security seriously and will respond to your report within 48 hours.

## Security Best Practices

When contributing to this project, please ensure:

### API Keys and Secrets
- Never commit API keys, passwords, or other secrets to the repository
- Always use environment variables for sensitive configuration
- Check `.env.example` for required environment variables
- Use strong, unique values for all secrets

### Database Security
- Never use default passwords in production
- Always use parameterized queries to prevent SQL injection
- Keep database credentials in environment variables

### Authentication
- This project uses Steam OAuth for authentication
- Admin privileges are controlled via Steam ID in the environment configuration
- Never hardcode user credentials

### Dependencies
- Regularly update dependencies to patch security vulnerabilities
- Run `composer audit` to check for known vulnerabilities
- Run `npm audit` for JavaScript dependencies

## Environment Configuration

Before deploying:

1. Generate a new Laravel application key:
   ```bash
   php artisan key:generate
   ```

2. Set up all required API keys:
   - Steam API keys from https://steamcommunity.com/dev/apikey
   - Twitch credentials from https://dev.twitch.tv/console/apps
   - Discord webhook URL from your Discord server settings

3. Configure admin access:
   - Set `ADMIN_STEAM_ID` to your Steam ID (64-bit format)
   - Find your Steam ID at https://steamid.io/

## Security Checklist

Before going live:

- [ ] All API keys are unique and not the default values
- [ ] Database password is strong and unique
- [ ] Laravel APP_KEY has been regenerated
- [ ] Admin Steam ID is properly configured
- [ ] HTTPS is enabled in production
- [ ] Debug mode is disabled in production (APP_DEBUG=false)
- [ ] Error reporting doesn't expose sensitive information
- [ ] File permissions are properly set
- [ ] `.env` file is not accessible via web
- [ ] Git history has been cleaned of any secrets

## Vulnerability Disclosure Timeline

- **0-48 hours**: Initial response to report
- **48-72 hours**: Vulnerability assessment and validation
- **3-7 days**: Fix development and testing
- **7-14 days**: Fix deployment and notification

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Additional Resources

- [Laravel Security Documentation](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Steam Web API Documentation](https://partner.steamgames.com/doc/webapi_overview)
