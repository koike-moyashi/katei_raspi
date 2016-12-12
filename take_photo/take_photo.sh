#!/bin/bash

export SLACK_TOKEN="XXXXXXXXXXXXXXXXXXXXXXXXXX"

date=`date +"%Y%m%d-%H%M%S"`

sudo /usr/local/bin/gphoto2 --set-config autofocusdrive=1 --capture-image-and-download --filename=/home/osmc/Pictures/$date.jpg
sudo chown osmc.osmc /home/osmc/Pictures/$date.jpg

#post slack
/usr/bin/convert -geometry 20% /home/osmc/Pictures/$date.jpg /home/osmc/Pictures/P$date.jpg
date | /usr/local/bin/slacker -c photo -n Raspi -i :robo: -f /home/osmc/Pictures/P$date.jpg
rm -f /home/osmc/Pictures/P$date.jpg

#upload nas
