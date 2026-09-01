<?php
/**
 * contact-lib.php のテスト。
 *
 * 実行: php astro/tests/contact-lib.test.php
 *
 * PHPUnit / Composer を使わないのは、レンタルサーバーに置くファイルを
 * 依存ゼロに保つため。テストランナーもこのファイル内で完結させる。
 */

require_once __DIR__ . '/../public/contact-lib.php';

// ---- 最小テストランナー ----------------------------------------------------

$GLOBALS['tests_run'] = 0;
$GLOBALS['tests_failed'] = 0;

function test(string $name, callable $fn): void {
    $GLOBALS['tests_run']++;
    try {
        $fn();
        echo "  ok   $name\n";
    } catch (Throwable $e) {
        $GLOBALS['tests_failed']++;
        echo "  FAIL $name\n";
        echo "       " . $e->getMessage() . "\n";
    }
}

function assert_same($expected, $actual, string $msg = ''): void {
    if ($expected !== $actual) {
        throw new Exception(sprintf(
            "%sexpected %s, got %s",
            $msg ? "$msg: " : '',
            var_export($expected, true),
            var_export($actual, true)
        ));
    }
}

function assert_true($actual, string $msg = ''): void {
    assert_same(true, $actual, $msg);
}

function assert_false($actual, string $msg = ''): void {
    assert_same(false, $actual, $msg);
}

function assert_contains(string $needle, string $haystack, string $msg = ''): void {
    if (strpos($haystack, $needle) === false) {
        throw new Exception(sprintf(
            "%sexpected to find %s in %s",
            $msg ? "$msg: " : '',
            var_export($needle, true),
            var_export($haystack, true)
        ));
    }
}

function assert_not_contains(string $needle, string $haystack, string $msg = ''): void {
    if (strpos($haystack, $needle) !== false) {
        throw new Exception(sprintf(
            "%sexpected NOT to find %s in %s",
            $msg ? "$msg: " : '',
            var_export($needle, true),
            var_export($haystack, true)
        ));
    }
}

// 有効な入力の雛形。個別テストで必要な項目だけ上書きする。
function valid_input(array $overrides = []): array {
    return array_merge([
        'name'    => '山田 太郎',
        'email'   => 'yamada@example.com',
        'tel'     => '092-000-0000',
        'message' => '見積をお願いします。',
    ], $overrides);
}

// ---- メールヘッダーインジェクション対策 ------------------------------------
// これが破れると、フォームが第三者へのスパム送信の踏み台にされる。

echo "\ncf_sanitize_header\n";

test('改行(LF)を除去する', function () {
    assert_same('山田太郎', cf_sanitize_header("山田\n太郎"));
});

test('復帰(CR)を除去する', function () {
    assert_same('山田太郎', cf_sanitize_header("山田\r太郎"));
});

test('CRLF で注入された Bcc ヘッダーを無害化する', function () {
    $attack = "yamada@example.com\r\nBcc: victim@example.com";
    $result = cf_sanitize_header($attack);
    assert_not_contains("\r", $result, 'CR が残っている');
    assert_not_contains("\n", $result, 'LF が残っている');
});

test('NULLバイトを除去する', function () {
    assert_same('abc', cf_sanitize_header("a\0bc"));
});

test('前後の空白を除去する', function () {
    assert_same('山田 太郎', cf_sanitize_header('  山田 太郎  '));
});

// ---- 入力バリデーション ----------------------------------------------------

echo "\ncf_validate\n";

test('正しい入力ならエラーなし', function () {
    assert_same([], cf_validate(valid_input()));
});

test('お名前が空ならエラー', function () {
    $errors = cf_validate(valid_input(['name' => '']));
    assert_same(['name'], array_keys($errors));
});

test('お名前が空白のみならエラー', function () {
    $errors = cf_validate(valid_input(['name' => '   ']));
    assert_same(['name'], array_keys($errors));
});

