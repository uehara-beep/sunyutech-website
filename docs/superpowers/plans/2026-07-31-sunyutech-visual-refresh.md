# サンユウテック ビジュアルリフレッシュ 実装計画

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** サンユウテックのコーポレートサイト全6ページを、ライト基調 + 明るいグラデーション背景・細線ジオメトリ・拡大した余白・欧文 Barlow による「引き算」のビジュアルへ刷新する。

**Architecture:** `Base.astro` にビューポート固定の背景3層（メッシュ / ドット / グレイン）を敷き、各ページ・各コンポーネントが持つ不透明な地色をすべて撤去して背景を透過させる。カード面は半透明サーフェスに置き換える。区切りは影ではなく 1px のヘアラインで行い、角丸は撤去する。

**Tech Stack:** Astro 6.4.2 / バニラCSS / CSS custom properties（`astro/src/styles/tokens.css`）/ Google Fonts（Noto Sans JP・Barlow）

**Source spec:** `docs/superpowers/specs/2026-07-31-sunyutech-visual-refresh-design.md`

## Global Constraints

- 生カラー値（`#0B2545` 等）の直書き禁止。すべて `tokens.css` の CSS 変数経由で参照する
- インラインスタイル禁止。Astro コンポーネントのスコープド `<style>` を使う
- すべての `<img>` に `alt` を付ける。hero 以外は `loading="lazy"`
- セマンティック HTML（`header` / `nav` / `main` / `section` / `article` / `footer`）を維持する
- コミットは Conventional Commits + `(issue#10)`。Issue 番号は **#10**
- AI 生成の旨をコミットメッセージに書かない（署名トレーラーを付けない）
- 本計画ではコピー・IA・セクション構成・事実データを変更しない。ビジュアルのみを対象とする
- 検証は `npm run build` / `grep` によるアサーション / `http://localhost:4321` のブラウザ確認で行う。**このプロジェクトにテストランナーは存在しない**（`package.json` の依存は `astro` と `@astrojs/sitemap` のみ）

---

## File Structure

| パス | 本計画での役割 |
|---|---|
| `astro/src/styles/tokens.css` | 背景レイヤー・ヘアライン・字間・欧文書体・サーフェスのトークンを追加。セクション余白を拡大 |
| `astro/src/styles/global.css` | `body` の不透明背景と、フォントの `@import` を撤去（フォント読み込みは `Base.astro` の `<head>` へ移す） |
| `astro/src/layouts/Base.astro` | 背景3層のマークアップとスタイルを保持。`currentPage` の型を修正 |
| `astro/src/components/Hero.astro` | 写真フルブリードから、左テキスト・右写真枠の2カラム構成へ再設計 |
| `astro/src/components/SectionTitle.astro` | eyebrow + 見出し + オレンジ下線から、ネイビーのベタ帯へ |
| `astro/src/components/StatGrid.astro` | 角丸カードからヘアライン区切りのグリッドへ |
| `astro/src/components/ProcessFlow.astro` | 角丸カード + 矢印記号から、罫線接続へ |
| `astro/src/components/FeatureCard.astro` | サーフェス・影・角丸・hover を刷新 |
| `astro/src/components/WorkCard.astro` | 同上 |
| `astro/src/components/Button.astro` | 角丸撤去、余白拡大、字間追加 |
| `astro/src/components/Header.astro` | 背景透過、ヘアライン、TEL の欧文化 |
| `astro/src/components/MobileNav.astro` | 影をヘアラインへ |
| `astro/src/components/CtaBand.astro` | ダーク面としてグローを追加 |
| `astro/src/components/BrandSignature.astro` | 欧文書体と字間の適用 |
| `astro/src/components/Form.astro` | 送信ボタンの角丸撤去 |
| `astro/src/pages/*.astro`（6ファイル） | セクション地色の撤去。`index.astro` に `id="process"` を追加 |

**変更不要と確認済みのファイル**

- `astro/src/components/Footer.astro` — 既に `--color-navy-950` の地色と `rgba(255,255,255,0.08)` のヘアラインを持ち、影も角丸も使っていない。ダーク面（従）としてそのまま成立するため、本計画では触らない
- `astro/src/components/ScrollReveal.astro` — 挙動のみでスタイルを持たない

---

## Task 0: Issue 起票とブランチ作成

**Files:** なし（git / GitHub 操作のみ）

**Interfaces:**
- Produces: Issue 番号 `N`。以降の全タスクのコミットメッセージ `(issue#10)` で使用する

- [ ] **Step 1: Issue を起票する**

本文には設計書へのリンクと、以下の完了条件を含める。

```
Title: ビジュアルリフレッシュ（ライト基調 + グラデーション背景 / paycle 系の細線・余白 / 欧文 Barlow）

設計: docs/superpowers/specs/2026-07-31-sunyutech-visual-refresh-design.md
実装計画: docs/superpowers/plans/2026-07-31-sunyutech-visual-refresh.md

全6ページ（index / service / works / company / recruit / contact）のビジュアルを刷新する。
コピー・IA・事実データは変更しない。

完了条件は設計書 §9「受け入れ基準」の10項目とする。
```

```bash
gh issue create --title "ビジュアルリフレッシュ（ライト基調 + グラデーション背景 / 細線・余白 / 欧文 Barlow）" --label enhancement --body-file /tmp/issue-body.md
```

- [ ] **Step 2: main から作業ブランチを切る**

```bash
git checkout main
git pull origin main
git checkout -b feature/10-visual-refresh
```

- [ ] **Step 3: 設計書をこのブランチに移して commit する**

設計書は現在 `feature/8-website-renewal-astro` 上に untracked で存在する。本改修は別 Issue のため、このブランチへ移す。

```bash
git add docs/superpowers/specs/2026-07-31-sunyutech-visual-refresh-design.md \
        docs/superpowers/plans/2026-07-31-sunyutech-visual-refresh.md
git commit -m "docs: ビジュアルリフレッシュの設計書と実装計画を追加 (issue#10)"
```

---

## Task 1: 基盤（トークン・背景3層・Barlow）

**Files:**
- Modify: `astro/src/styles/tokens.css`
- Modify: `astro/src/styles/global.css:1`, `astro/src/styles/global.css:12`
- Modify: `astro/src/layouts/Base.astro:10`, `astro/src/layouts/Base.astro:44-50`

**Interfaces:**
- Produces:
  - CSS 変数 `--font-en` / `--bg-base` / `--bg-mesh-1` / `--bg-mesh-2` / `--bg-mesh-3` / `--bg-dot` / `--bg-dot-size` / `--grain-opacity` / `--line-navy` / `--line-orange` / `--surface-card` / `--ls-en-label` / `--ls-en-wide` / `--ls-ja`
  - `--section-py-desktop` = 160px / `--section-py-tablet` = 112px / `--section-py-mobile` = 72px
  - `Base.astro` の DOM に `.bg-mesh` / `.bg-dots` / `.bg-grain` の3要素

> **設計書からの追加**: `--surface-card` は設計書 §5.1 に記載がないが、Task 3 でカード面を半透明にする際に必須のため本タスクで追加する。値は `rgba(255, 255, 255, .82)`。

- [ ] **Step 1: 検証コマンドを先に走らせて失敗を確認する**

```bash
cd astro
grep -c -- "--font-en" src/styles/tokens.css
grep -c "bg-mesh" src/layouts/Base.astro
grep -c "background-color: var(--color-white)" src/styles/global.css
```

