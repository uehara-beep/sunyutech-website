<?php
/**
 * お問い合わせフォームの受け口。
 *
 * レンタルサーバーの公開ディレクトリに置いて使う。設定値は隣の contact-config.php
 * から読む（シークレットキーを含むのでリポジトリには入れない）。
 *
 * GET  ?action=token  … フォーム用の署名付きトークンを発行
 * POST                … 検証してメール送信
 *
 * 必要な PHP: 8.0 以上
 */

declare(strict_types=1);

require_once __DIR__ . '/contact-lib.php';

// PHP の警告や通知が応答本文に混ざると、ブラウザ側が JSON を解釈できなくなる。
// 共用サーバーは display_errors=On が既定のことがあるため明示的に切る。
// 問題はログに残す（error_log は有効なまま）。
ini_set('display_errors', '0');
ini_set('log_errors', '1');

mb_internal_encoding('UTF-8');
mb_language('uni');

header('X-Content-Type-Options: nosniff');

const CF_MIN_SECONDS = 3;      // これより速い送信はボットとみなす
const CF_MAX_SECONDS = 3600;   // トークンの有効期限
const CF_RATE_MAX = 5;         // 同一IPからの上限件数（送信成功したものだけ数える）
const CF_RATE_WINDOW = 3600;   // レート制限の時間枠（秒）

const CF_PHONE = '092-555-9211';

/** リクエストが JSON を期待しているか（fetch からの送信かどうか）。 */
function cf_wants_json(): bool {
    return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

/**
 * 応答して終了する。
 *
 * JS が無効な環境では fetch が動かず素の POST が飛んでくる。その場合に
 * JSON をそのまま返すと、訪問者は生の JSON テキストだけの画面を見ることになる。
 * Accept ヘッダーを見て、人間向けには HTML を返す。
 */
function cf_respond(int $status, array $payload): void {
    http_response_code($status);

    if (cf_wants_json()) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    header('Content-Type: text/html; charset=UTF-8');
    $message = htmlspecialchars((string)($payload['message'] ?? ''), ENT_QUOTES, 'UTF-8');
    $heading = !empty($payload['ok']) ? '送信しました' : '送信できませんでした';
    echo <<<HTML
    <!doctype html><html lang="ja"><head><meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{$heading} | 株式会社サンユウテック</title>
    <style>
      body { font-family: system-ui, sans-serif; max-width: 40rem; margin: 4rem auto; padding: 0 1.5rem; line-height: 1.8; color: #1a1a1a; }
      h1 { font-size: 1.5rem; }
      a { color: #c2410c; }
    </style></head><body>
    <h1>{$heading}</h1>
    <p>{$message}</p>
    <p>お急ぎの場合はお電話ください: <a href="tel:0925559211">092-555-9211</a>（平日 9:00〜17:00）</p>
    <p><a href="/contact/">お問い合わせページに戻る</a></p>
    </body></html>
    HTML;
    exit;
}

/** Cloudflare の検証APIを叩く。cf_verify_turnstile に差し込んで使う。 */
function cf_http_post(string $url, array $fields): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($fields),
        CURLOPT_TIMEOUT        => 10,
    ]);
    $body = curl_exec($ch);
    curl_close($ch);
    // 通信失敗は空文字にする。呼び出し側で JSON として解釈できず false になる。
    return is_string($body) ? $body : '';
}

// ---- 設定の読み込みと検証 --------------------------------------------------

$configPath = __DIR__ . '/contact-config.php';
if (!is_file($configPath)) {
    // 設定漏れに気づかないまま「送れているつもり」になるのが一番まずいので、
    // 黙って握りつぶさずエラーを返す。
    error_log('contact.php: contact-config.php が見つかりません');
    cf_respond(500, ['ok' => false, 'message' => 'サーバー設定が未完了です。お手数ですがお電話（' . CF_PHONE . '）でご連絡ください。']);
}

$config = require $configPath;

if (!is_array($config) || ($configErrors = cf_validate_config($config))) {
    // キーの打ち間違いや記入漏れをここで捕まえる。放置すると後段で
    // TypeError になり、JSONを名乗るHTMLエラーページが返ってしまう。
    error_log('contact.php: contact-config.php の内容が不正です: ' . implode(' / ', $configErrors ?? ['配列を返していません']));
    cf_respond(500, ['ok' => false, 'message' => 'サーバー設定が未完了です。お手数ですがお電話（' . CF_PHONE . '）でご連絡ください。']);
}

// 既定の保存先には秘密鍵由来のサフィックスを付ける。共用サーバーの /tmp は
// 他の契約者からも書けるため、固定名だと先にディレクトリを作られて
// 書き込み不能にされ、レート制限を無効化できてしまう。
$rateDir = $config['rate_limit_dir']
    ?? (sys_get_temp_dir() . '/sunyutech-contact-' . substr(hash('sha256', $config['form_secret']), 0, 16));
if (!is_dir($rateDir)) {
    @mkdir($rateDir, 0700, true);
}
// 保存先が使えなくてもフォームは止めない。ここで止めると、/tmp が使えない
// だけで全ての問い合わせが拒否されフォームが死ぬ。レート制限を諦めて
// ログに残し、残り5層の対策で運用を続ける。
$rateLimitUsable = cf_rate_limit_available($rateDir);
if (!$rateLimitUsable) {
    error_log("contact.php: レート制限の保存先が使えないため、レート制限なしで動作しています: {$rateDir}");
}

