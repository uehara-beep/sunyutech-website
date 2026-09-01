<?php
/**
 * お問い合わせフォームの処理ロジック。
 *
 * 副作用のない純粋関数だけをここに置き、実際の送信・HTTP通信は contact.php 側で行う。
 * テスト: php astro/tests/contact-lib.test.php
 *
 * 依存ライブラリなし（Composer 不要）。レンタルサーバーにそのまま置ける。
 */

/**
 * 入力項目と、その最大文字数・必須かどうか。検証もメール本文も maxlength も
 * この定義を唯一の出典にする。
 *
 * このフォームは見積依頼と採用応募の両方を受ける。以前は会社名を必須に
 * していたため、採用ページのCTAから来た応募者はフォームを送信できなかった。
 * 会社名・工事種別・工事規模の項目は廃止し、必要なら本文に書いてもらう。
 */
const CF_FIELDS = [
    'name'    => ['label' => 'お名前',                   'required' => true,  'max' => 100],
    'email'   => ['label' => 'メールアドレス',           'required' => true,  'max' => 254],
    'tel'     => ['label' => '電話番号',                 'required' => false, 'max' => 50],
    'message' => ['label' => 'お問い合わせ・ご応募内容', 'required' => true,  'max' => 5000],
];

/** 定義外の項目（extras）の上限。未検証の入力が通る経路なので必ず抑える。 */
const CF_EXTRA_MAX_COUNT = 10;
const CF_EXTRA_MAX_KEY = 64;
const CF_EXTRA_MAX_VALUE = 200;

/** 項目ごとの最大文字数。フォーム側の maxlength に配って表示崩れと無駄な往復を防ぐ。 */
function cf_field_limits(): array {
    $limits = [];
    foreach (CF_FIELDS as $key => $spec) {
        $limits[$key] = $spec['max'];
    }
    return $limits;
}

/**
 * フォーム入力から文字列を1つ取り出す。
 *
 * company[]=A のように配列で送られると (string) キャストが
 * "Array to string conversion" 警告を出す。display_errors が有効な
 * 共用サーバーではその出力が JSON 応答の前に混ざり、ブラウザ側が
 * 応答を解釈できなくなる。文字列でない値は空として扱う。
 */
function cf_str(array $input, string $key): string {
    $value = $input[$key] ?? '';
    return is_string($value) ? $value : '';
}

/**
 * メールヘッダーに入れる値から、改行とNULLバイトを取り除く。
 *
 * これを怠ると、氏名やメールアドレス欄に "\r\nBcc: ..." を仕込まれてフォームが
 * 第三者へのスパム送信の踏み台にされる（メールヘッダーインジェクション）。
 */
function cf_sanitize_header(string $value): string {
    return trim(str_replace(["\r", "\n", "\0"], '', $value));
}

/**
 * 入力を検証し、項目名 => エラーメッセージ の配列を返す。問題なければ空配列。
 * クライアント側の required 属性は簡単に外せるので、必ずサーバー側でも検証する。
 */
function cf_validate(array $input): array {
    $errors = [];

    foreach (CF_FIELDS as $key => $spec) {
        $value = trim(cf_str($input, $key));

        if ($spec['required'] && $value === '') {
            $errors[$key] = $spec['label'] . 'を入力してください';
            continue;
        }
        if ($value !== '' && mb_strlen($value) > $spec['max']) {
            $errors[$key] = $spec['label'] . 'は' . $spec['max'] . '文字以内で入力してください';
            continue;
        }
        if ($key === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$key] = 'メールアドレスの形式が正しくありません';
        }
    }

    return $errors;
}

/**
 * ハニーポット判定。人間には見えない隠しフィールドに入力があればボット。
 * フォームを機械的に埋めるボットは、隠されていることが分からず全項目を埋める。
 */
function cf_is_bot_honeypot(array $input): bool {
    // 配列で送られた場合も、通常の利用ではありえないのでボット扱いにする
    if (isset($input['_gotcha']) && !is_string($input['_gotcha'])) {
        return true;
    }
    return trim(cf_str($input, '_gotcha')) !== '';
}

/**
 * フォーム表示時刻に署名を付けたトークンを発行する。
 * 署名があるので、送信側で時刻だけ書き換えて時間トラップをすり抜けることはできない。
 */
