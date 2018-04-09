#!/bin/sh
export SLACK_TOKEN="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
LANG=C date

cd ../unko

php ./add_calender.php
x=$?

if [ $x != 0 ]; then
  echo "$x日ぶりに、うんこ出たよ" | /usr/local/bin/slacker -c general -n unko -i :hankey:
  /usr/local/bin/jsay "よかったね。ウンコでてよかったね。いえーい"
fi