Expected: 順に `0` / `0` / `1`（最後の1は撤去対象が存在することの確認）

- [ ] **Step 2: tokens.css にトークンを追加する**

`src/styles/tokens.css` の `:root` 内、`/* colors */` ブロックの直後に追加する。

```css
  /* 欧文書体 */
  --font-en: 'Barlow', 'Helvetica Neue', Arial, sans-serif;

  /* 背景レイヤー */
  --bg-base: #F7F9FC;
  --bg-mesh-1: oklch(90% 0.075 245 / 0.95);
  --bg-mesh-2: oklch(92% 0.065 200 / 0.90);
  --bg-mesh-3: oklch(94% 0.060 70 / 0.95);
  --bg-dot: rgba(11, 37, 69, 0.10);
  --bg-dot-size: 22px;
  --grain-opacity: 0.035;

  /* サーフェス（背景を透過させるカード面） */
  --surface-card: rgba(255, 255, 255, 0.82);

  /* ヘアライン */
  --line-navy: rgba(11, 37, 69, 0.13);
  --line-orange: rgba(230, 126, 34, 0.55);

  /* 字間 */
  --ls-en-label: 0.26em;
  --ls-en-wide: 0.30em;
  --ls-ja: 0.04em;
```

- [ ] **Step 3: tokens.css のセクション余白を拡大する**

`/* layout */` ブロック内の3行を置き換える。

```css
  --section-py-desktop: 160px;
  --section-py-tablet: 112px;
  --section-py-mobile: 72px;
```

- [ ] **Step 4: global.css から body の不透明背景と @import を撤去する**

`src/styles/global.css:1` の `@import url(...)` の行を**削除**する。CSS の `@import` はスタイルシートを直列に読み込むため描画をブロックする。フォント読み込みは Step 5 で `Base.astro` の `<head>` に `<link rel="preconnect">` + `<link rel="stylesheet">` として移す（設計書 §6.1 の指定に従う）。

`src/styles/global.css:12` の行を**削除**する。

```css
  background-color: var(--color-white);   /* ← この1行を削除 */
```

- [ ] **Step 5: Base.astro にフォント読み込みと背景3層を追加し、currentPage の型を修正する**

`src/layouts/Base.astro:41`（`<link rel="icon" ...>`）の直前に、フォント読み込みを追加する。Noto Sans JP は `global.css` の `@import` から移設したものであり、新規追加ではない。

```astro
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;600;700;800&family=Barlow:wght@400;500;600;700&display=swap" />
```

`src/layouts/Base.astro:10` の `currentPage` 型に `'recruit'` を追加する（`recruit.astro` が `currentPage="recruit"` を渡しているのに型に存在せず、`Header.astro:5` とも不一致のため）。

```ts
  currentPage: 'index' | 'service' | 'works' | 'company' | 'contact' | 'recruit';
```

`<body>` の中身を置き換える。

```astro
  <body>
    <div class="bg-mesh" aria-hidden="true"></div>
    <div class="bg-dots" aria-hidden="true"></div>
    <div class="bg-grain" aria-hidden="true"></div>
    <Header currentPage={currentPage} />
    <main>
      <slot />
    </main>
    <Footer />
  </body>
```

`</html>` の後ろに `<style>` ブロックを追加する。

```astro
<style>
  .bg-mesh,
  .bg-dots,
  .bg-grain {
    position: fixed;
    inset: 0;
    pointer-events: none;
  }
  /* oklch 非対応環境向けフォールバック */
  .bg-mesh {
    z-index: -3;
    background: var(--bg-base);
  }
  @supports (color: oklch(90% 0.075 245)) {
    .bg-mesh {
      background:
        radial-gradient(ellipse 62% 88% at 6% -6%, var(--bg-mesh-1) 0%, transparent 66%),
        radial-gradient(ellipse 56% 78% at 96% 12%, var(--bg-mesh-2) 0%, transparent 70%),
        radial-gradient(ellipse 66% 82% at 70% 108%, var(--bg-mesh-3) 0%, transparent 68%),
        var(--bg-base);
    }
  }
  .bg-dots {
    z-index: -2;
    background-image: radial-gradient(circle, var(--bg-dot) 1.3px, transparent 1.4px);
    background-size: var(--bg-dot-size) var(--bg-dot-size);
  }
  .bg-grain {
    z-index: -1;
    opacity: var(--grain-opacity);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='180' height='180' filter='url(%23n)'/%3E%3C/svg%3E");
  }
</style>
```

- [ ] **Step 6: 検証コマンドを再実行して通ることを確認する**

```bash
cd astro
grep -c -- "--font-en" src/styles/tokens.css        # 1 であること
grep -c "bg-mesh" src/layouts/Base.astro            # 2 以上であること
grep -c "background-color: var(--color-white)" src/styles/global.css || echo "OK: 撤去済み"
grep -c "recruit" src/layouts/Base.astro            # 1 であること
npm run build
```

Expected: build が「6 page(s) built」で成功する

- [ ] **Step 7: ブラウザで背景3層が生成されていることを確認する**

`npm run dev` を起動し `http://localhost:4321/` を開く。DevTools のコンソールで実行する。

```js
({
  mesh: !!document.querySelector('.bg-mesh'),
  dots: !!document.querySelector('.bg-dots'),
  grain: !!document.querySelector('.bg-grain'),
  bodyBg: getComputedStyle(document.body).backgroundColor,
  meshBg: getComputedStyle(document.querySelector('.bg-mesh')).backgroundImage.slice(0, 40)
})
```

Expected: `mesh` / `dots` / `grain` がすべて `true`、`bodyBg` が `rgba(0, 0, 0, 0)`、`meshBg` が `radial-gradient` で始まる

この時点ではページ側の地色がまだ残っているため、**背景はまだ見えない**。それが正常である（Task 2 で撤去する）。

- [ ] **Step 8: Commit**

```bash
git add astro/src/styles/tokens.css astro/src/styles/global.css astro/src/layouts/Base.astro
git commit -m "feat(astro): 背景3層・欧文Barlow・ヘアライン等のトークンを追加し余白を拡大 (issue#10)"
```

---

## Task 2: セクション地色の撤去

**Files:**
- Modify: `astro/src/pages/index.astro:151-155`
- Modify: `astro/src/pages/service.astro:100-101`
- Modify: `astro/src/pages/works.astro:83`, `astro/src/pages/works.astro:88`
- Modify: `astro/src/pages/company.astro:95-98`, `astro/src/pages/company.astro:118`
- Modify: `astro/src/pages/contact.astro:54`
- Modify: `astro/src/pages/recruit.astro:70-71`, `astro/src/pages/recruit.astro:75`, `astro/src/pages/recruit.astro:87`

**Interfaces:**
- Consumes: Task 1 の背景3層
- Produces: 全6ページで固定メッシュ背景が透けて見える状態

現行は白と淡グレー `--color-gray-100` を交互に敷いてセクションを区切っている。この交互配置を廃止し、区切りは Task 3 以降のヘアラインが担う。

- [ ] **Step 1: 撤去対象を列挙して件数を確認する**

```bash
cd astro/src
grep -rn "background: var(--color-white)\|background: var(--color-gray-100)" pages/ | wc -l
```

Expected: `16`

- [ ] **Step 2: index.astro の地色を撤去する**