function cf_make_form_token(int $issuedAt, string $secret): string {
    return $issuedAt . '.' . hash_hmac('sha256', (string)$issuedAt, $secret);
}

/**
 * トークンを検証する。署名が正しく、かつ経過時間が [$minSeconds, $maxSeconds] に収まれば true。
 *
 * - 速すぎる送信 → ボット（人間はフォームを数秒で埋められない）
 * - 遅すぎる送信 → 使い回されたトークン
 *
 * 【既知の制約 / 受容したリスク】
 * このトークンは使い捨てではない。有効期限内（既定1時間）であれば、
 * 同じトークンで何度でも送信できる。
 *
 * 一回限りにするには使用済みトークンをサーバーに記録する必要があるが、
 * その保存先はレート制限と同じディレクトリになる。そこが使えないときに
 * 「通す」なら結局すり抜けられ、「止める」なら保存先が壊れた瞬間に
 * フォームが全滅する。cf_rate_limit_check で fail-open を選んだのと
 * 同じジレンマに戻るため、ここでは実装しない。
 *
 * 実害が出るのは「レート制限の保存先が壊れている」かつ「Turnstile 未設定」が
 * 重なったときに限られる。Turnstile を有効にすれば Cloudflare 側のトークンが
 * 一回限りなので、この経路は塞がる。
 */
function cf_check_form_token(string $token, int $now, string $secret, int $minSeconds, int $maxSeconds): bool {
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        return false;
    }
    [$issuedAt, $signature] = $parts;
    if ($issuedAt === '' || !ctype_digit($issuedAt)) {
        return false;
    }

    $expected = hash_hmac('sha256', $issuedAt, $secret);
    // hash_equals: 比較時間の差から署名を推測されないようにする
    if (!hash_equals($expected, $signature)) {
        return false;
    }

    $age = $now - (int)$issuedAt;
    return $age >= $minSeconds && $age <= $maxSeconds;
}

/**
 * Cloudflare Turnstile のトークンを検証する。
 *
 * $http は ($url, $fields) を受け取りレスポンス本文を返す callable。
 * こう切っておくことで、テストから実際の通信なしに検証できる。
 * 判定がつかない場合はすべて false（フェイルクローズ）。
 */
function cf_verify_turnstile(string $token, string $secret, string $remoteIp, callable $http): bool {
    if (trim($token) === '') {
        return false;
    }

    $body = $http('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => $remoteIp,
    ]);

    if (!is_string($body)) {
        return false;
    }

    $decoded = json_decode($body, true);
    return is_array($decoded) && ($decoded['success'] ?? false) === true;
}

/**
 * 送信するメールを組み立てる。
 *
 * From を訪問者のアドレスにすると SPF 認証に失敗し、Gmail 側で迷惑メール扱いになる。
 * そのため From は自社ドメイン固定、返信先だけ Reply-To で訪問者に向ける。
 *
 * 件名は UTF-8 のまま返す。MIMEエンコードは送信側の mb_send_mail() に任せる
 * （ここでエンコードすると長い件名が折り返されて改行が混入するため）。
 */
