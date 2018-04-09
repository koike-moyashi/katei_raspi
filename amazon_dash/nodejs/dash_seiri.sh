#!/bin/bash
export SLACK_TOKEN="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
LANG=C date

/usr/local/bin/jsay "。。こんにちはー。こんげつもよろしくー。"

cd ../seirichan
php ./add_calender.php

# 画像の個数
no=$(($RANDOM % 12))

echo "こんにちはー" | /usr/local/bin/slacker -c general -f ./img/$no.png -n seiri_chan -i :seiri_chan:
