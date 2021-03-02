sudo apt-get --yes install libcurl3 php7.0-curl php7.0-cli php7.0-xml
sudo usermod -a -G lpadmin pi
sudo systemctl enable ssh
wget http://www.wawision.de/adapterbox.deb 
sudo dpkg -i adapterbox.deb
echo "Seriennummer eingeben gefolgt von einem [ENTER]:"
read seriennummer
echo "<?php \$config['serial'] = '$seriennummer'; ?>" > /tmp/config.php
sudo mv /tmp/config.php /root/config.php
sudo halt
