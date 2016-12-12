#!/bin/sh
/usr/local/bin/julius -C /home/osmc/script/julius/dictation-kit/moyashi.jconf &
sleep 5
/home/osmc/script/julius/remote_robo.py &

exit 0

