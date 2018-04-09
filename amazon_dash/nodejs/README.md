# amazon dash button

下記の記事で使用しているスクリプトです。

* 生理ちゃんボタンとうんこボタンを作った話（Amazon dash）
  https://www.moyashi-koubou.com/blog/amazon_dash_seiri_unko/

## 使用方法

0. nodejsをセットアップ
0. このディレクトリで下記URLのInstallationを参考に、libpcap-devとnode-dash-buttonを入れる
    https://github.com/hortinstein/node-dash-button
0. node-dash-buttonに附属のfindbutton or ルーターのDHCPリース情報などからdashボタンのMACアドレスを見つける
0. amazon-dash.jsの```--:--:--:--:--:--```となっている箇所を変更
0. sudo node amazon-dash.js