test('電話番号は任意なので空でも通る', function () {
    // 応募者が携帯しか持っていない、日中出られない等で詰まらせない。
    // 連絡手段はメールで確保されている。
    assert_same([], cf_validate(valid_input(['tel' => ''])));
});

test('お問い合わせ内容が空ならエラー', function () {
    // 以前は任意だったが、これがフォームの本体なので必須にする。
    $errors = cf_validate(valid_input(['message' => '']));
    assert_same(['message'], array_keys($errors));
});

test('メールアドレスが空ならエラー', function () {
    $errors = cf_validate(valid_input(['email' => '']));
    assert_same(['email'], array_keys($errors));
});

test('メールアドレスの形式が不正ならエラー', function () {
    $errors = cf_validate(valid_input(['email' => 'not-an-email']));
    assert_same(['email'], array_keys($errors));
});

test('必須が複数欠けたら全部返す', function () {
    $errors = cf_validate(valid_input(['name' => '', 'message' => '']));
    assert_same(['name', 'message'], array_keys($errors));
});

test('会社名の項目は存在しない（求職者が送信できなくなるため）', function () {
    // 会社名を必須にしていたせいで、採用ページのCTAから来た応募者は
    // フォームを送信できなかった。項目ごと廃止し、必要なら本文に書いてもらう。
    assert_false(array_key_exists('company', cf_field_limits()));
    assert_same(['name', 'email', 'tel', 'message'], array_keys(cf_field_limits()));
});

test('配列を送りつけられても警告を出さずエラーにする', function () {
    // company[]=A のように配列で送ると (string) キャストが
    // "Array to string conversion" 警告を出し、その出力が JSON 応答の
    // 前に混ざってフロント側が解釈できなくなる。
    $errors = cf_validate(valid_input(['name' => ['A', 'B']]));
    assert_same(['name'], array_keys($errors));
});

