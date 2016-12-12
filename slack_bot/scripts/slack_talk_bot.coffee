# Description
#  指定されたroomで発言されたメッセージをjsayで発声する
# 
# Dependencies:
#  特に無し
#
# Configuration:
#  環境設定を書く
#
# Commands:
#  hubot <message> - <message欄にコメントを入れる>
#
# Notes:
#  メモ書き, その他
#
# Author:
#  ishiguro@moyashi-koubou.com

child_process = require('child_process')

module.exports = (robot) ->
  robot.hear /(.*)$/i, (msg) ->
    text = msg.message.text
    room = msg.envelope.room
    text_en = encodeURIComponent text

    # roomの指定
    if (room == "bot_test" || room == "robo_talk")
      # 外部コマンドの実行
      child_process.exec "sh /usr/local/bin/jsay '#{text}'", (error, stdout, stderr) ->
        # コマンド実行した標準出力をbotが話す
        msg.send(stdout)