function cf_build_mail(array $input, string $to, string $from): array {
    $name    = cf_sanitize_header(cf_str($input, 'name'));
    $replyTo = cf_sanitize_header(cf_str($input, 'email'));

    // Reply-To に不正な値が来たら、ヘッダーに載せずに捨てる
    if (!filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $replyTo = '';
    }

    $lines = ['サンユウテックのホームページからお問い合わせがありました。', ''];
    foreach (CF_FIELDS as $key => $spec) {
        $value = trim(cf_str($input, $key));
        $lines[] = $spec['label'] . ': ' . ($value === '' ? '(未入力)' : $value);
    }
    // CF_FIELDS に無い項目も拾う。デプロイの入れ替わり中に、古いHTMLを
    // 開いたままの訪問者が旧項目（会社名など）を送ってくることがある。
    // 定義だけを見て組み立てると黙って捨てられ、受け取る側は情報が
    // 欠けたことにすら気づけない。
    //
    // ただし cf_validate は CF_FIELDS しか検証しないため、ここは未検証の
    // 入力が通る唯一の経路になる。上限を掛けないと、message の文字数制限を
    // 別のキー名で送るだけで回避され、受信箱にメガバイト級の本文が届く。
    $internal = ['_token', '_gotcha', 'cf-turnstile-response'];
    $extras = [];
    foreach ($input as $key => $_) {
        if (count($extras) >= CF_EXTRA_MAX_COUNT) {
            break;
        }
        if (!is_string($key) || isset(CF_FIELDS[$key]) || in_array($key, $internal, true)) {
            continue;
        }
        if (mb_strlen($key) > CF_EXTRA_MAX_KEY) {
            continue;   // 通常の入力ではありえない長さのキーは捨てる
        }
        $value = trim(cf_str($input, $key));
        if ($value === '') {
            continue;
        }
        if (mb_strlen($value) > CF_EXTRA_MAX_VALUE) {
            $value = mb_substr($value, 0, CF_EXTRA_MAX_VALUE) . '…（以下省略）';
        }
        $extras[] = cf_sanitize_header($key) . ': ' . $value;
    }
    if ($extras) {
        $lines[] = '';
        $lines[] = '【その他の入力】';
        foreach ($extras as $line) {
            $lines[] = $line;
        }
    }

    $lines[] = '';
    $lines[] = '---';
    $lines[] = 'このメールは sunyutech.jp のお問い合わせフォームから自動送信されています。';
    $lines[] = 'そのまま返信すれば、お客様に直接届きます。';

    // Content-Type / MIME-Version / Content-Transfer-Encoding は付けない。
    // mb_send_mail が本文のエンコードに合わせて自分で組み立てるので、
    // 自前で重ねるとヘッダーの二重定義や文字化けの原因になる。
    $headers = ['From: ' . $from];
    if ($replyTo !== '') {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    return [
        'to'      => $to,
        'subject' => cf_sanitize_header('【HP問い合わせ】' . ($name === '' ? 'お名前なし' : $name)),
        'body'    => implode("\n", $lines),
        'headers' => implode("\r\n", $headers),
    ];
}

/**
 * メールを送る。$sender は mb_send_mail 互換の callable。
 *
 * まず -f でエンベロープ送信者を自社ドメインに指定して送る。これが無いと
 * サーバー既定の送信者（apache@svNNN...）のままになり、SPF が認証する
 * ドメインと From: が一致せず、受信側の Gmail で DMARC に落ちる。
 *
 * ただし共用サーバーによっては -f の指定自体が拒否される。そこで諦めると
 * 問い合わせが1通も届かなくなるため、-f 無しで送り直す。
 * 迷惑メールに入る可能性は残るが、届かないよりはよい。
 */
function cf_send_mail(array $mail, string $envelopeFrom, callable $sender): bool {
    if ($sender($mail['to'], $mail['subject'], $mail['body'], $mail['headers'], '-f' . $envelopeFrom)) {
        return true;
    }

    // 失敗の理由は PHP からは判別できない。-f の拒否とは限らず、MTA 自体の
    // 不調のこともある。後者の場合、MTA が受理した上で異常終了していると
    // 同じ問い合わせが2通届く可能性がある。それでも再送するのは、
    // 「届かない」より「稀に重複する」方が損失が小さいため。
    error_log('contact: 送信に失敗したため、エンベロープ送信者(-f)の指定なしで再試行します。'
        . '-f が許可されていないか、メール送信自体に問題がある可能性があります。'
        . '重複して届いている場合や届かない場合は、サーバー側の設定を確認してください。');

    return (bool)$sender($mail['to'], $mail['subject'], $mail['body'], $mail['headers'], null);
}

/**
 * レート制限の記録ファイルのパス。
 *
 * IP はそのままファイル名にせずハッシュ化する（"../.." のような値で
 * 保存先ディレクトリの外に書き出されるのを防ぐため）。
 */
function cf_rate_file(string $ip, string $dir): string {
    return rtrim($dir, '/') . '/' . sha1($ip) . '.json';
}

/**
 * 時間枠を過ぎたレート制限の記録を削除する。
 *
 * IP のハッシュ1件につき1ファイルを作るため、掃除しないと際限なく溜まる。
 * 溜まった分は訪問者の痕跡を保持し続けることにもなるので、
 * 役目を終えた時点で消す。
 */
function cf_rate_limit_sweep(string $dir, int $windowSeconds): void {
    if (!is_dir($dir)) {
        return;
    }
    $now = time();
    foreach (glob(rtrim($dir, '/') . '/*.json') ?: [] as $file) {
        $mtime = @filemtime($file);
        if ($mtime !== false && $now - $mtime > $windowSeconds) {
            @unlink($file);
        }
    }
}

/** レート制限の記録を保存できる状態か。呼び出し側はこれを見てログを残す。 */
function cf_rate_limit_available(string $dir): bool {
    return is_dir($dir) && is_writable($dir);
}

/** IPが上限に達していないか調べる。カウントは増やさない。 */
function cf_rate_limit_check(string $ip, string $dir, int $max, int $windowSeconds, int $now): bool {
    // 保存先が使えないときは制限をかけずに通す（fail-open）。
    // ここで false を返すと、/tmp が壊れただけで全ての問い合わせが429で
    // 拒否され、フォームが完全に死ぬ。レート制限は多層防御の1枚に過ぎず、
    // これが落ちても honeypot・トークン・Turnstile・バリデーションは生きている。
    // 「問い合わせが1件も届かない」方が「洪水対策が1枚落ちる」より重大。
    // 呼び出し側が cf_rate_limit_available で検知してログを残すこと。
    if (!cf_rate_limit_available($dir)) {
        return true;
    }

    // 共有ロックを取ってから読む。cf_rate_limit_hit は ftruncate してから
    // 書くので、ロック無しで読むとその隙間で空ファイルを読み、
    // 「記録なし = 通す」と誤判定して制限をすり抜けられる。
    $handle = @fopen(cf_rate_file($ip, $dir), 'r');
    if ($handle === false) {
        return true;   // まだ記録が無い
    }
    $raw = false;
    try {
        if (flock($handle, LOCK_SH)) {
            $raw = stream_get_contents($handle);
            flock($handle, LOCK_UN);
        }
    } finally {
        fclose($handle);
    }
    if ($raw === false || $raw === '') {
        return true;
    }

    $saved = json_decode($raw, true);
    if (!is_array($saved) || !isset($saved['window_start'], $saved['count'])) {
        return true;
    }
    if ($now - (int)$saved['window_start'] >= $windowSeconds) {
        return true;   // 時間枠を過ぎているのでリセット扱い
    }

    return (int)$saved['count'] < $max;
}

/**
 * 1件消費したことを記録する。
 *
 * 呼ぶのはメール送信に成功したときだけ。入力ミスで弾かれた送信まで数えると、
 * 正当な利用者が自分の打ち間違いで締め出されてしまう。
 *
 * 読み書きを1つのロック内で行う。ロックを跨ぐと、同時に送信された分が
 * 同じカウントを読んで同じ値を書き、上限を超えて通ってしまう。
 */
function cf_rate_limit_hit(string $ip, string $dir, int $windowSeconds, int $now): void {
    $handle = @fopen(cf_rate_file($ip, $dir), 'c+');
    if ($handle === false) {
        error_log("contact: レート制限の記録に失敗しました（{$dir}）");
        return;
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            error_log('contact: レート制限ファイルのロックに失敗しました');
            return;
        }

        $raw = stream_get_contents($handle);
        $saved = json_decode($raw ?: '', true);

        $windowStart = $now;
        $count = 0;
        if (is_array($saved) && isset($saved['window_start'], $saved['count'])
            && $now - (int)$saved['window_start'] < $windowSeconds) {
            $windowStart = (int)$saved['window_start'];
            $count = (int)$saved['count'];
        }

        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode(['window_start' => $windowStart, 'count' => $count + 1]));
        fflush($handle);
        flock($handle, LOCK_UN);
    } finally {
        fclose($handle);
    }
}