`pages/index.astro:151-155` の5行を、以下の1行に置き換える。

```css
  .section-strength { position: relative; }
```

- [ ] **Step 3: service.astro の地色を撤去する**

`pages/service.astro:100-101` を置き換える。

```css
  .method-section { padding: var(--section-py-desktop) 0; }
```

`.method-section.alt` のルールは削除する（交互配置を廃止するため）。`alt` クラスが付いた要素の記述は残してよい（スタイルが無くなるだけで無害）。

- [ ] **Step 4: works.astro の地色を撤去する**

`pages/works.astro:83` を置き換える。

```css
  .works-list { padding: var(--section-py-desktop) 0; }
```

`pages/works.astro:88` の `background: var(--color-white);` を削除する（フィルタタブの地色。Task 3 で `--surface-card` を当てる対象ではなく、透過させる）。

- [ ] **Step 5: company.astro の地色を撤去する**

`pages/company.astro:95-98` の4行をすべて削除する。`pages/company.astro:118` の `background: var(--color-gray-100);` を以下に置き換える。

```css
    background: var(--surface-card);
```

（118行目は情報テーブルを載せる面のため、可読性確保にサーフェスを当てる）

- [ ] **Step 6: contact.astro の地色を撤去する**

`pages/contact.astro:54` を置き換える。

```css
  .contact-block { padding: var(--section-py-desktop) 0; }
```

- [ ] **Step 7: recruit.astro の地色を撤去する**

`pages/recruit.astro:70-71` の2行を削除する。`pages/recruit.astro:75`（`.reasons li` の地色）と `pages/recruit.astro:87`（`.job-list li` の地色）を、それぞれ以下に置き換える。

```css
    background: var(--surface-card);
```

- [ ] **Step 8: 撤去できたことを検証する**

```bash
cd astro/src
grep -rn "background: var(--color-white)\|background: var(--color-gray-100)" pages/ || echo "OK: ページ側の地色をすべて撤去"
cd .. && npm run build
```

Expected: `OK: ページ側の地色をすべて撤去` が出力され、build が成功する

- [ ] **Step 9: ブラウザで背景が見えることを確認する**

`http://localhost:4321/` を開き、DevTools コンソールで各ページの背景の可視性を確認する。

```js
const pages = ['/', '/service/', '/works/', '/company/', '/recruit/', '/contact/'];
const out = [];
for (const p of pages) {
  const f = document.createElement('iframe');
  f.style.cssText = 'width:1200px;height:800px;position:fixed;left:-9999px';
  document.body.appendChild(f);
  await new Promise(r => { f.onload = r; f.src = p; });
  const d = f.contentDocument;
  const opaque = [...d.querySelectorAll('section, .section')].filter(s => {
    const bg = getComputedStyle(s).backgroundColor;
    return bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent';
  }).map(s => s.className);
  out.push({ p, opaqueSections: opaque });
  f.remove();
}
out
```

Expected: `opaqueSections` に残るのは CtaBand（`cta-band`）と各ページの `page-hero` のみ。他が残っていれば追加で撤去する

- [ ] **Step 10: Commit**

```bash
git add astro/src/pages/
git commit -m "refactor(astro): セクションの白/淡グレー交互地色を撤去し背景メッシュを透過 (issue#10)"
```

---

## Task 3: カード面の刷新（サーフェス・影・角丸）

**Files:**
- Modify: `astro/src/components/FeatureCard.astro:27-42`
- Modify: `astro/src/components/WorkCard.astro:29-40`
- Modify: `astro/src/components/StatGrid.astro:59-73`
- Modify: `astro/src/components/ProcessFlow.astro:31-39`

**Interfaces:**
- Consumes: `--surface-card` / `--line-navy` / `--line-orange`（Task 1）
- Produces: 影ゼロ・角丸ゼロのカード面。hover は罫線色の変化のみ

4コンポーネントとも「不透明な白 + 角丸 + 影 + 浮き上がり hover」という同じ構造を持つため、1タスクにまとめる。

- [ ] **Step 1: 検証コマンドを先に走らせて失敗を確認する**

```bash
cd astro/src
grep -rn "box-shadow" components/ | grep -v "transition" | wc -l
grep -rn -- "--radius-md" components/FeatureCard.astro components/WorkCard.astro components/StatGrid.astro components/ProcessFlow.astro | wc -l
```

Expected: 順に `4` 以上 / `4`

- [ ] **Step 2: FeatureCard を書き換える**

`components/FeatureCard.astro:27-42` を置き換える。

```css
  .feature-card {
    background: var(--surface-card);
    border: 1px solid var(--line-navy);
    border-radius: var(--radius-none);
    padding: var(--sp-6);
    display: flex;
    flex-direction: column;
    transition: border-color 0.25s ease;
  }
  .feature-card:hover {
    border-color: var(--line-orange);
  }
  .image { margin: calc(-1 * var(--sp-6)) calc(-1 * var(--sp-6)) var(--sp-4); }
  .image img { width: 100%; height: auto; aspect-ratio: 4/3; object-fit: cover; }
```

- [ ] **Step 3: WorkCard を書き換える**

`components/WorkCard.astro:29-40` を置き換える。

```css
  .work-card {
    background: var(--surface-card);
    border: 1px solid var(--line-navy);
    border-radius: var(--radius-none);
    overflow: hidden;
    transition: border-color 0.25s ease;
  }
  .work-card:hover {
    border-color: var(--line-orange);
  }
```

`components/WorkCard.astro:57` の `border-radius: var(--radius-full);`（工法チップ）は**残す**。チップは角丸のまま扱う。

- [ ] **Step 4: StatGrid のカード面を書き換える**

`components/StatGrid.astro:59-73` を置き換える。構造そのものは Task 6 で作り替えるが、先に影と角丸を落とす。

```css
  .stat {
    position: relative;
    padding: var(--sp-8) var(--sp-6) var(--sp-6);
    background: var(--surface-card);
    border-top: 3px solid var(--color-orange-600);
    border-left: 1px solid var(--line-navy);
    border-right: 1px solid var(--line-navy);
    border-bottom: 1px solid var(--line-navy);
    border-radius: var(--radius-none);
    transition: border-color 0.25s ease;
  }
  .stat:hover {
    border-color: var(--line-orange);
  }
```

- [ ] **Step 5: ProcessFlow のカード面を書き換える**

`components/ProcessFlow.astro:31-39` を置き換える。

```css
  .step {
    display: flex;
    flex-direction: column;
    gap: var(--sp-3);
    padding: var(--sp-6);
    background: var(--surface-card);
    border: 1px solid var(--line-navy);
    border-radius: var(--radius-none);
  }
```

- [ ] **Step 6: 検証コマンドを再実行する**

```bash
cd astro/src
grep -rn "box-shadow" components/FeatureCard.astro components/WorkCard.astro components/StatGrid.astro components/ProcessFlow.astro || echo "OK: 4コンポーネントから影を撤去"
grep -rn -- "--radius-md" components/FeatureCard.astro components/WorkCard.astro components/StatGrid.astro components/ProcessFlow.astro || echo "OK: 4コンポーネントから角丸を撤去"
cd .. && npm run build
```

Expected: 両方 `OK:` が出力され、build が成功する

- [ ] **Step 7: ブラウザで階層が読めることを確認する**

