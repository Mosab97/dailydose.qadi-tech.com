# Docker Setup Guide for DailyDose Application

This guide will walk you through setting up and running the DailyDose application using Docker.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Step 1: Verify Docker Installation](#step-1-verify-docker-installation)
3. [Step 2: Prepare Configuration Files](#step-2-prepare-configuration-files)
4. [Step 3: Update Database Configuration](#step-3-update-database-configuration)
5. [Step 4: Update Application Configuration](#step-4-update-application-configuration)
6. [Step 5: Start Docker Containers](#step-5-start-docker-containers)
7. [Step 6: Import Database](#step-6-import-database)
8. [Step 7: Access the Application](#step-7-access-the-application)
9. [Troubleshooting](#troubleshooting)
10. [Useful Commands](#useful-commands)

---

## Prerequisites

Before starting, ensure you have the following installed:

- **Docker Desktop** (for Mac/Windows) or **Docker Engine** (for Linux)
- **Docker Compose** (usually included with Docker Desktop)
- At least **2GB of free disk space**
- Ports available: **9000** (web), **3307** (MySQL), **9080** (phpMyAdmin)

### Check Docker Installation

Verify Docker is installed and running:

```bash
docker --version
docker-compose --version
```

---

## Step 1: Verify Docker Installation

Make sure Docker is running:

```bash
docker ps
```

If you see an error, start Docker Desktop or your Docker service.

---

## Step 2: Prepare Configuration Files

Navigate to your project directory:

```bash
cd /path/to/dailydose.qadi-tech.com
```

Copy the example configuration files (if they don't exist):

```bash
cp application/config/config.example.php application/config/config.php
cp application/config/database.example.php application/config/database.php
```

---

## Step 3: Update Database Configuration

Edit `application/config/database.php` and update the following values:

```php
$db['default'] = array(
    'hostname' => 'db',  // Use 'db' for Docker (service name)
    'username' => 'dailydose_user',
    'password' => 'dailydose_password',
    'database' => 'dailydose_db',
    'dbdriver' => 'mysqli',
    // ... rest of the configuration
);
```

**Important Notes:**
- Use `'db'` as the hostname (not `'localhost'`), as this is the Docker service name
- The credentials match what's defined in `docker-compose.yml`

---

## Step 4: Update Application Configuration

Edit `application/config/config.php` and update the base URL:

```php
$config['base_url'] = 'http://localhost:9000/';
```

**Note:** If port 9000 is in use, you can change it in `docker-compose.yml` under the `web` service ports section.

---

## Step 5: Start Docker Containers

### Option A: Start all services at once

```bash
docker-compose up -d
```

This will:
- Build the PHP/Apache container (first time only)
- Pull MySQL and phpMyAdmin images
- Create and start all containers

### Option B: Start services individually

```bash
# Build and start web container
docker-compose up -d web

# Start database container
docker-compose up -d db

# Start phpMyAdmin container
docker-compose up -d phpmyadmin
```

### Wait for MySQL to Initialize

After starting, wait about 30-60 seconds for MySQL to be ready. Check the logs:

```bash
docker-compose logs db
```

Look for: `MySQL init process done. Ready for start up.`

### Verify Containers are Running

```bash
docker-compose ps
```

You should see all three containers with status "Up":
- `dailydose-web`
- `dailydose-db`
- `dailydose-phpmyadmin`

---

## Step 6: Import Database

### Method 1: Using Docker Exec (Recommended)

Navigate to your project directory and run:

```bash
docker exec -i dailydose-db mysql -u root -prootpassword123 dailydose_db < "eRestro Single Vendor blank database - v1.1.2.sql"
```

**Note:** Replace `"eRestro Single Vendor blank database - v1.1.2.sql"` with your actual SQL file path.

### Method 2: Using phpMyAdmin

1. Open phpMyAdmin: http://localhost:9080
2. Login with:
   - **Username:** `root`
   - **Password:** `rootpassword123`
3. Select database `dailydose_db` from the left sidebar
4. Click on the **Import** tab
5. Choose your SQL file (`eRestro Single Vendor blank database - v1.1.2.sql`)
6. Click **Go** to import

### Method 3: Copy SQL file into container

```bash
# Copy SQL file into container
docker cp "eRestro Single Vendor blank database - v1.1.2.sql" dailydose-db:/tmp/database.sql

# Import from inside container
docker exec dailydose-db mysql -u root -prootpassword123 dailydose_db -e "source /tmp/database.sql"

# Clean up
docker exec dailydose-db rm /tmp/database.sql
```

### Verify Database Import

Check if tables were created:

```bash
docker exec -i dailydose-db mysql -u root -prootpassword123 dailydose_db -e "SHOW TABLES;"
```

You should see a list of tables like: `addresses`, `categories`, `products`, `orders`, etc.

---

## Step 7: Access the Application

### Web Application

Open your browser and navigate to:

**http://localhost:9000**

### phpMyAdmin (Database Management)

Open your browser and navigate to:

**http://localhost:9080**

Login credentials:
- **Username:** `root`
- **Password:** `rootpassword123`

### Port Configuration

If the default ports are in use, you can change them in `docker-compose.yml`:

```yaml
services:
  web:
    ports:
      - "YOUR_PORT:80"  # Change 9000 to your preferred port
  
  db:
    ports:
      - "YOUR_PORT:3306"  # Change 3307 to your preferred port
  
  phpmyadmin:
    ports:
      - "YOUR_PORT:80"  # Change 9080 to your preferred port
```

After changing ports, restart containers:

```bash
docker-compose down
docker-compose up -d
```

**Don't forget to update `config.php` base_url if you change the web port!**

---

## Troubleshooting

### Port Already in Use

**Error:** `Bind for 0.0.0.0:XXXX failed: port is already allocated`

**Solution:** 
1. Find what's using the port:
   ```bash
   # On Mac/Linux
   lsof -i :9000
   
   # On Windows
   netstat -ano | findstr :9000
   ```
2. Either stop the service using that port, or change the port in `docker-compose.yml`

### Database Connection Errors

**Error:** `Unable to connect to database`

**Solution:**
1. Ensure database container is running:
   ```bash
   docker-compose ps
   ```
2. Check database logs:
   ```bash
   docker-compose logs db
   ```
3. Verify database configuration in `application/config/database.php` uses `'db'` as hostname (not `'localhost'`)
4. Wait a few more seconds if MySQL just started (it needs time to initialize)

### Session Directory Errors

**Error:** `mkdir(): Invalid path` or `Failed to initialize storage module`

**Solution:**
The sessions directory should be created automatically, but if you encounter issues:

```bash
docker exec dailydose-web bash -c "mkdir -p /var/www/html/application/sessions && chown -R www-data:www-data /var/www/html/application/sessions && chmod -R 775 /var/www/html/application/sessions"
```

### Permission Errors

**Error:** `Permission denied` when uploading files

**Solution:**
Fix permissions for uploads directory:

```bash
docker exec dailydose-web bash -c "chown -R www-data:www-data /var/www/html/uploads && chmod -R 775 /var/www/html/uploads"
```

### Containers Not Starting

**Solution:**
1. Check Docker is running:
   ```bash
   docker ps
   ```
2. View detailed logs:
   ```bash
   docker-compose logs
   ```
3. Rebuild containers:
   ```bash
   docker-compose down
   docker-compose build --no-cache
   docker-compose up -d
   ```

### Database Not Found

**Error:** `Unknown database 'dailydose_db'`

**Solution:**
The database should be created automatically, but you can create it manually:

```bash
docker exec -i dailydose-db mysql -u root -prootpassword123 -e "CREATE DATABASE IF NOT EXISTS dailydose_db;"
```

---

## Useful Commands

### Container Management

```bash
# View running containers
docker-compose ps

# View all containers (including stopped)
docker-compose ps -a

# Start containers
docker-compose start

# Stop containers
docker-compose stop

# Stop and remove containers
docker-compose down

# Stop and remove containers + volumes (⚠️ DELETES DATABASE!)
docker-compose down -v

# Restart containers
docker-compose restart

# Rebuild containers
docker-compose build
docker-compose up -d
```

### Viewing Logs

```bash
# All services
docker-compose logs

# Specific service
docker-compose logs web
docker-compose logs db
docker-compose logs phpmyadmin

# Follow logs (real-time)
docker-compose logs -f web
```

### Accessing Containers

```bash
# Access web container shell
docker exec -it dailydose-web bash

# Access database container shell
docker exec -it dailydose-db bash

# Access MySQL command line
docker exec -it dailydose-db mysql -u root -prootpassword123 dailydose_db
```

### Database Operations

```bash
# Execute SQL command
docker exec -i dailydose-db mysql -u root -prootpassword123 dailydose_db -e "YOUR_SQL_COMMAND"

# Export database
docker exec dailydose-db mysqldump -u root -prootpassword123 dailydose_db > backup.sql

# Import database (from file)
docker exec -i dailydose-db mysql -u root -prootpassword123 dailydose_db < database.sql

# List all databases
docker exec -i dailydose-db mysql -u root -prootpassword123 -e "SHOW DATABASES;"

# List all tables
docker exec -i dailydose-db mysql -u root -prootpassword123 dailydose_db -e "SHOW TABLES;"
```

### File Permissions

```bash
# Fix uploads directory permissions
docker exec dailydose-web bash -c "chown -R www-data:www-data /var/www/html/uploads && chmod -R 775 /var/www/html/uploads"

# Fix cache directory permissions
docker exec dailydose-web bash -c "chown -R www-data:www-data /var/www/html/application/cache && chmod -R 775 /var/www/html/application/cache"

# Fix logs directory permissions
docker exec dailydose-web bash -c "chown -R www-data:www-data /var/www/html/application/logs && chmod -R 775 /var/www/html/application/logs"

# Fix sessions directory permissions
docker exec dailydose-web bash -c "chown -R www-data:www-data /var/www/html/application/sessions && chmod -R 775 /var/www/html/application/sessions"
```

---

## Default Credentials

### Database

- **Host:** `db` (from within containers) or `localhost:3307` (from host machine)
- **Root Username:** `root`
- **Root Password:** `rootpassword123`
- **Database Name:** `dailydose_db`
- **Database User:** `dailydose_user`
- **Database Password:** `dailydose_password`

### phpMyAdmin

- **URL:** http://localhost:9080
- **Username:** `root`
- **Password:** `rootpassword123`

---

## Production Considerations

⚠️ **WARNING:** This setup is for **development only**. For production:

1. **Change all default passwords**
2. **Use environment variables** for sensitive data
3. **Set up SSL/TLS certificates** (HTTPS)
4. **Configure proper firewall rules**
5. **Set up regular database backups**
6. **Use production-ready PHP settings** (memory_limit, max_execution_time, etc.)
7. **Configure proper error logging** (disable display_errors)
8. **Use a reverse proxy** (nginx) in front of Apache
9. **Set up monitoring and alerting**
10. **Use Docker secrets** for sensitive information

---

## Summary

Quick setup checklist:

- [ ] Docker installed and running
- [ ] Configuration files copied and updated
- [ ] Database hostname set to `'db'` in `database.php`
- [ ] Base URL updated in `config.php`
- [ ] Containers started with `docker-compose up -d`
- [ ] MySQL initialized (waited 30-60 seconds)
- [ ] Database imported successfully
- [ ] Application accessible at http://localhost:9000
- [ ] phpMyAdmin accessible at http://localhost:9080

---

## Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [CodeIgniter Documentation](https://codeigniter.com/user_guide/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

---

**Need Help?** Check the logs first: `docker-compose logs` or refer to the troubleshooting section above.
