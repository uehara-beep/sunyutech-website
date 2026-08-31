<?php
/**
 * contact.php の設定テンプレート。
 *
 * === 使い方 ===
 * このファイルをレンタルサーバー上で contact-config.php という名前にコピーし、
 * 値を埋めてください。contact-config.php は Git に入れません（シークレット
 * キーを含むため）。サイトを再デプロイしても上書きされないので、設定は一度
 * だけで済みます。
 */

return [
    // 問い合わせの届き先。実際のアドレスに書き換えること。
    // このファイルは公開リポジトリに入るため、ここには本物を書かない。
    'mail_to' => 'CHANGE-ME@example.jp',

    // 送信元。必ず sunyutech.jp のアドレスにすること。
    // 訪問者のアドレスを入れると SPF 認証に失敗し、Gmail 側で迷惑メール扱いになる。
    'mail_from' => 'no-reply@sunyutech.jp',   // 送信元。sunyutech.jp のアドレスにすること

    // 送信時間トラップ用の秘密鍵。他人と共有しない適当な長い文字列にする。
    // 生成例（サーバーの SSH かローカルで実行）:
    //   php -r "echo bin2hex(random_bytes(32));"
    'form_secret' => 'ここに生成したランダム文字列を貼る',

    // --- Cloudflare Turnstile ---
    // https://dash.cloudflare.com → Turnstile → サイトを追加 で2つのキーが発行される。
    // 両方ここに書けば有効になる。サイトの再ビルドやアップロードは不要。
    //
    // 既定は false。キーを2つとも埋めてから true にすること。
    // 片方だけで true にすると設定エラーとして 500 を返す（黙って全送信を
    // 拒否する状態を作らないため）。
    'turnstile_enabled' => false,
    'turnstile_sitekey' => '',   // 公開されるキー（ブラウザに渡る）
    'turnstile_secret'  => '',   // 絶対に公開しないキー

    // レート制限のカウントを保存するディレクトリ。
    // 未指定ならサーバーの一時ディレクトリを使う。公開ディレクトリの外を推奨。
    // 'rate_limit_dir' => '/home/example/contact-rate',
];