`http://localhost:4321/` の「事業内容」と「直近の施工実績」を見る。影が無くなった状態でカードの境界が背景から判別できることを確認する。判別しにくい場合は `--line-navy` の不透明度を `0.13` → `0.18` に上げる（`tokens.css`）。調整した場合はその旨をコミットメッセージに含める。

- [ ] **Step 8: Commit**

```bash
git add astro/src/components/
git commit -m "refactor(astro): カード面を半透明サーフェス化し影と角丸を撤去 (issue#10)"
```

---

## Task 4: 残りの角丸撤去

**Files:**
- Modify: `astro/src/components/Button.astro:17`
- Modify: `astro/src/components/Form.astro:65`
- Modify: `astro/src/components/MobileNav.astro:61`
- Modify: `astro/src/pages/recruit.astro:90`
- Modify: `astro/src/pages/company.astro:100`, `astro/src/pages/company.astro:120`
- Modify: `astro/src/pages/service.astro:108`

**Interfaces:**
- Consumes: `--line-navy`（Task 1）
- Produces: リポジトリ全体で `--radius-md` / `--radius-lg` の参照がゼロ

`--radius-sm`（`FeatureCard.astro:49` の番号バッジ、`Form.astro:48` の入力欄）と `--radius-full`（`WorkCard.astro:57` の工法チップ、`works.astro:91` のフィルタタブ）は**意図的に残す**。入力欄とチップの小さな丸みは操作可能であることの手がかりとして機能し、静的なカード面が角ばっていることと役割で区別できるため。

- [ ] **Step 1: 検証コマンドを先に走らせて失敗を確認する**

```bash
cd astro/src
grep -rn -- "--radius-md\|--radius-lg" . | grep -v "styles/tokens.css" | wc -l
```

Expected: `7`

- [ ] **Step 2: 各箇所を `--radius-none` に置き換える**

以下7箇所の `var(--radius-md)` を `var(--radius-none)` に変更する。

| ファイル:行 | 対象 |
|---|---|
| `components/Button.astro:17` | ボタン全般 |
| `components/Form.astro:65` | 送信ボタン |
| `pages/recruit.astro:90` | 募集職種カード |
| `pages/company.astro:100` | 地図の枠 |
| `pages/company.astro:120` | 情報テーブルの面 |
| `pages/service.astro:108` | 工法セクションの画像 |

- [ ] **Step 3: MobileNav の影をヘアラインに置き換える**

`components/MobileNav.astro:61` の `box-shadow: var(--shadow-md);` を置き換える。

```css
      border-bottom: 1px solid var(--line-navy);
```

`MobileNav.astro:59` の `background: var(--color-white);` は**残す**。開閉するドロップダウンは背後のコンテンツを隠す必要があり、透過させると文字が重なって読めなくなるため。

- [ ] **Step 4: 検証コマンドを再実行する**

```bash
cd astro/src
grep -rn -- "--radius-md\|--radius-lg" . | grep -v "styles/tokens.css" || echo "OK: radius-md/lg の参照ゼロ"
grep -rn "box-shadow" . | grep -v "styles/tokens.css" || echo "OK: box-shadow の使用ゼロ"
cd .. && npm run build
```

Expected: 両方 `OK:` が出力され、build が成功する

- [ ] **Step 5: Commit**

```bash
git add astro/src/
git commit -m "refactor(astro): 残りの角丸とMobileNavの影を撤去 (issue#10)"
```

---

## Task 5: SectionTitle をベタ帯へ

**Files:**
- Modify: `astro/src/components/SectionTitle.astro`（全面書き換え）

**Interfaces:**
- Consumes: `--font-en` / `--ls-en-label` / `--ls-ja`（Task 1）
- Produces: `SectionTitle` の Props は現行と互換（`text` / `eyebrow` / `level` / `align` / `invert`）。**呼び出し側の変更は不要**

現行は「`01 ── RENEWAL PROCESS`」＋見出し＋オレンジ下線の3段構成。これをネイビーのベタ帯1つに畳む。`eyebrow` の `"01 / RENEWAL PROCESS"` という書式は現行のまま維持する。

- [ ] **Step 1: 現行の呼び出し方を確認する**

```bash
cd astro/src
grep -rn "SectionTitle" pages/ | head -20
```

Expected: `eyebrow="01 / RENEWAL PROCESS"` のような `番号 / 英字ラベル` 形式で渡されている。Props を変えないことを確認する

- [ ] **Step 2: SectionTitle.astro を書き換える**

`components/SectionTitle.astro` の全体を置き換える。

```astro
---
interface Props {
  text: string;
  eyebrow?: string;
  level?: 2 | 3;
  align?: 'left' | 'center';
  invert?: boolean;
}
const { text, eyebrow, level = 2, align = 'left', invert = false } = Astro.props;
const Tag = `h${level}` as 'h2' | 'h3';
const num = eyebrow?.split(' / ')[0];
const label = eyebrow?.includes(' / ') ? eyebrow.split(' / ').slice(1).join(' / ') : undefined;
---
<header class:list={['section-title', `align-${align}`, invert && 'invert']}>
  <div class="band">
    {eyebrow && (
      <p class="eyebrow">
        <span class="num">{num}</span>
        {label && <span class="label">{label}</span>}
      </p>
    )}
    <Tag>{text}</Tag>
  </div>
</header>
<style>
  .section-title { margin: 0 0 var(--sp-12); }
  .section-title.align-center { text-align: center; }
  .band {
    display: inline-block;
    background: var(--color-navy-900);
    padding: var(--sp-4) var(--sp-8) var(--sp-4);
  }
  .section-title.invert .band {
    background: var(--color-orange-600);
  }
  .eyebrow {
    margin: 0 0 var(--sp-2);
    display: flex;
    align-items: center;
    gap: var(--sp-3);
    font-family: var(--font-en);
    font-size: var(--fs-xs);
    letter-spacing: var(--ls-en-label);
    text-transform: uppercase;
    font-weight: var(--fw-semibold);
    color: rgba(255, 255, 255, 0.85);
  }
  .num { font-feature-settings: 'tnum' 1; }
  .label { color: rgba(255, 255, 255, 0.85); }
  .section-title h2,
  .section-title h3 {
    font-size: var(--fs-3xl);
    font-weight: var(--fw-bold);
    color: var(--color-white);
    letter-spacing: var(--ls-ja);
    line-height: var(--lh-snug);
    margin: 0;
  }
  @media (max-width: 768px) {
    .band { padding: var(--sp-3) var(--sp-6); }
    .section-title h2, .section-title h3 { font-size: var(--fs-2xl); }
  }
  @media (max-width: 480px) {
    .section-title h2, .section-title h3 { font-size: var(--fs-xl); }
    .eyebrow { font-size: 10px; letter-spacing: 0.2em; }
  }
</style>
```

- [ ] **Step 3: 検証する**

```bash
cd astro/src
grep -c "accent" components/SectionTitle.astro || echo "OK: オレンジ下線を撤去"
grep -c -- "--font-en" components/SectionTitle.astro   # 1 であること
cd .. && npm run build
```

Expected: `OK: オレンジ下線を撤去` が出力され、build が成功する

- [ ] **Step 4: ブラウザで全ページの見出しを確認する**

`/`（5箇所）・`/service/`・`/works/`・`/company/`・`/recruit/`・`/contact/` を開き、ベタ帯が見出しの長さに応じて伸縮し、はみ出しが無いことを確認する。特に `/company/` の「保有機械・保有資格」など長い見出しをモバイル幅 375px で確認する。

