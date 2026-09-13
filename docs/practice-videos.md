# 練習メニューの動画

新規・編集画面の各練習メニューで動画を複数選択できます。選択すると4MiBずつ順番に送信し、完了後に「保存する／更新する」で練習に関連付けます。動画はアドバイスの直後に表示します。

- MP4 / MOV / M4V、1本200MiB、各メニュー10本。`config/practice_video.php`で上限を管理します。
- 通信失敗時は最大3回自動再試行し、さらに手動再試行・未完了分の取り消しができます。
- 動画を外す操作は練習の保存時に確定します。送信済みでも練習を保存しなかった動画は24時間で期限切れになります。
- 動画の変換は行いません。再生できるコーデックはブラウザによります。「動画を開く」リンクも表示します。

## 保存と認証

`practice_details.video_url`をTEXTに拡張し、複数の相対パスをJSON配列として保存します。既存の単一HTTP(S) URLは表示・保持できます。任意の新規URLやファイルパスの登録は受け付けません。

`practice_video_uploads`は送信者・対象選手・受信位置・状態・有効期限・関連する練習明細を管理します。`storage/app/practice-videos/{player_id}/`に保存し、公開ストレージには置きません。認証付きの配信ルートで対象選手へのアクセス権を再確認します。完了前の動画や他ユーザーの未保存動画は再生できません。

チャンクはDB行ロック内で受信位置に書き込みます。同じ番号の再送では二重追記しません。最終サイズとファイル内容由来のMIMEタイプを確認してから完成扱いにします。練習への関連付けもDBトランザクション内で実行します。

担当関係の作成・変更権限は既存のPlayerControllerの仕様に依存します。この変更では担当割当機能自体の権限設計は変更していません。

## 運用

マイグレーション：

```sh
php artisan migrate --path=database/migrations/2026_09_13_000001_add_practice_video_uploads.php
```

PHPの`upload_max_filesize`は4MiB以上、`post_max_size`とWebサーバーのリクエスト上限はmultipartの余裕を含め4MiBより大きく設定します。動画全体を1リクエストで送る必要はありません。

期限切れ動画・取り外した動画・削除済み練習の動画は次のコマンドで清掃します。登録済みで参照されている動画は削除しません。

```sh
php artisan practice-videos:cleanup
```

`app/Console/Kernel.php`に毎時実行を登録しています。実運用サーバーでLaravelスケジューラを動かす必要があります。PHP実行ユーザーには動画ディレクトリの読み書き・削除権限が必要です。既存cronがある場合は重複登録しません。

```cron
* * * * * cd /var/www/jocks && /usr/bin/php7.4 artisan schedule:run >> /dev/null 2>&1
```

今回確認したganaユーザーのcrontabには登録がなく、OSのcron設定は変更していません。

## 検証

```sh
php vendor/bin/phpunit
node tests/JavaScript/practice-videos.test.js
```

PHPテストにはPDO SQLiteが必要です。テストはメモリDBとStorage::fakeを使用し、実DB・実動画を変更しません。検証環境ではSQLite拡張を/tmpに展開し、PHPの`-d extension=.../pdo_sqlite.so`でテスト時だけ読み込みました。

PHPでは4MiB超の分割、重複再送、順序・サイズ・形式不正、期限切れ、複数動画の保存・編集、権限、Range配信、旧URL、入力エラー時の復元、清掃を検証しています。JSテストはDOMと通信を模擬して分割・再試行・動的メニュー・保存防止を検証しています。実ブラウザ・スマートフォンでの実動画再生は別途確認が必要です。
