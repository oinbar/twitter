#!/usr/bin/env bash


#sudo DEBIAN_FRONTEND=noninteractive apt-get -y install [packagename]

sudo apt-get update
sudo apt-get install curl
sudo apt-get install php-pear

# Install PHP 5.5
sudo apt-get install software-properties-common
sudo add-apt-repository ppa:ondrej/php5-oldstable
sudo apt-get upgrade
sudo apt-get install php5
sudo apt-get install php5-dev

#sudo apt-get install php5-mcrypt
#sudo php5enmod mcrypt

# Install Apache2
sudo apt-get install apache2 libapache2-mod-php5

# Install MySQL
sudo apt-get install mysql-server php5-mysql

# Install mongodb
sudo pecl install mongo
sudo echo "extension=mongo.so" >> sudo /etc/php5/cli/php.ini
sudo echo "extension=mongo.so" >> sudo /etc/php5/apache2/php.ini

# install redis


# Install Composer
sudo curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# install vendors
composer install

# launch server
#sudo php artisan serve