- [ ] **Step 5: Commit**

```bash
git add astro/src/components/SectionTitle.astro
git commit -m "feat(astro): SectionTitleをネイビーのベタ帯に再設計 (issue#10)"
```

---

## Task 6: StatGrid をヘアライングリッドへ

**Files:**
- Modify: `astro/src/components/StatGrid.astro:53-118`（`<style>` ブロック）

**Interfaces:**
- Consumes: `--font-en` / `--line-navy`（Task 1）、Task 3 で撤去済みの影・角丸
- Produces: Props（`stats: Stat[]`）は不変。`<script>` のカウントアップ処理も不変

カードの並置をやめ、罫線で区切った1枚のグリッドにする。数字は Barlow に切り替える。

- [ ] **Step 1: カウントアップが動いていることを先に確認する**

`http://localhost:4321/` の「保有機械・体制」までスクロールし、`732` が 0 から増える挙動を目視で確認する。この挙動は本タスクで壊してはならない。

- [ ] **Step 2: StatGrid の `<style>` ブロックを置き換える**

`components/StatGrid.astro:53-118` の `<style>` 内を置き換える。`---` のフロントマターと HTML、`<script>` は一切変更しない。

```css
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
    border-left: 1px solid var(--line-navy);
    border-top: 1px solid var(--line-navy);
  }
  .stat {
    position: relative;
    padding: var(--sp-8) var(--sp-6);
    border-right: 1px solid var(--line-navy);
    border-bottom: 1px solid var(--line-navy);
    background: transparent;
  }
  .index {
    position: absolute;
    top: var(--sp-4);
    right: var(--sp-4);
    font-family: var(--font-en);
    font-size: var(--fs-xs);
    letter-spacing: var(--ls-en-label);
    color: var(--color-gray-500);
    font-weight: var(--fw-semibold);
  }
  .value-row {
    display: flex;
    align-items: baseline;
    gap: var(--sp-2);
    margin-bottom: var(--sp-4);
  }
  .value {
    font-family: var(--font-en);
    font-size: var(--fs-6xl);
    font-weight: var(--fw-semibold);
    color: var(--color-navy-900);
    line-height: 1;
    letter-spacing: 0.01em;
    font-feature-settings: 'tnum' 1;
  }
  .unit {
    font-family: var(--font-base);
    font-size: var(--fs-base);
    font-weight: var(--fw-bold);
    color: var(--color-orange-600);
  }
  .label {
    font-size: var(--fs-sm);
    font-weight: var(--fw-semibold);
    color: var(--color-gray-900);
    letter-spacing: var(--ls-ja);
    margin: 0 0 var(--sp-1);
  }
  .caveat {
    font-size: var(--fs-xs);
    color: var(--color-gray-500);
    margin: 0;
  }
  @media (max-width: 1023px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 480px) {
    .stat-grid { grid-template-columns: 1fr; }
    .value { font-size: var(--fs-5xl); }
  }
```

- [ ] **Step 3: 検証する**

```bash
cd astro/src
grep -c "box-shadow\|border-radius" components/StatGrid.astro || echo "OK: 影・角丸なし"
grep -c -- "--font-en" components/StatGrid.astro    # 2 であること（.index と .value）
cd .. && npm run build
```

Expected: `OK: 影・角丸なし` が出力され、`--font-en` が2箇所、build が成功する

- [ ] **Step 4: カウントアップが壊れていないことを再確認する**

ページをリロードして「保有機械・体制」までスクロールし、`732` のカウントアップが Step 1 と同じく動くことを確認する。

- [ ] **Step 5: Commit**

```bash
git add astro/src/components/StatGrid.astro
git commit -m "feat(astro): StatGridをヘアライン区切りのグリッドに再設計 (issue#10)"
```

---

## Task 7: ProcessFlow を罫線接続へ

**Files:**
- Modify: `astro/src/components/ProcessFlow.astro`（全面書き換え）

**Interfaces:**
- Consumes: `--font-en` / `--line-navy` / `--line-orange`（Task 1）
- Produces: Props なし（`steps` は内部定数）。呼び出し側（`index.astro:33`）の変更は不要

矢印記号「→」による接続をやめ、工程を貫く1本の横罫と、各工程のノード（点）で表現する。

- [ ] **Step 1: ProcessFlow.astro の全体を置き換える**

```astro
---
const steps = [
  { num: '01', label: '床版はつり', sub: 'CONJET ロボット' },
  { num: '02', label: '断面修復', sub: '欠損部の再生' },
  { num: '03', label: '床版防水', sub: '層の新設' },
  { num: '04', label: '舗装復旧', sub: '一気に再生' },
] as const;
---
<ol class="process-flow" aria-label="高速道路リニューアル ワンストップ4工程">
  {steps.map((s) => (
    <li class="step">
      <span class="node" aria-hidden="true"></span>
      <span class="num">{s.num}</span>
      <h4>{s.label}</h4>
      <p>{s.sub}</p>
    </li>
  ))}
</ol>
<style>
  .process-flow {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    padding: var(--sp-12) 0 var(--sp-8);
    position: relative;
  }
  /* 4工程を貫く1本の横罫 */
  .process-flow::before {
    content: '';
    position: absolute;
    top: var(--sp-12);
    left: 0;
    right: 0;
    height: 1px;
    background: var(--line-navy);
  }
  .step {
    position: relative;
    padding: var(--sp-8) var(--sp-6) 0 0;
  }
  .node {
    position: absolute;
    top: 0;
    left: 0;
    width: 9px;
    height: 9px;
    border-radius: var(--radius-full);
    background: var(--color-orange-600);
    transform: translate(-4px, -4px);
  }
  .num {
    display: block;
    font-family: var(--font-en);
    font-size: var(--fs-sm);
    font-weight: var(--fw-semibold);
    letter-spacing: var(--ls-en-label);
    color: var(--color-orange-600);
    margin-bottom: var(--sp-4);
  }
  .step h4 {
    font-size: var(--fs-xl);
    font-weight: var(--fw-bold);
    color: var(--color-navy-900);
    letter-spacing: var(--ls-ja);
    margin: 0 0 var(--sp-2);
  }
  .step p {
    font-size: var(--fs-sm);
    color: var(--color-gray-700);
    margin: 0;
  }
  @media (max-width: 1023px) {
    .process-flow { grid-template-columns: repeat(2, 1fr); row-gap: var(--sp-8); }
  }
  @media (max-width: 480px) {
    .process-flow { grid-template-columns: 1fr; row-gap: var(--sp-6); }
    .process-flow::before { display: none; }
    .step { padding-left: var(--sp-6); border-left: 1px solid var(--line-navy); }
    .node { left: 0; top: var(--sp-8); }
  }
</style>
```

- [ ] **Step 2: 検証する**

```bash
cd astro/src
grep -c "→" components/ProcessFlow.astro || echo "OK: 矢印記号を撤去"
grep -c -- "--radius-md" components/ProcessFlow.astro || echo "OK: 角丸なし"
cd .. && npm run build
```

Expected: 両方 `OK:` が出力され、build が成功する

- [ ] **Step 3: ブラウザで3ブレークポイントを確認する**

`http://localhost:4321/` の「ワンストップ施工4工程」を、1440 / 768 / 375px で確認する。横罫とノードが揃っていること、375px では縦罫に切り替わることを確認する。

