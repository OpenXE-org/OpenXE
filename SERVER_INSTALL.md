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
