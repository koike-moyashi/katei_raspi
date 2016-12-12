#!/bin/sh

# 引数チェック
CMDNAME=`basename $0`
if [ $# -lt 1 ]; then
    echo "Usage: ${CMDNAME} [ text ]" 1>&2
    exit 1
fi

# 定数定義（出力ファイル名、辞書の場所、音声データの場所）
TMPFILE=`mktemp /tmp/tmp.XXXXXXXX.wav`
DIC=/var/lib/mecab/dic/open-jtalk/naist-jdic
VOICE=/usr/share/hts-voice/mei/mei_normal.htsvoice


# 音声データ生成
echo "$1" | open_jtalk \
-x ${DIC} \
-m ${VOICE} \
-ow ${TMPFILE} && \


# 生成した音声データを再生する
amixer -q -c 2 set PCM 60%
aplay --quiet ${TMPFILE}
amixer -q -c 2 set PCM 100%

# 生成した音声データを削除する
rm -f ${TMPFILE}

echo -n "オハナシシタヨ"

# 終了
exit 0