- [ ] **Step 4: Commit**

```bash
git add astro/src/components/ProcessFlow.astro
git commit -m "feat(astro): ProcessFlowを罫線とノードによる接続に再設計 (issue#10)"
```

---

## Task 8: Button の再設計

**Files:**
- Modify: `astro/src/components/Button.astro:13-31`（`<style>` ブロック）

**Interfaces:**
- Consumes: `--ls-ja`（Task 1）、Task 4 で撤去済みの角丸
- Produces: Props（`variant` / `href` / `label`）は不変。`primary` / `secondary` / `tertiary` の3種を維持

- [ ] **Step 1: Button の `<style>` を置き換える**

`components/Button.astro:13-31` を置き換える。`---` と HTML は変更しない。

```astro
<style>
  .btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: var(--sp-6) var(--sp-8);
    border-radius: var(--radius-none);
    font-weight: var(--fw-semibold);
    font-size: var(--fs-base);
    letter-spacing: 0.16em;
    text-decoration: none;
    transition: background-color 0.15s, color 0.15s, border-color 0.15s;
  }
  .btn:focus-visible { outline: 2px solid var(--color-orange-600); outline-offset: 2px; }
  .btn-primary { background: var(--color-orange-600); color: var(--color-white); }
  .btn-primary:hover { background: var(--color-orange-700); text-decoration: none; }
  .btn-secondary {
    background: transparent;
    color: var(--color-navy-900);
    border: 1px solid var(--color-navy-900);
  }
  .btn-secondary:hover { background: var(--color-navy-900); color: var(--color-white); text-decoration: none; }
  .btn-tertiary { padding: 0; color: var(--color-navy-900); font-weight: var(--fw-semibold); letter-spacing: var(--ls-ja); }
  .btn-tertiary:hover { text-decoration: underline; }
  .btn-tertiary .arrow { margin-left: var(--sp-4); color: var(--color-orange-600); transition: margin-left 0.2s ease; }
  .btn-tertiary:hover .arrow { margin-left: var(--sp-6); }
</style>
```

`secondary` の地色を `--color-white` から `transparent` に変えているのは、背景メッシュを透過させるため。

- [ ] **Step 2: 検証する**

```bash
cd astro/src
grep -c -- "--radius-md" components/Button.astro || echo "OK: 角丸なし"
grep -c "var(--color-white)" components/Button.astro   # 1 であること（btn-primary の文字色のみ）
cd .. && npm run build
```

Expected: `OK: 角丸なし` が出力され、`--color-white` が1箇所、build が成功する

- [ ] **Step 3: ブラウザでコントラストを確認する**

`/contact/` と `/` のヒーローで、`secondary` ボタンの文字と背景メッシュのコントラスト比が 4.5:1 以上であることを DevTools の Accessibility パネルで確認する。下回る場合は `--surface-card` を `secondary` の地色に当てる。

- [ ] **Step 4: Commit**

```bash
git add astro/src/components/Button.astro
git commit -m "feat(astro): Buttonの角丸を撤去し余白と字間を拡大 (issue#10)"
```

---

## Task 9: Header / CtaBand / BrandSignature

**Files:**
- Modify: `astro/src/components/Header.astro:42-48`, `astro/src/components/Header.astro:84-85`
- Modify: `astro/src/components/CtaBand.astro:24-25`
- Modify: `astro/src/components/BrandSignature.astro`（`<style>` 内の `.brand-sig`）

**Interfaces:**
- Consumes: `--font-en` / `--ls-en-wide` / `--line-navy` / `--surface-card`（Task 1）
- Produces: いずれも Props は不変

- [ ] **Step 1: Header を半透明化しヘアラインに変える**

`components/Header.astro:42-48` を置き換える。

```css
  .header {
    position: sticky;
    top: 0;
    z-index: var(--z-sticky);
    background: var(--surface-card);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid var(--line-navy);
  }
```

`--color-white` ではなく `--surface-card` + `backdrop-filter` にしているのは、スクロール時に背景メッシュが透けつつ、下のコンテンツと重なっても文字が読める状態を保つため。

`components/Header.astro:84-85` を置き換え、TEL を欧文書体にする。

```css
  .tel-num { font-family: var(--font-en); font-weight: var(--fw-semibold); font-size: var(--fs-base); letter-spacing: 0.1em; }
  .tel-hours { font-size: var(--fs-xs); color: var(--color-gray-700); letter-spacing: var(--ls-ja); }
```

- [ ] **Step 2: CtaBand にグローを追加する**

`components/CtaBand.astro:24-25` の `.cta-band` を置き換える。

```css
  .cta-band {
    position: relative;
    background: var(--color-navy-900);
    padding: var(--section-py-tablet) 0;
    overflow: hidden;
  }
  .cta-band::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 60% 100% at 88% 0%, var(--color-navy-700) 0%, transparent 72%);
    opacity: 0.85;
  }
  .cta-inner { position: relative; z-index: 1; }
```

`.cta-inner` は既存の `components/CtaBand.astro:26-34` のルールに `position: relative; z-index: 1;` を足す形でもよい。重複定義を避けること。

- [ ] **Step 3: BrandSignature を欧文書体にする**

`components/BrandSignature.astro` の `.brand-sig` ルールで、`font-family` と `letter-spacing` を置き換える。

```css
  .brand-sig {
    font-family: var(--font-en);
    letter-spacing: var(--ls-en-wide);
    text-transform: uppercase;
    color: var(--color-navy-900);
    font-weight: var(--fw-semibold);
    margin: 0;
  }
```

- [ ] **Step 4: 検証する**

```bash
cd astro/src
grep -c -- "--font-en" components/Header.astro components/BrandSignature.astro
grep -c "var(--color-white)" components/Header.astro || echo "OK: Headerの不透明白を撤去"
cd .. && npm run build
```

Expected: `--font-en` が各1箇所以上、`OK: Headerの不透明白を撤去` が出力され、build が成功する

- [ ] **Step 5: ブラウザでスティッキーヘッダーを確認する**

`/` をスクロールし、ヘッダーが半透明でぼかしが効いていること、コンテンツが下を通っても nav とロゴが読めることを確認する。`backdrop-filter` 非対応環境では `--surface-card` の 82% 不透明で代替される。

- [ ] **Step 6: Commit**

```bash
git add astro/src/components/Header.astro astro/src/components/CtaBand.astro astro/src/components/BrandSignature.astro
git commit -m "feat(astro): Headerを半透明化しCtaBandにグロー、BrandSignatureを欧文化 (issue#10)"
```

---

## Task 10: Hero の再構成

**Files:**
- Modify: `astro/src/components/Hero.astro`（全面書き換え）
- Modify: `astro/src/pages/index.astro:28`（`id="process"` の追加）

**Interfaces:**
- Consumes: `--font-en` / `--ls-en-wide` / `--line-navy` / `--surface-card`（Task 1）、`Button`（Task 8）、`BrandSignature`（Task 9）
- Produces: Props（`heading1` / `heading2` / `sub` / `primaryCta` / `secondaryCta` / `imageSrc` / `imageAlt` / `showSignature`）は**不変**。`index.astro` の呼び出しは変更しない

`Hero.astro` を使っているのは `index.astro` のみ（他5ページは各自の `.page-hero` セクションを持つ）。

