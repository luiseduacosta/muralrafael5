#!/bin/bash

# sudo chmod +x install.sh

# READ VARS

echo -e "database name?"
read database

echo -e "database dump location?"
read dump

echo -e "username?"
read username

echo -e "password?"
read password

# INSTALL PACKAGES

#sudo do-release-upgrade
sudo apt-get -y update
sudo apt-get -y dist-upgrade
sudo apt -y install software-properties-common php php-common php-mysql php-sqlite3 php-xml mariadb-server composer
sudo apt-get -y install php-curl

# CONFIGURE DATABASE

sudo systemctl start mariadb.service
echo -e "[y] unix socket [n] change password [y] remove users [y] login remote [y] reload tables"
read
sudo mysql_secure_installation

sudo mariadb -e "CREATE DATABASE $database;"
sudo mariadb -e "CREATE USER IF NOT EXISTS '$username'@'%' IDENTIFIED BY '$password'"
sudo mariadb -e "GRANT ALL PRIVILEGES ON *.* TO '$username'@'%' WITH GRANT OPTION;"
sudo mariadb -e "FLUSH PRIVILEGES;"
sudo mariadb $database < $dump
echo "mariadb database configured"

# SET USERS PERMISSIONS

sudo chown -R www-data /var/www/html
sudo usermod -a -G www-data $username
sudo chmod -R a+w /var/www/html

# CLONE MURAL

cd /var/www/html
git clone "https://github.com/rafaelcastrocouto/mural"
echo "mural repository cloned"

cd /var/www/html/mural
sudo chmod -R a+w /var/www/html/mural
sudo php /var/www/html/mural/src/Console/Installer.php
composer update
echo "composer requirements installed"

# EDIT APACHE CONF PERMISSION

echo nano /etc/apache2/apache2.conf
echo "<Directory /var/www>"
echo "	Options Indexes FollowSymLinks"
echo "	AllowOverride All <<<<<<<< CHANGE THIS"
echo "	Require all granted"
echo "</Directory>"
echo -e "enter to proceed to nano"
read
sudo nano /etc/apache2/apache2.conf

# CREATE CONFIG .ENV

sudo cp /var/www/html/mural/config/.env.example /var/www/html/mural/config/.env
echo "export SECURITY_SALT="12345678901234567890123456789012" <<<<<<<< CHANGE THIS"
echo "export PASSWORD => "$password", <<<<<<<< CHANGE THIS"
echo -e "enter to proceed to nano"
read
sudo nano /var/www/html/mural/config/.env

# CREATE CONFIG app_local.php

sudo cp /var/www/html/mural/config/app_local.example.php /var/www/html/mural/config/app_local.php
echo "'database' => '"$database"', <<<<<<<< CHANGE THIS"
echo "'username' => '"$username"', <<<<<<<< CHANGE THIS"
echo -e "enter to proceed to nano"
read
sudo nano /var/www/html/mural/config/app_local.php

# CONFIG APACHE MOD REWRITE

#sudo apt -y install software-properties-common apt-transport-https
#sudo add-apt-repository ppa:ondrej/php -y
#sudo apt -y install php8.1 libapache2-mod-php8.1
sudo a2enmod rewrite
echo "mod rewrite configured"
sudo systemctl restart apache2
echo "systemctl apache server restarted"

# CONFIG PHPMYADMIN

sudo apt -y install phpmyadmin
sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-available/phpmyadmin.conf
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin
echo "phpmyadmin configured"

