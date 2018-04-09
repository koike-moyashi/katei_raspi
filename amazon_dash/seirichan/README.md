# 生理ちゃんボタン

下記の記事で使用しているスクリプトです。

* 生理ちゃんボタンとうんこボタンを作った話（Amazon dash）
  https://www.moyashi-koubou.com/blog/amazon_dash_seiri_unko/

## 使用方法

0. cloneしたディレクトリでgoogle calender apiの使用許可＆純正sdkのダウンロード（Step 1-4をする）
https://developers.google.com/calendar/quickstart/php
0. google calenderでカレンダーを一つ作成→該当カレンダーの設定→カレンダーIDをコピーしておく
0. add_calender.phpの97行目のカレンダーIDを上記のものに変更
0. 手動実行：php add_calender.php（テスト）