写真フルブリード＋文字重ねをやめ、左にテキスト・右に写真枠の2カラムにする。

> **設計書からの明確化**: 設計書 §7 は「数字インジケータ（1 2 3 4）を追加」としているが、見本（paycle）でこれはカルーセルの現在位置表示だった。カルーセルを持たない本サイトで意味のない数字を置くのは避け、**4工程（はつり→補修→防水→復旧）への索引**として実装する。各数字は工程セクション `#process` へのアンカーとし、スクリーンリーダー向けに工程名を持たせる。

- [ ] **Step 1: index.astro の工程セクションにアンカーを付ける**

`pages/index.astro:28` を置き換える。

```astro
  <section class="section section-process" id="process">
```

- [ ] **Step 2: Hero.astro の全体を置き換える**

```astro
---
import Button from './Button.astro';
import BrandSignature from './BrandSignature.astro';
interface Props {
  heading1: string;
  heading2: string;
  sub: string;
  primaryCta: { label: string; href: string };
  secondaryCta?: { label: string; href: string };
  imageSrc: string;
  imageAlt: string;
  showSignature?: boolean;
}
const { heading1, heading2, sub, primaryCta, secondaryCta, imageSrc, imageAlt, showSignature = true } = Astro.props;
const steps = [
  { num: '1', label: '床版はつり' },
  { num: '2', label: '断面修復' },
  { num: '3', label: '床版防水' },
  { num: '4', label: '舗装復旧' },
];
---
<section class="hero" aria-label="ヒーロー">
  <div class="hero-inner">
    <ol class="hero-steps" aria-label="ワンストップ4工程へ移動">
      {steps.map(s => (
        <li>
          <a href="#process">
            <span aria-hidden="true">{s.num}</span>
            <span class="visually-hidden">{s.label}</span>
          </a>
        </li>
      ))}
    </ol>

    <div class="hero-grid">
      <div class="hero-text">
        {showSignature && <BrandSignature variant="eyebrow" />}
        <h1>
          <span>{heading1}</span>
          <span>{heading2}</span>
        </h1>
        <div class="hero-lower">
          <p class="hero-ghost" aria-hidden="true">
            <span>ONE STOP</span>
            <span>ON SITE</span>
            <span>NATIONWIDE</span>
          </p>
          <div class="hero-copy">
            <p class="hero-sub">{sub}</p>
            <div class="hero-ctas">
              <Button variant="primary" href={primaryCta.href} label={primaryCta.label} />
              {secondaryCta && <Button variant="secondary" href={secondaryCta.href} label={secondaryCta.label} />}
            </div>
          </div>
        </div>
      </div>

      <figure class="hero-figure">
        <img src={imageSrc} alt={imageAlt} width="1200" height="900" loading="eager" fetchpriority="high" />
      </figure>
    </div>
  </div>
</section>
<style>
  .hero { padding: var(--sp-16) 0 var(--section-py-desktop); }
  .hero-inner {
    max-width: var(--wrapper-max);
    margin: 0 auto;
    padding: 0 var(--gutter-desktop);
  }

  .hero-steps {
    display: flex;
    gap: var(--sp-4);
    margin: 0 0 var(--sp-8);
    font-family: var(--font-en);
    font-size: var(--fs-sm);
    letter-spacing: var(--ls-en-wide);
  }
  .hero-steps a {
    color: var(--color-gray-500);
    font-weight: var(--fw-semibold);
    text-decoration: none;
    padding: var(--sp-1) var(--sp-2);
  }
  .hero-steps a:hover { color: var(--color-orange-600); text-decoration: none; }
  .hero-steps a:focus-visible { outline: 2px solid var(--color-orange-600); outline-offset: 2px; }

  .hero-grid {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: var(--sp-12);
    align-items: start;
  }

  .hero h1 {
    font-size: var(--fs-6xl);
    font-weight: var(--fw-bold);
    color: var(--color-navy-900);
    line-height: var(--lh-snug);
    letter-spacing: var(--ls-ja);
    margin-bottom: var(--sp-12);
  }
  .hero h1 span { display: block; }

  .hero-lower {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: var(--sp-12);
    align-items: start;
  }
  .hero-ghost {
    margin: 0;
    font-family: var(--font-en);
    font-size: var(--fs-xl);
    font-weight: var(--fw-bold);
    letter-spacing: var(--ls-en-wide);
    color: transparent;
    -webkit-text-stroke: 1px rgba(11, 37, 69, 0.26);
  }
  .hero-ghost span { display: block; margin-bottom: var(--sp-2); }

  .hero-sub {
    font-size: var(--fs-base);
    color: var(--color-gray-700);
    line-height: var(--lh-relaxed);
    margin-bottom: var(--sp-8);
    max-width: 46ch;
  }
  .hero-ctas { display: flex; gap: var(--sp-4); flex-wrap: wrap; }

  .hero-figure { margin: 0; border: 1px solid var(--line-navy); line-height: 0; }
  .hero-figure img { width: 100%; height: auto; aspect-ratio: 4/3; object-fit: cover; }

  @media (max-width: 1023px) {
    .hero { padding: var(--sp-12) 0 var(--section-py-tablet); }
    .hero-inner { padding: 0 var(--gutter-tablet); }
    .hero-grid { grid-template-columns: 1fr; }
    .hero h1 { font-size: var(--fs-5xl); }
    .hero-figure { order: -1; margin-bottom: var(--sp-8); }
  }
  @media (max-width: 768px) {
    .hero-inner { padding: 0 var(--gutter-mobile); }
    .hero h1 { font-size: var(--fs-4xl); margin-bottom: var(--sp-8); }
    .hero-lower { grid-template-columns: 1fr; gap: var(--sp-8); }
    .hero-ghost { font-size: var(--fs-lg); }
  }
</style>
```

`-webkit-text-stroke` は Chrome / Safari / Firefox で利用可能。非対応環境では文字が `transparent` になり見えなくなるが、`aria-hidden="true"` の装飾要素であり情報は失われない。

- [ ] **Step 3: 検証する**

```bash
cd astro/src
grep -c "hero-overlay\|hero-bg\|hero-scroll" components/Hero.astro || echo "OK: フルブリード写真の構造を撤去"
grep -c 'id="process"' pages/index.astro    # 1 であること
grep -c 'alt=""' components/Hero.astro || echo "OK: 空altを撤去（写真がコンテンツになったため）"
cd .. && npm run build
```

Expected: `OK:` 2つが出力され、`id="process"` が1箇所、build が成功する

- [ ] **Step 4: ブラウザでヒーローと LCP を確認する**

`http://localhost:4321/` を 1440 / 768 / 375px で確認する。

- 見出しが背景メッシュの上で読めること（コントラスト比 4.5:1 以上）
- 4工程の数字をクリックすると工程セクションへ移動すること
- 1023px 以下で写真がテキストの上に来ること
- Tab キーで 4工程リンク → CTA の順に到達できること

- [ ] **Step 5: Commit**

```bash
git add astro/src/components/Hero.astro astro/src/pages/index.astro
git commit -m "feat(astro): Heroを左テキスト・右写真枠の2カラムに再設計 (issue#10)"
```

---

## Task 11: 全ページ検証と受け入れ基準の確認

**Files:** なし（検証のみ。不備が見つかった場合のみ該当ファイルを修正）