test('メール本文の組み立ても配列入力で壊れない', function () {
    $mail = cf_build_mail(valid_input(['name' => ['A']]), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_not_contains('Array', $mail['subject']);
});

test('ハニーポットに配列を入れられてもボット判定できる', function () {
    assert_true(cf_is_bot_honeypot(['_gotcha' => ['x']]));
});

test('お問い合わせ内容が長すぎるとエラー', function () {
    $errors = cf_validate(valid_input(['message' => str_repeat('あ', 5001)]));
    assert_same(['message'], array_keys($errors));
});

test('お名前が長すぎるとエラー', function () {
    $errors = cf_validate(valid_input(['name' => str_repeat('あ', 101)]));
    assert_same(['name'], array_keys($errors));
});

// ---- ハニーポット ----------------------------------------------------------

echo "\ncf_is_bot_honeypot\n";

test('隠しフィールドが空なら人間とみなす', function () {
    assert_false(cf_is_bot_honeypot(['_gotcha' => '']));
});

test('隠しフィールドが存在しなくても人間とみなす', function () {
    assert_false(cf_is_bot_honeypot([]));
});

test('隠しフィールドに入力があればボットとみなす', function () {
    assert_true(cf_is_bot_honeypot(['_gotcha' => 'http://spam.example.com']));
});

test('隠しフィールドが空白のみならボットとみなさない', function () {
    assert_false(cf_is_bot_honeypot(['_gotcha' => '   ']));
});

// ---- 送信時間トラップ ------------------------------------------------------
// フォーム表示時刻を署名付きで埋め込み、速すぎる/古すぎる送信を弾く。

echo "\ncf_make_form_token / cf_check_form_token\n";

const TEST_SECRET = 'test-secret-key';
const MIN_SECONDS = 3;
const MAX_SECONDS = 3600;

test('発行直後のトークンは、3秒待てば通る', function () {
    $issued = 1000000;
    $token = cf_make_form_token($issued, TEST_SECRET);
    assert_true(cf_check_form_token($token, $issued + 5, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
});

test('3秒未満で送信されたら弾く（ボットは即座に送信する）', function () {
    $issued = 1000000;
    $token = cf_make_form_token($issued, TEST_SECRET);
    assert_false(cf_check_form_token($token, $issued + 1, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
});

test('境界: ちょうど3秒後は通る', function () {
    $issued = 1000000;
    $token = cf_make_form_token($issued, TEST_SECRET);
    assert_true(cf_check_form_token($token, $issued + 3, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
});

test('1時間を超えて古いトークンは弾く', function () {
    $issued = 1000000;
    $token = cf_make_form_token($issued, TEST_SECRET);
    assert_false(cf_check_form_token($token, $issued + 3601, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
});

test('署名が改ざんされたトークンは弾く', function () {
    $issued = 1000000;
    $token = cf_make_form_token($issued, TEST_SECRET);
    // タイムスタンプだけ書き換えて、時間トラップをすり抜けようとする
    $parts = explode('.', $token);
    $forged = ($issued - 100) . '.' . $parts[1];
    assert_false(cf_check_form_token($forged, $issued + 5, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
});

test('別の秘密鍵で作られたトークンは弾く', function () {
    $issued = 1000000;
    $token = cf_make_form_token($issued, 'other-secret');
    assert_false(cf_check_form_token($token, $issued + 5, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
});

test('形式が壊れたトークンは弾く', function () {
    assert_false(cf_check_form_token('garbage', 1000005, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
    assert_false(cf_check_form_token('', 1000005, TEST_SECRET, MIN_SECONDS, MAX_SECONDS));
});

// ---- Cloudflare Turnstile --------------------------------------------------
// 実際のHTTP通信は呼び出し側から渡す（テストではダミーを差し込む）。

echo "\ncf_verify_turnstile\n";

test('Cloudflare が success:true を返せば通す', function () {
    $http = fn($url, $fields) => json_encode(['success' => true]);
    assert_true(cf_verify_turnstile('token', 'secret', '1.2.3.4', $http));
});

test('Cloudflare が success:false を返せば弾く', function () {
    $http = fn($url, $fields) => json_encode(['success' => false, 'error-codes' => ['invalid-input-response']]);
    assert_false(cf_verify_turnstile('token', 'secret', '1.2.3.4', $http));
});

test('トークンが空なら通信せずに弾く', function () {
    $called = false;
    $http = function () use (&$called) { $called = true; return json_encode(['success' => true]); };
    assert_false(cf_verify_turnstile('', 'secret', '1.2.3.4', $http));
    assert_false($called, 'トークンが空なのに Cloudflare へ問い合わせている');
});

test('通信が失敗したら弾く（フェイルクローズ）', function () {
    $http = fn($url, $fields) => false;
    assert_false(cf_verify_turnstile('token', 'secret', '1.2.3.4', $http));
});

test('応答がJSONとして壊れていたら弾く', function () {
    $http = fn($url, $fields) => 'not json';
    assert_false(cf_verify_turnstile('token', 'secret', '1.2.3.4', $http));
});

test('シークレットと訪問者IPを Cloudflare に渡している', function () {
    $seen = null;
    $http = function ($url, $fields) use (&$seen) {
        $seen = $fields;
        return json_encode(['success' => true]);
    };
    cf_verify_turnstile('tok', 'sec', '1.2.3.4', $http);
    assert_same('sec', $seen['secret']);
    assert_same('tok', $seen['response']);
    assert_same('1.2.3.4', $seen['remoteip']);
});

// ---- メール組み立て --------------------------------------------------------

echo "\ncf_build_mail\n";

test('宛先は設定した受信アドレス', function () {
    $mail = cf_build_mail(valid_input(), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_same('inbox@example.jp', $mail['to']);
});

test('From は自社ドメイン（SPF を通すため訪問者アドレスは使わない）', function () {
    $mail = cf_build_mail(valid_input(), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains('From: ', $mail['headers']);
    assert_contains('no-reply@sunyutech.jp', $mail['headers']);
    assert_not_contains('From: yamada@example.com', $mail['headers']);
});

test('Reply-To は訪問者のアドレス（返信でそのまま届く）', function () {
    $mail = cf_build_mail(valid_input(), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains('Reply-To: yamada@example.com', $mail['headers']);
});

test('本文に入力項目がすべて含まれる', function () {
    $mail = cf_build_mail(valid_input(), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains('山田 太郎', $mail['body']);
    assert_contains('yamada@example.com', $mail['body']);
    assert_contains('092-000-0000', $mail['body']);
    assert_contains('見積をお願いします。', $mail['body']);
});

test('件名にお名前が入る（会社名の項目が無くなったため）', function () {
    $mail = cf_build_mail(valid_input(), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains('山田 太郎', $mail['subject']);
});

test('お名前が空でも件名が組み立てられる', function () {
    $mail = cf_build_mail(valid_input(['name' => '']), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains('【HP問い合わせ】', $mail['subject']);
});

test('任意項目が空でも本文は組み立てられる', function () {
    $mail = cf_build_mail(valid_input(['tel' => '']), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains('山田 太郎', $mail['body']);
});

test('Content-Type と MIME-Version は自前で付けない', function () {
    // mb_send_mail が自分で Content-Type と Content-Transfer-Encoding を組み立てる。
    // 自前で重ねると、ヘッダーが二重になったり、本文がBASE64なのにヘッダーが
    // それを示さない状態になってメールソフトで文字化けする。
    $mail = cf_build_mail(valid_input(), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_not_contains('Content-Type', $mail['headers']);
    assert_not_contains('MIME-Version', $mail['headers']);
});

test('定義外の項目が送られても本文から消えない', function () {
    // デプロイの入れ替わり中に、古いHTMLを開いたままの訪問者が
    // company / method / detail を送ってくることがある。CF_FIELDS だけを
    // 見て組み立てると、それらが黙って捨てられ、受け取る側は
    // 情報が欠けたことにすら気づけない。
    $input = valid_input(['company' => '株式会社サンプル', 'method' => '舗装']);
    $mail = cf_build_mail($input, 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains('株式会社サンプル', $mail['body']);
    assert_contains('舗装', $mail['body']);
});

test('定義外の項目にも長さ制限がかかる', function () {
    // 上限を掛けないと、message の5000文字制限を別のキー名で送るだけで
    // 回避でき、受信箱にメガバイト級の本文が届く。
    $mail = cf_build_mail(valid_input(['spam' => str_repeat('A', 200000)]), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_true(mb_strlen($mail['body']) < 5000, '本文が ' . mb_strlen($mail['body']) . ' 文字ある');
    assert_contains('（以下省略）', $mail['body']);
});

test('旧detail相当の長文は切られずに届く', function () {
    // extras は「デプロイ中に旧HTMLから届いた入力を救う」ための仕組み。
    // 旧 detail は2000文字まで許容していたので、そこを切ると
    // 救うはずの内容そのものを失う。
    $body = str_repeat('あ', 800);
    $mail = cf_build_mail(valid_input(['detail' => $body]), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_contains($body, $mail['body']);
    assert_not_contains('（以下省略）', $mail['body']);
});

test('合計の上限を超えたら以降は載せない', function () {
    // 1項目あたりを緩めた分、合計で歯止めをかける。
    $input = valid_input();
    for ($i = 0; $i < 10; $i++) {
        $input["extra{$i}"] = str_repeat('X', 1500);
    }
    $mail = cf_build_mail($input, 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_true(mb_strlen($mail['body']) < 6000, '本文が ' . mb_strlen($mail['body']) . ' 文字ある');
});

test('定義外の項目は個数にも上限がある', function () {
    $input = valid_input();
    for ($i = 0; $i < 100; $i++) {
        $input["extra{$i}"] = "値{$i}";
    }
    $mail = cf_build_mail($input, 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_true(substr_count($mail['body'], 'extra') <= 10, '項目が多すぎる');
});

test('異常に長いキー名は本文に出さない', function () {
    $mail = cf_build_mail(valid_input([str_repeat('k', 500) => '値']), 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_not_contains('kkkkkkkkkk', $mail['body']);
});

test('定義外でも空の項目は本文に出さない', function () {
    $input = valid_input(['company' => '']);
    $mail = cf_build_mail($input, 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_not_contains('company', $mail['body']);
});

test('内部用の項目は本文に出さない', function () {
    // _token / _gotcha / Turnstile のトークンは受信者にとって不要
    $input = valid_input(['_token' => 'abc.def', '_gotcha' => '', 'cf-turnstile-response' => 'xyz']);
    $mail = cf_build_mail($input, 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_not_contains('abc.def', $mail['body']);
    assert_not_contains('xyz', $mail['body']);
});

test('Reply-To に改行が注入されても無害化される', function () {
    $input = valid_input(['email' => "yamada@example.com\r\nBcc: victim@example.com"]);
    $mail = cf_build_mail($input, 'inbox@example.jp', 'no-reply@sunyutech.jp');
    assert_not_contains('Bcc:', $mail['headers']);
});

test('件名に改行が注入されても新しいヘッダーにならない', function () {
    $input = valid_input(['name' => "サンプル\r\nBcc: victim@example.com"]);
    $mail = cf_build_mail($input, 'inbox@example.jp', 'no-reply@sunyutech.jp');
    // 防御の本体は「改行が残らないこと」。改行さえ消えていれば "Bcc: ..." は
    // ヘッダーではなく件名の一部の文字列にしかならず、無害。
    assert_not_contains("\r", $mail['subject']);
    assert_not_contains("\n", $mail['subject']);
});

// ---- メール送信（エンベロープ送信者のフォールバック）------------------------
//
// -f でエンベロープ送信者を指定すると SPF/DMARC のアライメントが取れるが、
// 共用サーバーによってはこの指定自体が拒否される。そのとき送信を諦めると
// 問い合わせが1通も届かなくなるため、-f 無しで送り直す。

echo "\ncf_send_mail\n";

test('1回目で成功したら送り直さない', function () {
    $calls = [];
    $sender = function ($to, $subj, $body, $headers, $params = null) use (&$calls) {
        $calls[] = $params;
        return true;
    };
    assert_true(cf_send_mail(['to'=>'a@b.jp','subject'=>'s','body'=>'b','headers'=>'h'], 'no-reply@sunyutech.jp', $sender));
    assert_same(1, count($calls), '送信回数');
    assert_same('-fno-reply@sunyutech.jp', $calls[0]);
});

test('-f が拒否されたら -f 無しで送り直す', function () {
    $calls = [];
    $sender = function ($to, $subj, $body, $headers, $params = null) use (&$calls) {
        $calls[] = $params;
        return $params === null;   // -f 付きは失敗、無しは成功
    };
    assert_true(cf_send_mail(['to'=>'a@b.jp','subject'=>'s','body'=>'b','headers'=>'h'], 'no-reply@sunyutech.jp', $sender));
    assert_same(2, count($calls), '送信回数');
    assert_same('-fno-reply@sunyutech.jp', $calls[0]);
    assert_same(null, $calls[1]);
});

test('どちらでも送れなければ false', function () {
    $sender = fn($to, $subj, $body, $headers, $params = null) => false;
    assert_false(cf_send_mail(['to'=>'a@b.jp','subject'=>'s','body'=>'b','headers'=>'h'], 'no-reply@sunyutech.jp', $sender));
});

// ---- レート制限 ------------------------------------------------------------
//
// 「上限に達しているか」の判定(check)と「1件消費する」(hit)を分ける。
// 入力ミスで弾かれた送信まで数えると、正当な利用者が締め出されるため、
// hit はメール送信に成功したときだけ呼ぶ。

echo "\ncf_rate_limit_check / cf_rate_limit_hit\n";

$rate_dir = sys_get_temp_dir() . '/cf-rate-test-' . getmypid();
@mkdir($rate_dir, 0700, true);

test('何も記録が無ければ通す', function () use ($rate_dir) {
    assert_true(cf_rate_limit_check('10.0.0.1', $rate_dir, 5, 3600, 1000000));
});

test('check だけではカウントが増えない', function () use ($rate_dir) {
    $ip = '10.0.0.9';
    for ($i = 0; $i < 20; $i++) {
        assert_true(cf_rate_limit_check($ip, $rate_dir, 5, 3600, 1000000), "{$i}回目のcheckで弾かれた");
    }
});

test('hit を上限回数だけ呼ぶと、次の check で弾かれる', function () use ($rate_dir) {
    $ip = '10.0.0.2';
    $now = 1000000;
    for ($i = 0; $i < 5; $i++) {
        assert_true(cf_rate_limit_check($ip, $rate_dir, 5, 3600, $now + $i), "{$i}回目のcheckで弾かれた");
        cf_rate_limit_hit($ip, $rate_dir, 3600, $now + $i);
    }
    assert_false(cf_rate_limit_check($ip, $rate_dir, 5, 3600, $now + 5));
});

test('時間枠を過ぎればリセットされる', function () use ($rate_dir) {
    $ip = '10.0.0.3';
    $now = 1000000;
    for ($i = 0; $i < 5; $i++) {
        cf_rate_limit_hit($ip, $rate_dir, 3600, $now + $i);
    }
    assert_false(cf_rate_limit_check($ip, $rate_dir, 5, 3600, $now + 10));
    assert_true(cf_rate_limit_check($ip, $rate_dir, 5, 3600, $now + 3601));
});

test('IPごとに独立してカウントする', function () use ($rate_dir) {
    $now = 1000000;
    for ($i = 0; $i < 5; $i++) {
        cf_rate_limit_hit('10.0.0.4', $rate_dir, 3600, $now + $i);
    }
    assert_false(cf_rate_limit_check('10.0.0.4', $rate_dir, 5, 3600, $now + 5));
    assert_true(cf_rate_limit_check('10.0.0.5', $rate_dir, 5, 3600, $now + 5));
});

test('IPが記録ファイル名に化けないこと（パストラバーサル対策）', function () use ($rate_dir) {
    cf_rate_limit_hit('../../etc/passwd', $rate_dir, 3600, 1000000);
    $escaped = glob($rate_dir . '/../*');
    assert_same([], array_filter($escaped ?: [], fn($p) => strpos(basename($p), 'passwd') !== false));
});

test('保存先が使えるかを判定できる', function () use ($rate_dir) {
    assert_true(cf_rate_limit_available($rate_dir));
    assert_false(cf_rate_limit_available('/nonexistent-dir-for-test'));
});

test('保存先が使えないときは制限をかけずに通す（fail-open）', function () {
    // 当初は fail-close にしていたが、それだと保存先が壊れた瞬間に
    // 全ての問い合わせが429で拒否され、フォームが完全に死ぬ。
    // レート制限は多層防御の1枚であり、これが落ちても honeypot・
    // トークン・Turnstile・バリデーションは生きている。
    // 「問い合わせが届かない」方が「洪水対策が1枚落ちる」より重大なので、
    // 通した上で呼び出し側がエラーログを残す設計にする。
    assert_true(cf_rate_limit_check('10.0.0.6', '/nonexistent-dir-for-test', 5, 3600, 1000000));
});

// 後片付け
array_map('unlink', glob($rate_dir . '/*') ?: []);
@rmdir($rate_dir);

// ---- レート制限ファイルの掃除 ----------------------------------------------
//
// IP のハッシュ1件につき1ファイルを作るため、掃除しないと際限なく溜まる。
// 溜まった分は個人情報（IPの痕跡）を保持し続けることにもなる。

echo "\ncf_rate_limit_sweep\n";

test('時間枠を過ぎた記録は削除される', function () {
    $dir = sys_get_temp_dir() . '/cf-sweep-' . getmypid();
    @mkdir($dir, 0700, true);
    $old = $dir . '/' . sha1('old') . '.json';
    $new = $dir . '/' . sha1('new') . '.json';
    file_put_contents($old, '{}');
    file_put_contents($new, '{}');
    touch($old, time() - 7200);

    cf_rate_limit_sweep($dir, 3600);

    assert_false(file_exists($old), '古い記録が残っている');
    assert_true(file_exists($new), '新しい記録が消えている');

    array_map('unlink', glob($dir . '/*') ?: []);
    @rmdir($dir);
});

test('保存先が無くても落ちない', function () {
    cf_rate_limit_sweep('/nonexistent-dir-for-sweep', 3600);
    assert_true(true);
});

test('自分が作った記録以外は消さない', function () {
    // rate_limit_dir は設定で任意のパスを指定できる。他の用途と共用の
    // ディレクトリを指されたとき、無関係なファイルを消してはいけない。
    $dir = sys_get_temp_dir() . '/cf-sweep-safe-' . getmypid();
    @mkdir($dir, 0700, true);
    $mine = $dir . '/' . sha1('someip') . '.json';
    $other = $dir . '/important-backup.json';
    file_put_contents($mine, '{}');
    file_put_contents($other, '{}');
    touch($mine, time() - 7200);
    touch($other, time() - 7200);

    cf_rate_limit_sweep($dir, 3600);

    assert_false(file_exists($mine), '自分の記録が残っている');
    assert_true(file_exists($other), '無関係なファイルを消している');

    array_map('unlink', glob($dir . '/*') ?: []);
    @rmdir($dir);
});

// ---- Cloudflare からのリクエストか判定 --------------------------------------
//
// CF-Connecting-IP は誰でも自由に付けられるヘッダー。オリジンに直接
// アクセスされた場合、これを信じるとレート制限を毎回リセットされる。
// REMOTE_ADDR が Cloudflare のIPレンジ内のときだけ信頼する。

echo "\ncf_is_cloudflare_ip / cf_resolve_client_ip\n";

test('Cloudflare のIPレンジ内なら true', function () {
    assert_true(cf_is_cloudflare_ip('173.245.48.1'));
    assert_true(cf_is_cloudflare_ip('104.16.0.1'));
    assert_true(cf_is_cloudflare_ip('172.64.0.1'));
});

test('無関係なIPなら false', function () {
    assert_false(cf_is_cloudflare_ip('8.8.8.8'));
    assert_false(cf_is_cloudflare_ip('192.168.1.1'));
    assert_false(cf_is_cloudflare_ip('1.2.3.4'));
});

test('IPv6 の Cloudflare レンジも判定できる', function () {
    assert_true(cf_is_cloudflare_ip('2400:cb00::1'));
    assert_false(cf_is_cloudflare_ip('2001:4860:4860::8888'));
});

test('壊れた値は false', function () {
    assert_false(cf_is_cloudflare_ip('not-an-ip'));
    assert_false(cf_is_cloudflare_ip(''));
});

test('Cloudflare 経由なら CF-Connecting-IP を採用する', function () {
    assert_same('203.0.113.7', cf_resolve_client_ip('173.245.48.1', '203.0.113.7'));
});

test('Cloudflare 経由でなければヘッダーを無視して REMOTE_ADDR を使う', function () {
    // 攻撃者がヘッダーを詐称してレート制限を回避しようとするケース
    assert_same('8.8.8.8', cf_resolve_client_ip('8.8.8.8', '203.0.113.7'));
});

test('ヘッダーが無ければ REMOTE_ADDR を使う', function () {
    assert_same('173.245.48.1', cf_resolve_client_ip('173.245.48.1', ''));
});

test('ヘッダーの値がIPとして不正なら REMOTE_ADDR を使う', function () {
    assert_same('173.245.48.1', cf_resolve_client_ip('173.245.48.1', 'garbage'));
});

test('信頼するレンジを設定から差し替えられる', function () {
    // Cloudflare がレンジを追加したとき、コードを変えずに設定で追随できる。
    // 追随できないと REMOTE_ADDR（=Cloudflareの共有エッジIP）で
    // レート制限をかけることになり、全訪問者が同じ枠を食い合う。
    assert_same('203.0.113.7', cf_resolve_client_ip('198.51.100.5', '203.0.113.7', ['198.51.100.0/24']));
    assert_same('198.51.100.5', cf_resolve_client_ip('198.51.100.5', '203.0.113.7', ['10.0.0.0/8']));
});

test('レンジ表が古いかどうかを判定できる', function () {
    // ヘッダーは来ているのに接続元が既知のレンジ外 = 表が古い可能性。
    // 呼び出し側がログに残せるようにする。
    assert_true(cf_proxy_header_unrecognized('8.8.8.8', '203.0.113.7'));
    assert_false(cf_proxy_header_unrecognized('173.245.48.1', '203.0.113.7'));
    assert_false(cf_proxy_header_unrecognized('8.8.8.8', ''));
});

// ---- 設定ファイルの検証 ----------------------------------------------------
//
// 手書きの contact-config.php はキーの打ち間違いが起きる。放置すると
// TypeError で HTML のエラーページが返り、フロント側が解釈できなくなる。

echo "\ncf_validate_config\n";

function full_config(array $overrides = []): array {
    return array_merge([
        'mail_to'           => 'inbox@example.jp',
        'mail_from'         => 'no-reply@sunyutech.jp',
        'form_secret'       => 'a-long-random-secret',
        'turnstile_enabled' => false,
        'turnstile_secret'  => '',
    ], $overrides);
}

test('必要なキーが揃っていれば問題なし', function () {
    assert_same([], cf_validate_config(full_config()));
});

test('mail_to が無ければエラー', function () {
    $c = full_config(); unset($c['mail_to']);
    assert_same(['mail_to'], array_keys(cf_validate_config($c)));
});

test('form_secret が空ならエラー', function () {
    assert_same(['form_secret'], array_keys(cf_validate_config(full_config(['form_secret' => '']))));
});

test('form_secret がテンプレートのままならエラー', function () {
    $c = full_config(['form_secret' => 'ここに生成したランダム文字列を貼る']);
    assert_same(['form_secret'], array_keys(cf_validate_config($c)));
});

test('mail_to がメールアドレスの形式でなければエラー', function () {
    assert_same(['mail_to'], array_keys(cf_validate_config(full_config(['mail_to' => 'not-an-email']))));
});

test('Turnstile 有効なのにシークレットが空ならエラー', function () {
    $c = full_config(['turnstile_enabled' => true, 'turnstile_secret' => '', 'turnstile_sitekey' => 'k']);
    assert_same(['turnstile_secret'], array_keys(cf_validate_config($c)));
});

test('Turnstile 有効なのにサイトキーが空ならエラー', function () {
    // サイトキーはブラウザ側が必要とする。無いとトークンを送れず
    // 全送信が拒否されるため、シークレットと同様に必須。
    $c = full_config(['turnstile_enabled' => true, 'turnstile_secret' => 's', 'turnstile_sitekey' => '']);
    assert_same(['turnstile_sitekey'], array_keys(cf_validate_config($c)));
});

test('Turnstile 有効で両方揃っていれば問題なし', function () {
    $c = full_config(['turnstile_enabled' => true, 'turnstile_secret' => 's', 'turnstile_sitekey' => 'k']);
    assert_same([], cf_validate_config($c));
});

test('Turnstile 無効ならシークレットが空でも問題なし', function () {
    $c = full_config(['turnstile_enabled' => false, 'turnstile_secret' => '']);
    assert_same([], cf_validate_config($c));
});

// ---- 結果 -----------------------------------------------------------------

echo "\n";
echo str_repeat('-', 50) . "\n";
printf("%d tests, %d failures\n", $GLOBALS['tests_run'], $GLOBALS['tests_failed']);
exit($GLOBALS['tests_failed'] > 0 ? 1 : 0);
