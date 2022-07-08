# Server Install (Example for ubuntu-22.04-live-server-amd64.iso)

## Create new server instance
e.g. 8 GB RAM, 128 GB hard drive, 4 CPUs.

## Install the operating system

## Refresh the update system
`sudo apt-get update`

## Install the webserver
`sudo apt-get install apache2`

-> Test the webserver (http://IP_ADDRESS)

It should show the "Apache2 Default Page".

## Install PHP (8.1)
`sudo apt-get install php libapache2-mod-php`

`php -v` should output:

```PHP 8.1.2 (cli) (built: Jun 13 2022 13:52:54) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.2, Copyright (c) Zend Technologies
with Zend OPcache v8.1.2, Copyright (c), by Zend Technologies
```

## Install PHP modules
`sudo apt-get install php-mysql php-cli php-imap php-curl php-xml php-soap php-zip php-mbstring php-gd`

## Configure PHP

Add the following lines to `/etc/php/8.1/apache2/php.ini` and `/etc/php/8.1/cli/php.ini`:

```
disable_functions = pcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority,dl,highlight_file,show_source,proc_open,popen
post_max_size = 100M
upload_max_filesize = 100M

max_execution_time = 3600
max_input_time = 3600
magic_quotes_gpc = Off
file_uploads = Yes (gesetzt: file_uploads = On)
max_file_uploads = 20
short_open_tag = On
max_input_vars=3000
memory_limit = 256M
```

## Install additional zip
`sudo apt-get install zip`

## Install mysql client
`sudo apt-get install mysql-client`

## Install database server
`sudo apt-get install mariadb-server`

## Configure database server
`sudo mysql_secure_installation`
```
NOTE: RUNNING ALL PARTS OF THIS SCRIPT IS RECOMMENDED FOR ALL MariaDB
      SERVERS IN PRODUCTION USE!  PLEASE READ EACH STEP CAREFULLY!

In order to log into MariaDB to secure it, we'll need the current
password for the root user. If you've just installed MariaDB, and
haven't set the root password yet, you should just press enter here.

Enter current password for root (enter for none): 
OK, successfully used password, moving on...

Setting the root password or using the unix_socket ensures that nobody
can log into the MariaDB root user without the proper authorisation.

You already have your root account protected, so you can safely answer 'n'.

Switch to unix_socket authentication [Y/n] n
 ... skipping.

You already have your root account protected, so you can safely answer 'n'.

Change the root password? [Y/n] y
New password: 
Re-enter new password: 
Password updated successfully!
Reloading privilege tables..
 ... Success!


By default, a MariaDB installation has an anonymous user, allowing anyone
to log into MariaDB without having to have a user account created for
them.  This is intended only for testing, and to make the installation
go a bit smoother.  You should remove them before moving into a
production environment.

Remove anonymous users? [Y/n] y
 ... Success!

Normally, root should only be allowed to connect from 'localhost'.  This
ensures that someone cannot guess at the root password from the network.

Disallow root login remotely? [Y/n] y
 ... Success!

By default, MariaDB comes with a database named 'test' that anyone can
access.  This is also intended only for testing, and should be removed
before moving into a production environment.

Remove test database and access to it? [Y/n] y
 - Dropping test database...
 ... Success!
 - Removing privileges on test database...
 ... Success!

Reloading the privilege tables will ensure that all changes made so far
will take effect immediately.

Reload privilege tables now? [Y/n] y
 ... Success!

Cleaning up...

All done!  If you've completed all of the above steps, your MariaDB
installation should now be secure.

Thanks for using MariaDB!
```
## Create database for xenomporio
`mysql -u root -p`

```
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is 41
Server version: 10.6.7-MariaDB-2ubuntu1 Ubuntu 22.04

Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

MariaDB [(none)]> CREATE DATABASE xenomporio;
Query OK, 1 row affected (0.001 sec)

MariaDB [(none)]> CREATE USER 'xenomporio'@'localhost' IDENTIFIED BY 'enteryourpasswordhere';
Query OK, 0 rows affected (0.015 sec)

MariaDB [(none)]> GRANT ALL PRIVILEGES ON xenomporio.* TO 'xenomporio'@'localhost' WITH GRANT OPTION;
Query OK, 0 rows affected (0.012 sec)

MariaDB [(none)]> FLUSH PRIVILEGES;
Query OK, 0 rows affected (0.002 sec)

MariaDB [(none)]> quit
Bye

```

You can test your database like this:
`mysql -u xenomporio -p`

```
Enter password: 
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is 44
Server version: 10.6.7-MariaDB-2ubuntu1 Ubuntu 22.04

Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

MariaDB [(none)]> show databases;
+--------------------+
| Database           |
+--------------------+
| information_schema |
| xenomporio         |
+--------------------+
2 rows in set (0.001 sec)

MariaDB [(none)]> quit
Bye
```
# Reset your server
