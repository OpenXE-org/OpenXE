# OpenXE installation

## Place the installation files into a folder in /var/www/html/.

`cd /var/www/html`

e.g. Release 1.0:

`sudo wget https://github.com/openxe-org/OpenXE/archive/refs/tags/V.1.0.zip`

`unzip V.1.0.zip`

## Set folder permissions:

`sudo chown www-data:www-data OpenXE-V.1.0 -R`

## Fire up the setup page in a browser

http://yourserverip/OpenXE-V.1.0 (watch out, its case sensitive)

<img src="doc/Install-10.png" width="800px">
<img src="doc/Install-20.png" width="800px">
<img src="doc/Install-30.png" width="800px">
<img src="doc/Install-40.png" width="800px">
<img src="doc/Install-50.png" width="800px">
<img src="doc/Install-60.png" width="800px">
<img src="doc/Install-done.png" width="800px">
