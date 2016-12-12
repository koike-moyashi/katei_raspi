#!/bin/sh
# 
# 休日をチェックして休日の場合はお話しない
#


# 引数チェック
CMDNAME=`basename $0`
if [ $# -lt 1 ]; then
    echo "Usage: ${CMDNAME} [ text ]" 1>&2
    exit 1
fi

# 休日チェック
HOLIDAY=`/usr/bin/curl -s http://s-proj.com/utils/checkHoliday.php | grep holiday | wc -l`

# 定数定義（出力ファイル名、辞書の場所、音声データの場所）
TMPFILE=`mktemp /tmp/tmp.XXXXXXXX.wav`
DIC=/var/lib/mecab/dic/open-jtalk/naist-jdic
VOICE=/usr/share/hts-voice/mei/mei_normal.htsvoice

# 休日の場合止める
if [ $HOLIDAY -eq 1 ];then
 exit 0
fi


# 音声データ生成
echo "$1" | open_jtalk \
-x ${DIC} \
-m ${VOICE} \
-ow ${TMPFILE} && \


# osmc一時停止
/home/osmc/script/play_music.py -p


# 生成した音声データを再生する
amixer -q -c 2 set PCM 60%
aplay --quiet /home/osmc/script/wav/se_maoudamashii_chime01.wav
aplay --quiet ${TMPFILE}
amixer -q -c 2 set PCM 100%

# osmc一時停止解除
/home/osmc/script/play_music.py -p


# 生成した音声データを削除する
rm -f ${TMPFILE}

# 終了
exit 0
