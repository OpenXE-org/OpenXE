# Server Installation Guide (Ubuntu 24.04 LTS / Ubuntu 26.04 LTS)

This guide walks you through setting up a clean Linux server for OpenXE on supported Ubuntu LTS releases (**Ubuntu 24.04 LTS** or **Ubuntu 26.04 LTS**).

> [!IMPORTANT]
> **PHP Lifecycle & Security Notice:**
> OpenXE requires a currently supported PHP runtime. According to the official [PHP Supported Versions Lifecycle](https://www.php.net/supported-versions.php), **PHP 8.1 is End-of-Life (EOL)** and no longer receives security fixes or bug updates.
> Even if your instance runs in an internal, non-public network, using an unsupported EOL PHP version represents a critical security risk.
> - **Ubuntu 24.04 LTS (Noble Numbat):** Ships with **PHP 8.3** (Supported)
> - **Ubuntu 26.04 LTS:** Ships with **PHP 8.5** (Supported)

---

## 1. System Requirements

* **Architecture:** x86_64 / amd64
* **CPU:** 2–4 CPU cores minimum
* **RAM:** 4 GB minimum (8 GB or more recommended for production workloads)
* **Storage:** 50 GB+ SSD / NVMe
* **Operating System:** Ubuntu Server 24.04 LTS or Ubuntu Server 26.04 LTS

*(During the Ubuntu Server installation, make sure to enable **OpenSSH server** for remote administration).*

---

## 2. Refresh Package Index & Upgrade

Update the package lists and upgrade existing system packages:

```bash
sudo apt-get update && sudo apt-get upgrade -y
```

---

## 3. Install Apache Web Server

Install Apache 2:

```bash
sudo apt-get install -y apache2
```

Enable standard Apache modules required by OpenXE (`rewrite`, `headers` and optionally `ssl`):

```bash
sudo a2enmod rewrite headers ssl
```

Verify that Apache is active:

```bash
sudo systemctl status apache2
```

Navigating to `http://<YOUR_SERVER_IP>` in your browser should now display the default "Apache2 Ubuntu Default Page".

---

## 4. Install PHP

Install the standard PHP meta-package and the Apache PHP integration module:

```bash
sudo apt-get install -y php libapache2-mod-php
```

Verify the active PHP version:

```bash
php -v
```

Expected output:
* On **Ubuntu 24.04 LTS**: `PHP 8.3.x (cli) ...`
* On **Ubuntu 26.04 LTS**: `PHP 8.5.x (cli) ...`

### Install Required PHP Extensions

Install the required modules for MySQL/MariaDB database access, cURL, XML/SOAP, multi-byte string handling, ZIP compression, and GD graphics:

```bash
sudo apt-get install -y \
  php-mysql \
  php-cli \
  php-curl \
  php-xml \
  php-soap \
  php-zip \
  php-mbstring \
  php-gd
```

---

## 5. Configure PHP Settings

OpenXE requires adjustments to upload limits, execution times, and memory limits.

Locate your `php.ini` files. Replace `<VERSION>` with `8.3` (for Ubuntu 24.04) or `8.5` (for Ubuntu 26.04):
* Apache module: `/etc/php/<VERSION>/apache2/php.ini`
* CLI interpreter: `/etc/php/<VERSION>/cli/php.ini`

*(Tip: You can dynamically target your active PHP version directory using `$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')`)*

Apply or verify the following configuration directives in both `php.ini` files:

```ini
; Function restrictions & hardening
disable_functions = pcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority,dl,highlight_file,show_source,proc_open,popen

; Request & Upload Limits
post_max_size = 100M
upload_max_filesize = 100M
file_uploads = Yes
max_file_uploads = 20

; Timeouts & Memory
max_execution_time = 3600
max_input_time = 3600
max_input_vars = 3000
memory_limit = 256M

; Scripting
short_open_tag = On
```

---

## 6. Install Auxiliary Tools

Install archive utilities and Git:

```bash
sudo apt-get install -y zip unzip git
```

---

## 7. Install and Secure MariaDB Server

Install MariaDB server:

```bash
sudo apt-get install -y mariadb-server
```

Run the security setup script:

```bash
sudo mariadb-secure-installation
```

Recommended responses during initialization:
* Switch to `unix_socket` authentication: **n**
* Change root password: **y** (set a strong root password)
* Remove anonymous users: **y**
* Disallow root login remotely: **y**
* Remove test database and access to it: **y**
* Reload privilege tables now: **y**

---

## 8. Create Database & User for OpenXE

Log in to the database shell as root:

```bash
sudo mariadb -u root -p
```

Run the following SQL commands to create the database with UTF-8 support and set up a dedicated user (replace `'YOUR_PASSWORD'` with a secure password):

```sql
CREATE DATABASE openxe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'openxe'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';
GRANT ALL PRIVILEGES ON openxe.* TO 'openxe'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Test your database connection with the newly created credentials:

```bash
mariadb -u openxe -p -e "SHOW DATABASES;"
```

The output should confirm access to `openxe` and `information_schema`.

---

## 9. Configure Apache VirtualHost

To allow OpenXE's `.htaccess` routing and security rules to function, `AllowOverride All` must be enabled.

In your virtual host configuration (e.g. `/etc/apache2/sites-available/000-default.conf` or your custom site config):

```apache
<Directory /var/www/html>
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Restart Apache to apply the new PHP configuration and Apache modules:

```bash
sudo systemctl restart apache2
```

---

## 10. Proceed with Installation

Your server is now ready. Continue with the [OpenXE Installation Guide (INSTALL.md)](INSTALL.md) to set up the OpenXE application files and complete the web-based installer.