**Interfaces:**
- Consumes: Task 1〜10 のすべて
- Produces: 設計書 §9 の受け入れ基準10項目の充足

- [ ] **Step 1: 静的アサーションをまとめて走らせる**

```bash
cd astro
echo "--- build ---"
npm run build

echo "--- 生カラー値（tokens.css 以外） ---"
grep -rnE "#[0-9a-fA-F]{3,8}\b" src/ --include="*.astro" --include="*.css" \
  | grep -v "src/styles/tokens.css" | grep -v "data:image/svg" || echo "OK: 生カラー値ゼロ"

echo "--- box-shadow ---"
grep -rn "box-shadow" src/ | grep -v "src/styles/tokens.css" || echo "OK: box-shadow ゼロ"

echo "--- radius-md / radius-lg ---"
grep -rn -- "--radius-md\|--radius-lg" src/ | grep -v "src/styles/tokens.css" || echo "OK: 参照ゼロ"

echo "--- ページ側の不透明地色 ---"
grep -rn "background: var(--color-white)\|background: var(--color-gray-100)" src/pages/ || echo "OK: ゼロ"

echo "--- section-py の値 ---"
grep -n -- "--section-py" src/styles/tokens.css
```

Expected: build が「6 page(s) built」で成功。`OK:` が4つ出力される。`--section-py-desktop: 160px` / `--section-py-tablet: 112px` / `--section-py-mobile: 72px`

- [ ] **Step 2: 横スクロールを4ブレークポイント × 6ページで検証する**

`npm run dev` を起動し `http://localhost:4321/` の DevTools コンソールで実行する。

```js
const pages = ['/', '/service/', '/works/', '/company/', '/recruit/', '/contact/'];
const widths = [375, 768, 1024, 1440];
const out = [];
for (const w of widths) {
  for (const p of pages) {
    const f = document.createElement('iframe');
    f.style.cssText = `width:${w}px;height:900px;position:fixed;left:-9999px;top:0;border:0`;
    document.body.appendChild(f);
    await new Promise(r => { f.onload = r; f.src = p; });
    await new Promise(r => setTimeout(r, 350));
    const d = f.contentDocument, iw = f.contentWindow.innerWidth;
    const over = d.documentElement.scrollWidth > iw;
    const offenders = over
      ? [...d.querySelectorAll('body *')].filter(e => e.getBoundingClientRect().right > iw + 1)
          .slice(0, 3).map(e => e.tagName + '.' + e.className)
      : [];
    out.push({ w, p, scrollW: d.documentElement.scrollWidth, over, offenders });
    f.remove();
  }
}
out.filter(r => r.over).length ? out.filter(r => r.over) : 'ALL OK';
```

Expected: `'ALL OK'`

- [ ] **Step 3: アクセシビリティを検証する**

```js
const pages = ['/', '/service/', '/works/', '/company/', '/recruit/', '/contact/'];
const out = [];
for (const p of pages) {
  const f = document.createElement('iframe');
  f.style.cssText = 'width:1200px;height:900px;position:fixed;left:-9999px;border:0';
  document.body.appendChild(f);
  await new Promise(r => { f.onload = r; f.src = p; });
  const d = f.contentDocument;
  out.push({
    p,
    imgs: d.querySelectorAll('img').length,
    imgNoAlt: [...d.querySelectorAll('img')].filter(i => i.getAttribute('alt') === null).length,
    imgNoLazy: [...d.querySelectorAll('img')].filter(i => i.getAttribute('loading') !== 'lazy')
      .map(i => i.getAttribute('src')),
    h1: [...d.querySelectorAll('h1')].map(h => h.textContent.trim().slice(0, 24)),
    focusable: d.querySelectorAll('a[href],button,input,select,textarea').length
  });
  f.remove();
}
out
```

Expected: 全ページで `imgNoAlt` が `0`、`h1` が1件、`imgNoLazy` はロゴとヒーロー写真のみ

- [ ] **Step 4: oklch フォールバックを検証する**

DevTools コンソールで実行する。

```js
CSS.supports('color', 'oklch(90% 0.075 245)')
```

`true` の環境では、`.bg-mesh` に `radial-gradient` が適用されていることを Elements パネルで確認する。フォールバックの動作は、`Base.astro` の `@supports` ブロックを一時的にコメントアウトして `--bg-base` の単色 `#F7F9FC` になることを確認し、確認後にコメントを戻す。

- [ ] **Step 5: Barlow のフォールバックを検証する**

DevTools の Network パネルで `fonts.gstatic.com` へのリクエストをブロックし、リロードする。レイアウトが崩れず `Helvetica Neue` / `Arial` で表示されることを確認する。確認後、ブロックを解除する。

- [ ] **Step 6: コントラスト比を検証する**

DevTools の Accessibility パネルで、以下の組み合わせが 4.5:1 以上であることを確認する。

| 要素 | ページ |
|---|---|
| 本文（`--color-gray-900`） | 全ページ |
| ヒーロー見出し（`--color-navy-900`） | `/` |
| `.hero-sub`（`--color-gray-700`） | `/` |
| StatGrid の `.caveat`（`--color-gray-500`） | `/` `/company/` |
| Button secondary の文字 | `/` `/contact/` |

背景メッシュが最も濃くかかる位置（左上・右上・下部中央）で測ること。下回るものがあれば `--bg-mesh-*` の明度を上げて再測定する。

- [ ] **Step 7: 見つかった不備を修正して commit する**

Step 1〜6 で不備が見つかった場合のみ、該当ファイルを修正する。

```bash
git add astro/src/
git commit -m "fix(astro): 全ページ検証で見つかった不備を修正 (issue#10)"
```

- [ ] **Step 8: 受け入れ基準の充足を設計書に記録する**

設計書 `docs/superpowers/specs/2026-07-31-sunyutech-visual-refresh-design.md` §9 のチェックボックス10項目を、確認できたものから `- [x]` に更新する。

```bash
git add docs/superpowers/specs/2026-07-31-sunyutech-visual-refresh-design.md
git commit -m "docs: ビジュアルリフレッシュの受け入れ基準の充足を記録 (issue#10)"
```

- [ ] **Step 9: PR を作成する**

```bash
git push -u origin feature/10-visual-refresh
```

PR 本文には設計書 §9 の受け入れ基準10項目と、以下の「本PRで扱っていないこと」を明記する。

```
## 本PRで扱っていないこと（別作業）
- お問い合わせフォームの PHP 化・迷惑メール対策
- プライバシーポリシーの新設
- 画像最適化（public/images 計33MB）← 本PRで hero の必要寸法が変わるため、本PRのマージ後に着手する
- /works の元請実名（NIPPO・前田道路）および推測ベースの実績8件の是正
- 採用ページの本格版
- 本番 sunyutech.jp への置換
```

---

## 実施順序の注意

- **Task 1 → Task 2 は連続で行う**。Task 1 だけを適用した状態ではページ側の地色が背景を覆っており、変化が何も見えない。途中で止めると「効果がない」と誤解する
- **Task 3 は Task 2 の後に行う**。地色を撤去してからでないと、サーフェスの半透明が意味を持たない
- Task 5〜10 は相互に依存しないため、順序を入れ替えてよい
- **画像最適化は本計画の完了後に着手する**。Task 10 で hero の写真が「フルブリード」から「4:3 の枠内」に変わり、必要な画像寸法が変わるため

---

**Last Updated**: 2026-07-31