// ---- トークン発行 ----------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && ($_GET['action'] ?? '') === 'token') {
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    // サイトキーもここで渡す。ビルド時に埋め込むと、Turnstile を有効化する
    // たびに再ビルドと再デプロイが必要になり、サーバーしか触れない担当者が
    // 設定を完了できない。
    echo json_encode([
        'token' => cf_make_form_token(time(), $config['form_secret']),
        'turnstile_sitekey' => !empty($config['turnstile_enabled'])
            ? (string)($config['turnstile_sitekey'] ?? '') : '',
        // 上限をフォームにも配って maxlength にする。サーバー定義を唯一の
        // 出典にすることで、フロントとサーバーの上限がずれるのを防ぐ。
        'field_limits' => cf_field_limits(),
    ]);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    cf_respond(405, ['ok' => false, 'message' => '許可されていないリクエストです。']);
}

// ---- 送信処理 --------------------------------------------------------------

$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$forwardedIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '';
$trustedRanges = $config['trusted_proxy_ranges'] ?? null;

$ip = cf_resolve_client_ip($remoteAddr, $forwardedIp, $trustedRanges);

// Cloudflare のヘッダーは来ているのに接続元が既知のレンジ外。
// レンジ表が古いか、ヘッダーの詐称。前者を放置すると全訪問者が
// Cloudflare の共有エッジIPとして同じレート制限枠を食い合い、
// 5件で全員が締め出される。気づけるようにログに残す。
if (cf_proxy_header_unrecognized($remoteAddr, $forwardedIp, $trustedRanges)) {
    error_log("contact.php: 信頼レンジ外からプロキシヘッダーを受信しました remote={$remoteAddr}。"
        . 'Cloudflare のIPレンジ表が古い可能性があります（https://www.cloudflare.com/ips/）');
}

// 1層目: ハニーポット。ボットには「成功」を返して破棄する。
//        失敗を返すと、ボット側が条件を変えて再試行してくるため。
if (cf_is_bot_honeypot($_POST)) {
    // 破棄した事実を残す。パスワードマネージャの自動入力などで人間の
    // 問い合わせを誤って捨てた場合、ログが無いと気づけない。
    error_log('contact.php: ハニーポットにより破棄しました ip=' . $ip);
    cf_respond(200, ['ok' => true, 'message' => '送信しました。担当者よりご連絡いたします。']);
}

// 2層目: レート制限（ここでは判定のみ。実際に消費するのは送信成功後）
if ($rateLimitUsable && !cf_rate_limit_check($ip, $rateDir, CF_RATE_MAX, CF_RATE_WINDOW, time())) {
    cf_respond(429, ['ok' => false, 'message' => '送信回数の上限に達しました。しばらく時間をおいてからお試しください。']);
}

// 3層目: 送信時間トラップ
$token = cf_str($_POST, '_token');
if ($token === '') {
    // JavaScript が無効だとトークンが入らない。再読み込みを促しても
    // 永久に成功しないので、電話での問い合わせに誘導する。
    cf_respond(400, ['ok' => false, 'message' => 'お使いのブラウザではフォームをご利用いただけません（JavaScriptが無効です）。お手数ですがお電話（' . CF_PHONE . '）でご連絡ください。']);
}
if (!cf_check_form_token($token, time(), $config['form_secret'], CF_MIN_SECONDS, CF_MAX_SECONDS)) {
    cf_respond(400, ['ok' => false, 'message' => 'フォームの有効期限が切れました。ページを再読み込みしてもう一度お試しください。']);
}

// 4層目: Cloudflare Turnstile
//        有効なのにシークレットが空、という組み合わせは cf_validate_config が
//        起動時に弾いている（黙って全送信を拒否する状態を作らないため）。
//        ここに来た時点でシークレットは必ず設定されている。
if (!empty($config['turnstile_enabled'])) {
    $ok = cf_verify_turnstile(
        (string)($_POST['cf-turnstile-response'] ?? ''),
        (string)$config['turnstile_secret'],
        $ip,
        'cf_http_post'
    );
    if (!$ok) {
        cf_respond(400, ['ok' => false, 'message' => '認証に失敗しました。ページを再読み込みしてもう一度お試しください。']);
    }
}

// 5層目: サーバー側バリデーション
$errors = cf_validate($_POST);
if ($errors) {
    cf_respond(422, ['ok' => false, 'message' => '入力内容をご確認ください。', 'errors' => $errors]);
}

// 6層目: メールヘッダーインジェクション対策は cf_build_mail の中で実施

$mail = cf_build_mail($_POST, $config['mail_to'], $config['mail_from']);

// エンベロープ送信者の指定は cf_send_mail が担当する。
// サーバーが -f を拒否した場合は指定なしで再送する（詳細は同関数のコメント）。
// $config['mail_from'] は cf_validate_config でメールアドレス形式を検証済み。
$sent = cf_send_mail($mail, $config['mail_from'], function ($to, $subject, $body, $headers, $params) {
    return $params === null
        ? mb_send_mail($to, $subject, $body, $headers)
        : mb_send_mail($to, $subject, $body, $headers, $params);
});

if (!$sent) {
    error_log('contact.php: mb_send_mail に失敗しました');
    cf_respond(500, ['ok' => false, 'message' => '送信に失敗しました。お手数ですが、お電話（' . CF_PHONE . '）でご連絡ください。']);
}

// 送信できたときだけレート制限を消費する。入力ミスで弾かれた分まで数えると、
// 正当な利用者が自分の打ち間違いで1時間締め出されてしまう。
if ($rateLimitUsable) {
    cf_rate_limit_hit($ip, $rateDir, CF_RATE_WINDOW, time());
}

cf_respond(200, ['ok' => true, 'message' => '送信しました。担当者よりご連絡いたします。']);