/** Cloudflare が公開しているIPレンジ。 https://www.cloudflare.com/ips/ */
const CF_IP_RANGES = [
    '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
    '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
    '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
    '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
    '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32', '2405:b500::/32',
    '2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
];

/** IPが CIDR 表記の範囲に入るか。IPv4/IPv6 どちらも扱う。 */
function cf_cidr_match(string $ip, string $cidr): bool {
    $parts = explode('/', $cidr);
    if (count($parts) !== 2) {
        return false;
    }
    $ipBin = @inet_pton($ip);
    $netBin = @inet_pton($parts[0]);
    if ($ipBin === false || $netBin === false || strlen($ipBin) !== strlen($netBin)) {
        return false;
    }

    $bits = (int)$parts[1];
    $wholeBytes = intdiv($bits, 8);
    $restBits = $bits % 8;

    if ($wholeBytes > 0 && substr($ipBin, 0, $wholeBytes) !== substr($netBin, 0, $wholeBytes)) {
        return false;
    }
    if ($restBits > 0) {
        $mask = chr((0xFF << (8 - $restBits)) & 0xFF);
        if (($ipBin[$wholeBytes] & $mask) !== ($netBin[$wholeBytes] & $mask)) {
            return false;
        }
    }
    return true;
}

/**
 * そのIPが信頼するプロキシ（既定では Cloudflare）のものか。
 *
 * $ranges を渡せば設定から差し替えられる。Cloudflare がレンジを追加したとき、
 * コードを変更せず contact-config.php だけで追随できるようにするため。
 */
