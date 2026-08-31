# お問い合わせフォーム スパム対策 設計

**Date**: 2026-08-31
**Status**: Approved（口頭承認済み）
**Scope**: `astro/src/components/Form.astro` のみ

## 目的

1. フォーム経由のボットスパム送信を防ぐ
2. `mailto:` によるメールアドレスのHTML露出（アドレス収集ボット）をなくす

## 方式

Formspree（無料枠 50件/月）を採用。見た目は現行フォームのまま変更しない。

### 変更点

| 項目 | 内容 |
|------|------|
| 送信先 | `mailto:contact@sunyutech.jp` → `https://formspree.io/f/{FORM_ID}`（POST） |
| ハニーポット | Formspree 標準の `_gotcha` 隠しフィールドを追加（CSSで画面外配置、ボットが入力すると自動破棄） |
| 件名 | `_subject` 隠しフィールドで「サンユウテックHP お問い合わせ」を指定 |
| 送信UX | インラインJSで fetch 送信。成功時はフォームを隠して日本語の完了メッセージ、失敗時は電話番号つきエラーメッセージをページ内表示（Formspreeの英語完了画面には飛ばさない） |
| 送信中 | ボタンをdisabled + 「送信中…」表示で二重送信防止 |

### スパム防御の層

1. Formspree サーバー側スパムフィルタ（常時有効）
2. `_gotcha` ハニーポット
3. メールアドレスがHTMLから消えることでアドレス収集を遮断

## 手作業（サイト管理者）

1. https://formspree.io で無料アカウント作成（受信先: contact@sunyutech.jp）
2. New Form を作成しフォームID（`f/` の後の英数字）を取得
3. `Form.astro` 冒頭の `FORMSPREE_ID` 定数を差し替え

## 対象外

- reCAPTCHA/Turnstile の導入（スパムが実際に増えた場合の追加策として保留）
- 受信メールソフト側の振り分け設定

## 付録: 実装済みコード（未適用・yukiさん引き継ぎ用）

本設計を実装した Form.astro の全文。フォーム送信は yuki さん担当となったため working tree からは差し戻し済み。採用する場合は以下をそのまま使用可（FORMSPREE_ID の差し替えのみ必要）。

```astro
---
// Formspree のフォームID。https://formspree.io でアカウント作成後、
// New Form で発行される ID（https://formspree.io/f/XXXX の XXXX 部分）に差し替える。
const FORMSPREE_ID = 'YOUR_FORM_ID';
---
<form class="contact-form" action={`https://formspree.io/f/${FORMSPREE_ID}`} method="POST">
  <div class="field">
    <label for="company">会社名 <span aria-label="必須">*</span></label>
    <input type="text" id="company" name="company" required />
  </div>
  <div class="field">
    <label for="name">ご担当者名 <span aria-label="必須">*</span></label>
    <input type="text" id="name" name="name" required />
  </div>
  <div class="field">
    <label for="tel">電話番号 <span aria-label="必須">*</span></label>
    <input type="tel" id="tel" name="tel" required />
  </div>
  <div class="field">
    <label for="email">メールアドレス <span aria-label="必須">*</span></label>
    <input type="email" id="email" name="email" required />
  </div>
  <div class="field">
    <label for="method">工事種別</label>
    <select id="method" name="method">
      <option value="">選択してください</option>
      <option value="舗装">舗装</option>
      <option value="WJ">ウォータージェット</option>
      <option value="コンクリート補修">コンクリート補修</option>
      <option value="その他">その他</option>
    </select>
  </div>
  <div class="field">
    <label for="detail">工事規模・場所・希望時期</label>
    <textarea id="detail" name="detail" rows="4"></textarea>
  </div>
  <div class="field">
    <label for="message">お問い合わせ内容</label>
    <textarea id="message" name="message" rows="6"></textarea>
  </div>
  {/* ハニーポット: 人間には見えず、ボットが入力すると Formspree 側で破棄される */}
  <div class="gotcha" aria-hidden="true">
    <label for="_gotcha">このフィールドは空のままにしてください</label>
    <input type="text" id="_gotcha" name="_gotcha" tabindex="-1" autocomplete="off" />
  </div>
  <input type="hidden" name="_subject" value="サンユウテックHP お問い合わせ" />
  <button type="submit" class="submit">送信する</button>
  <p class="form-status" role="status" aria-live="polite" hidden></p>
</form>
<style>
  .contact-form { display: flex; flex-direction: column; gap: var(--sp-6); max-width: 680px; }
  .field { display: flex; flex-direction: column; gap: var(--sp-2); }
  label { font-weight: var(--fw-semibold); font-size: var(--fs-sm); color: var(--color-navy-900); }
  label span { color: var(--color-status-error); margin-left: var(--sp-1); font-weight: var(--fw-bold); }
  input, select, textarea {
    padding: var(--sp-3);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-sm);
    font-family: var(--font-base);
    font-size: var(--fs-base);
    color: var(--color-gray-900);
    background: var(--color-white);
  }
  input:focus, select:focus, textarea:focus {
    outline: 2px solid var(--color-orange-600);
    outline-offset: 1px;
    border-color: var(--color-navy-900);
  }
  .gotcha {
    position: absolute;
    left: -9999px;
    width: 1px;
    height: 1px;
    overflow: hidden;
  }
  .submit {
    background: var(--color-orange-600);
    color: var(--color-white);
    font-weight: var(--fw-bold);
    border: none;
    padding: var(--sp-4) var(--sp-8);
    border-radius: var(--radius-md);
    align-self: flex-start;
  }
  .submit:hover { background: var(--color-orange-700); }
  .submit:disabled { opacity: 0.6; cursor: not-allowed; }
  .form-status { font-size: var(--fs-base); font-weight: var(--fw-semibold); }
  .form-status.is-success { color: var(--color-navy-900); }
  .form-status.is-error { color: var(--color-status-error); }
</style>
<script>
  const form = document.querySelector<HTMLFormElement>('.contact-form');
  const status = form?.querySelector<HTMLParagraphElement>('.form-status');
  const submit = form?.querySelector<HTMLButtonElement>('.submit');

  const SUCCESS_MSG = '送信しました。担当者よりご連絡いたします。';
  const ERROR_MSG = '送信に失敗しました。お手数ですが、お電話（092-555-9211）でご連絡ください。';

  function showStatus(message: string, type: 'success' | 'error') {
    if (!status) return;
    status.textContent = message;
    status.classList.remove('is-success', 'is-error');
    status.classList.add(type === 'success' ? 'is-success' : 'is-error');
    status.hidden = false;
  }

  form?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!submit) return;

    submit.disabled = true;
    const originalLabel = submit.textContent;
    submit.textContent = '送信中…';

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json' },
      });
      if (!response.ok) throw new Error(`Formspree responded ${response.status}`);
      form.reset();
      showStatus(SUCCESS_MSG, 'success');
      submit.hidden = true;
    } catch {
      showStatus(ERROR_MSG, 'error');
      submit.disabled = false;
      submit.textContent = originalLabel;
    }
  });
</script>
```
