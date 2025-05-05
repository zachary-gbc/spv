#!/bin/bash

. /var/www/html/pss/conf/pss.conf

dbname="spv_prod"
echo "Is PSS already running? (Y or N)"
read pssyesno
if [[ $pssyesno == "N" ]] || [[ $pssyesno == "n" ]]
then
  echo "Please Install PSS first, that is required. Please find code at github.com/zachary-gbc/pss"
  exit 1
fi

sudo apt-get update
sudo apt-get install php-gd

install_log="/home/pi/spv_install.log"
echo "Initiating Install" > $install_log

sudo mkdir -p /var/www/html/spv
sudo chown pi:pi /var/www/html/spv
sudo mkdir -p /var/www/html/spv/files
sudo chown www-data:www-data /var/www/html/spv/files
sudo mysql --user='root' -e "CREATE DATABASE IF NOT EXISTS $dbname"

sudo mysql --user="$dbuser" --password="$dbpass" --database="$dbname" -e "DROP TABLE IF EXISTS Devices;"
sudo mysql --user="$dbuser" --password="$dbpass" --database="$dbname" -e "CREATE TABLE StagePlots(Plot_ID INT NOT NULL AUTO_INCREMENT, Plot_Name VARCHAR(255) NOT NULL, Plot_EventDate DATE NULL, Plot_Start1 DATETIME NULL, Plot_Start2 DATETIME NULL, Plot_Start3 DATETIME NULL, PRIMARY KEY(Plot_ID))"

echo "Copy Files to correct folder"

cp pretitle.php /var/www/html/spv/pretitle.php
cp posttitle.php /var/www/html/spv/posttitle.php
cp footer.php /var/www/html/spv/footer.php
cp index.php /var/www/html/spv/index.php
cp viewplot.php /var/www/html/spv/viewplot.php
cp viewplot.php /var/www/html/spv/styles.css
sudo service apache2 restart

echo ""
echo "----------------------"
echo "-- Install Complete --"
echo "----------------------"
echo ""

sudo rm -r -f /home/pi/spv
