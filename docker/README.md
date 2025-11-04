# Docker Setup for DailyDose Application

This guide will help you set up and run the DailyDose application using Docker.

## Prerequisites

- Docker Desktop installed and running (for Mac/Windows)
- Docker and Docker Compose installed (for Linux)
- At least 2GB of free disk space
- Ports 8080, 8081, and 3306 available on your system

## Quick Start

1. **Clone or navigate to the project directory**
   ```bash
   cd /path/to/dailydose.qadi-tech.com
   ```

2. **Copy configuration files**
   ```bash
   cp application/config/config.example.php application/config/config.php
   cp application/config/database.example.php application/config/database.php
   ```

3. **Update database configuration**
   Edit `application/config/database.php` with the following values:
   ```php
   'hostname' => 'db',  // Use 'db' for Docker, 'localhost' for local
   'username' => 'dailydose_user',
   'password' => 'dailydose_password',
   'database' => 'dailydose_db',
   ```

4. **Update base URL in config.php**
   Edit `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost:8080/';
   ```

5. **Start the containers**
   ```bash
   docker-compose up -d
   ```

6. **Wait for MySQL to be ready** (about 30 seconds)
   ```bash
   docker-compose logs db
   ```

7. **Access the application**
   - Web Application: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

## Database Setup

### Option 1: Using phpMyAdmin

1. Go to http://localhost:8081
2. Login with:
   - Username: `root`
   - Password: `rootpassword123`
3. Import your database schema or run migrations

### Option 2: Using MySQL Command Line

```bash
docker exec -it dailydose-db mysql -u root -prootpassword123 dailydose_db
```

### Option 3: Using Docker Exec

```bash
docker exec -it dailydose-db bash
mysql -u root -prootpassword123 dailydose_db < /path/to/your/database.sql
```

## Configuration

### Environment Variables

You can modify the following in `docker-compose.yml`:

- **Database Password**: Change `MYSQL_ROOT_PASSWORD` and `MYSQL_PASSWORD`
- **Database Name**: Change `MYSQL_DATABASE`
- **Database User**: Change `MYSQL_USER`
- **Application Port**: Change `8080:80` to `YOUR_PORT:80`
- **phpMyAdmin Port**: Change `8081:80` to `YOUR_PORT:80`

### File Permissions

If you encounter permission issues with uploads:

```bash
docker exec -it dailydose-web chown -R www-data:www-data /var/www/html/uploads
docker exec -it dailydose-web chmod -R 775 /var/www/html/uploads
```

## Useful Commands

### View logs
```bash
# All services
docker-compose logs

# Specific service
docker-compose logs web
docker-compose logs db
```

### Stop containers
```bash
docker-compose stop
```

### Start containers
```bash
docker-compose start
```

### Restart containers
```bash
docker-compose restart
```

### Stop and remove containers
```bash
docker-compose down
```

### Stop and remove containers + volumes (⚠️ This will delete your database!)
```bash
docker-compose down -v
```

### Rebuild containers
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Access container shell
```bash
# Web container
docker exec -it dailydose-web bash

# Database container
docker exec -it dailydose-db bash
```

### Check container status
```bash
docker-compose ps
```

## Troubleshooting

### Port already in use
If port 8080, 8081, or 3306 is already in use:

1. Edit `docker-compose.yml`
2. Change the port mappings:
   ```yaml
   ports:
     - "YOUR_PORT:80"  # Instead of 8080:80
   ```

### Database connection issues
1. Ensure the database container is running: `docker-compose ps`
2. Check database logs: `docker-compose logs db`
3. Verify database credentials in `application/config/database.php`
4. Make sure you're using `db` as hostname (not `localhost`) in Docker

### Application not loading
1. Check web container logs: `docker-compose logs web`
2. Verify file permissions:
   ```bash
   docker exec -it dailydose-web chown -R www-data:www-data /var/www/html
   ```
3. Check if Apache is running:
   ```bash
   docker exec -it dailydose-web service apache2 status
   ```

### Permission denied errors
```bash
docker exec -it dailydose-web chmod -R 775 /var/www/html/uploads
docker exec -it dailydose-web chmod -R 775 /var/www/html/application/cache
docker exec -it dailydose-web chmod -R 775 /var/www/html/application/logs
```

## Database Credentials

Default credentials (change in docker-compose.yml for production):

- **Root User**: `root`
- **Root Password**: `rootpassword123`
- **Database Name**: `dailydose_db`
- **Database User**: `dailydose_user`
- **Database Password**: `dailydose_password`

## Production Considerations

⚠️ **Do NOT use this setup in production without:**

1. Changing all default passwords
2. Setting up proper SSL/TLS certificates
3. Configuring proper firewall rules
4. Using environment variables for sensitive data
5. Setting up proper backup strategies
6. Using a production-ready web server configuration
7. Setting appropriate PHP settings (memory_limit, upload_max_filesize, etc.)

## Support

For issues related to:
- Docker setup: Check Docker logs and documentation
- Application issues: Refer to the main README.md
- Database issues: Check MySQL logs and configuration