function cf_is_cloudflare_ip(string $ip, ?array $ranges = null): bool {
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }
    foreach ($ranges ?? CF_IP_RANGES as $range) {
        if (cf_cidr_match($ip, $range)) {
            return true;
        }
    }
    return false;
}

/**
 * プロキシのヘッダーは来ているのに、接続元が既知のレンジに無い状態か。
 *
 * true のときは (a) レンジ表が古い か (b) ヘッダーの詐称 のどちらか。
 * どちらも運用上気づきたいので、呼び出し側でログに残す。
 * 放置すると、全訪問者が Cloudflare の共有エッジIPとして同じレート制限枠を
 * 食い合い、5件で全員が締め出される。
 */
function cf_proxy_header_unrecognized(string $remoteAddr, string $forwardedIp, ?array $ranges = null): bool {
    return $forwardedIp !== ''
        && filter_var($forwardedIp, FILTER_VALIDATE_IP) !== false
        && !cf_is_cloudflare_ip($remoteAddr, $ranges);
}

/**
 * 訪問者の実IPを決める。
 *
 * CF-Connecting-IP は誰でも自由に付けられるヘッダーなので、無条件に信じると
 * オリジンへ直接アクセスできる相手にレート制限を毎回リセットされる。
 * 接続元が Cloudflare のときだけこのヘッダーを採用する。
 */
function cf_resolve_client_ip(string $remoteAddr, string $forwardedIp, ?array $ranges = null): string {
    if ($forwardedIp !== ''
        && filter_var($forwardedIp, FILTER_VALIDATE_IP)
        && cf_is_cloudflare_ip($remoteAddr, $ranges)) {
        return $forwardedIp;
    }
    return $remoteAddr;
}

/**
 * 設定ファイルの中身を検証する。項目名 => エラーメッセージ を返す。
 *
 * 手書きの contact-config.php はキーの打ち間違いや記入漏れが起きる。
 * 検証しないと後段で TypeError になり、JSONを名乗るHTMLエラーページが
 * 返ってフロント側が解釈できなくなる。
 */
function cf_validate_config(array $config): array {
    $errors = [];

    // テンプレートのプレースホルダのまま使われていないか
    $placeholders = ['ここに生成したランダム文字列を貼る', 'ここに Turnstile のシークレットキーを貼る', 'ここに Turnstile のサイトキーを貼る'];

    foreach (['mail_to', 'mail_from'] as $key) {
        $value = $config[$key] ?? null;
        if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$key] = "{$key} に有効なメールアドレスを設定してください";
        }
    }

    $secret = $config['form_secret'] ?? null;
    if (!is_string($secret) || trim($secret) === '' || in_array($secret, $placeholders, true)) {
        $errors['form_secret'] = 'form_secret にランダムな文字列を設定してください';
    }

    if (!empty($config['turnstile_enabled'])) {
        $ts = $config['turnstile_secret'] ?? null;
        if (!is_string($ts) || trim($ts) === '' || in_array($ts, $placeholders, true)) {
            $errors['turnstile_secret'] = 'turnstile_enabled が true のときは turnstile_secret が必要です';
        }
        // サイトキーはブラウザ側がウィジェットを出すのに必要。これが無いと
        // トークンを送れず、全ての送信が拒否される。
        $sk = $config['turnstile_sitekey'] ?? null;
        if (!is_string($sk) || trim($sk) === '' || in_array($sk, $placeholders, true)) {
            $errors['turnstile_sitekey'] = 'turnstile_enabled が true のときは turnstile_sitekey が必要です';
        }
    }

    return $errors;
}
