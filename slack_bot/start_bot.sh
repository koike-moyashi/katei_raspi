#!/bin/sh
export HUBOT_SLACK_TOKEN=XXXXXXXXXXXXXXXXXXX
export PATH=$PATH:/home/osmc/.nodebrew/current/bin

cd /home/osmc/script/slack_bot
forever start -l /var/log/nodejs/logfile.log -a -c coffee node_modules/.bin/hubot -a slack
