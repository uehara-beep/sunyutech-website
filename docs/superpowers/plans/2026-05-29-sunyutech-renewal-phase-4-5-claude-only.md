# サンユウテック HPリニューアル Phase 4-5 Claude-only 実装計画

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** ChatGPT image2 / Claude Design を経由せず、Claude Code が直接 design/handoff/* を執筆してから Astro 4.x で5ページを実装し、Phase 5 QA Must Have を満たすローカルプレビュー検収状態にする。

**Architecture:** `projects/sunyutech-renewal/design/handoff/` を実装契約として書き起こし、それを参照しながら `astro/` 配下に静的サイトを構築。Tailwind不使用・バニラCSS + design tokens、JS最小限・必要時のみ Astro Island。本番置換は別Issue。

**Tech Stack:** Astro 4.x / TypeScript strict / バニラCSS（design tokens via CSS変数）/ gh CLI

**Spec参照:** `docs/superpowers/specs/2026-05-28-sunyutech-website-renewal-design.md`（§12 Pivot 注記含む）
**前プラン参照:** `docs/superpowers/plans/2026-05-28-sunyutech-renewal-phase-0-to-3.md`（Task 9 まで完了済、Phase 0/1/2-prompt の成果物が手元にある）

---

## File Structure（本プランで作成）

| パス | 役割 |
|---|---|
| `projects/sunyutech-renewal/design/handoff/10-design-principles.md` | デザイン原則3-5本 |
| `projects/sunyutech-renewal/design/handoff/20-design-tokens.yaml` | colors / typography / spacing / radius / shadow / z-index |
| `projects/sunyutech-renewal/design/handoff/25-layout-contract.yaml` | wrapper / content / gutter / breakpoints / responsive cols |
| `projects/sunyutech-renewal/design/handoff/30-component-spec.md` | 7コンポーネントの構造・props・状態・a11y |
| `projects/sunyutech-renewal/design/handoff/40-page-structure.md` | 5ページのセクション順序とコンテンツ |
| `projects/sunyutech-renewal/design/handoff/50-acceptance-criteria.md` | spec §5 を実装用チェックリスト形式に展開 |
| `projects/sunyutech-renewal/design/handoff/60-implementation-notes.md` | Astro実装ルール・既存画像参照表 |
| `projects/sunyutech-renewal/qa/checklist.md` | Phase 5 QA 実行ログ |
| `astro/` 以下一式 | Astro プロジェクト本体 |
| `README.md` / `CLAUDE.md` | 既存更新 |

---

## Task 1: design/handoff 基礎 3ファイル（原則 / tokens / layout contract）

**Files:**
- Create: `projects/sunyutech-renewal/design/handoff/10-design-principles.md`
- Create: `projects/sunyutech-renewal/design/handoff/20-design-tokens.yaml`
- Create: `projects/sunyutech-renewal/design/handoff/25-layout-contract.yaml`

- [ ] **Step 1: `10-design-principles.md` を作成**

```markdown
# 10 Design Principles

## P1. 受注ペルソナの3秒読了を最優先する
ヒーローで「何の会社か」「自分の現場に使えるか」「次にどこをタップするか」が3秒で伝わる構造を最上位制約とする。文字量はサブ含め 2 行以内、CTA は主+副の 2 つだけ、装飾より行間と余白で読みやすさを担保。

## P2. ネイビーで信頼、オレンジは点でしか使わない
主色 #0B2545（navy）は heading / nav / 主要面材 / フッターに広く使う。差し色 #E67E22（orange）は主CTA塗りとカード矢印などの小アクセントだけ。可視面積の 5% 以下を厳格に守る（背景塗りに使わない）。

## P3. 数字と固有名詞で説得する
「24台」「3工法」「30,000m²」「CONJET327」「NLB 35305DGF」「NIPPO」「前田道路」「九州自動車道」「092-555-9211」を各ページに分散配置。抽象的な「最高品質」「お客様第一」「DX」「イノベーション」は禁止。

## P4. 既存写真をそのまま使う前提で設計する
`images/` の現行写真ファイル（hero-1〜8、service-paving/waterjet/concrete、work-paving01〜04・work-waterjet01〜03・work-concrete01、about-image）を一切置換せず、レイアウトを写真に合わせて作る。新規画像生成は行わない。

## P5. JS最小限、Astro Island は最後の手段
ヘッダのモバイル開閉と works のフィルタが必要になった時だけ Astro Island を使う。それ以外は静的HTML+CSSで完結する。
```

- [ ] **Step 2: `20-design-tokens.yaml` を作成**

```yaml
# 20 Design Tokens
# CSS変数として src/styles/tokens.css に展開する基準値。値直書き禁止、必ず変数経由。

colors:
  navy:
    900: "#0B2545"   # primary
    800: "#13315C"
    700: "#1E3A5F"
  orange:
    600: "#E67E22"   # accent
    700: "#D35400"   # hover
    500: "#F39C12"   # subtle highlight (使う時は要相談)
  gray:
    900: "#1F2937"   # body text base
    700: "#374151"
    500: "#6B7280"
    300: "#D1D5DB"
    200: "#E5E7EB"   # card border
    100: "#F4F6F9"   # soft gray section bg
  white: "#FFFFFF"
  status:
    success: "#0E8A16"
    error: "#B91C1C"

typography:
  font_family:
    base: "'Noto Sans JP', 'Hiragino Sans', 'Yu Gothic', sans-serif"
    mono: "'JetBrains Mono', ui-monospace, monospace"
  weights:
    regular: 400
    medium: 500
    semibold: 600
    bold: 700
    extrabold: 800
  scale:
    xs: "0.75rem"     # 12px
    sm: "0.875rem"    # 14px
    base: "1rem"      # 16px
    lg: "1.125rem"    # 18px
    xl: "1.25rem"     # 20px
    2xl: "1.5rem"     # 24px
    3xl: "2rem"       # 32px
    4xl: "2.5rem"     # 40px
    5xl: "3rem"       # 48px
  line_height:
    tight: 1.2
    normal: 1.5
    relaxed: 1.7

spacing:
  base: 4
  scale:
    1: "4px"
    2: "8px"
    3: "12px"
    4: "16px"
    6: "24px"
    8: "32px"
    12: "48px"
    16: "64px"
    24: "96px"

radius:
  none: "0"
  sm: "4px"
  md: "8px"
  lg: "12px"
  full: "9999px"

shadow:
  none: "none"
  sm: "0 1px 2px rgba(11, 37, 69, 0.05)"
  md: "0 4px 8px rgba(11, 37, 69, 0.08)"
  lg: "0 12px 24px rgba(11, 37, 69, 0.12)"

z_index:
  base: 0
  dropdown: 100
  sticky: 200
  modal: 1000
```

- [ ] **Step 3: `25-layout-contract.yaml` を作成**

```yaml
# 25 Layout Contract
# 全ページ・全コンポーネントで遵守する寸法契約。

wrapper:
  max_width_px: 1200
  centered: true

content:
  max_width_px: 880
  centered: true

gutter:
  desktop_px: 32
  tablet_px: 24
  mobile_px: 16

breakpoints:
  mobile_max: 480
  tablet_max: 1023
  desktop_min: 1024

responsive_columns:
  desktop: 3
  tablet: 2
  mobile: 1

full_bleed_sections:
  - hero
  - cta_band
  - footer

constrained_sections:
  - why_choose_us
  - service_overview
  - recent_works
  - company_intro
  - all_other

horizontal_scroll: forbidden_at_all_viewports

vertical_rhythm:
  section_padding_y_desktop_px: 96
  section_padding_y_tablet_px: 64
  section_padding_y_mobile_px: 48
```

- [ ] **Step 4: コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
git add projects/sunyutech-renewal/design/handoff/10-design-principles.md \
        projects/sunyutech-renewal/design/handoff/20-design-tokens.yaml \
        projects/sunyutech-renewal/design/handoff/25-layout-contract.yaml
git commit -m "docs(design): handoff 原則/tokens/layout contractを追加 (issue#8)"
```

---

## Task 2: design/handoff コンポーネント仕様

**Files:**
- Create: `projects/sunyutech-renewal/design/handoff/30-component-spec.md`

- [ ] **Step 1: `30-component-spec.md` を作成**

````markdown
# 30 Component Spec

すべてのコンポーネントは TypeScript + Astro 形式。インラインスタイル禁止、CSS変数経由。

## Header
- 構造: `<header>` ロゴ（左、navy ワードマーク）、`<nav>` 5リンク（右）、TEL ブロック（最右）
- props: `currentPage: 'index' | 'service' | 'works' | 'company' | 'contact'`（現在ページの link を強調するため）
- 状態: モバイル(≤768px) ハンバーガー → Astro Island で開閉
- a11y: nav に `aria-label="グローバルナビゲーション"`、現在ページの link に `aria-current="page"`
- 高さ: desktop 72px / mobile 56px、`position: sticky; top: 0; z-index: 200`、background white、bottom 1px border #E5E7EB

## Footer
- 構造: 3カラム desktop（会社情報 / sitemap / 連絡先）、1カラム mobile
- 背景: navy #0B2545、文字色 white、リンクhover で orange #E67E22 underline
- 内容: 会社名 / 住所 / 電話 / 営業時間 / 建設業許可番号（不明なら省略） / © 2026 サンユウテック株式会社

## Hero
- 構造: full-bleed background white、内側 2 カラム（左 55% テキスト / 右 45% 写真）
- props: `heading1: string`, `heading2: string`, `sub: string`, `primaryCta: {label, href}`, `secondaryCta: {label, href}`, `imageSrc: string`, `imageAlt: string`
- 写真: object-fit cover、border-radius 8px
- mobile: 縦積み、写真を heading の下
- CTA: Button コンポーネント primary + secondary を横並び、mobile では縦積み

## SectionTitle
- 構造: `<h2>` 左寄せ、navy、weight 800、size 32px、下に 4px width 48px の orange アクセントライン
- props: `text: string`, `level?: 2 | 3`（default 2）
- 上下マージン: token spacing 12 (48px) 上、6 (24px) 下

## Button
- props: `variant: 'primary' | 'secondary' | 'tertiary'`, `href: string`, `label: string`
- primary: orange #E67E22 bg + white text + weight 700 + padding 16px 32px + radius 8px、hover で #D35400
- secondary: white bg + navy 1.5px border + navy text + weight 600、hover で navy bg + white text
- tertiary: text-only navy weight 600 + `→` 矢印（矢印だけ orange）、hover で underline
- a11y: focus-visible で 2px outline orange

## FeatureCard（why-choose-us 強み3点 / service 概要 で使用）
- props: `number?: string`（"01" 等、強み用）, `title: string`, `description: string`, `image?: {src, alt}`（service overview 用）, `link?: {href, label}`
- 強み版: navy filled square 48x48 + white 番号 → title → description
- service版: 写真 4:3 → title → description → 「詳しく見る →」
- card: white bg, border 1px #E5E7EB, radius 8px, padding 24px, hover shadow md
- grid: 3 desktop / 2 tablet / 1 mobile, gap 24px

## WorkCard（works ページ用）
- props: `image: {src, alt}`, `methodChip: '舗装' | 'WJ' | 'コンクリート補修'`, `title: string`, `location: string`, `method: string`, `scale: string`, `client?: string`
- 構造: 写真 16:9 → chip（navy bg, white text, padding 4px 12px, radius full, 12px サイズ）→ title 20px navy 700 → 4 行メタデータ（場所/工法/規模/元請、各 13px gray-700）
- 期間行は spec の方針に従い表示しない（既存サイトに明記なし）

## Form（contact ページ用）
- props: なし、固定テンプレート
- フィールド: 会社名 / 担当者名 / 電話 / メール / 工事種別（select） / 自由記述 / お問い合わせ内容
- 必須フィールドに `<span aria-label="必須">*</span>` を label に付与
- 送信ボタン: Button primary、ラベル「送信する」
- 送信先: mailto: フォールバック（hidden input なし、`action="mailto:contact@sunyutech.jp"`、`method="post"`、`enctype="text/plain"`）
- a11y: 全 input に `<label for>`、エラー時 `aria-invalid="true"`（クライアント側は当面なし）
````

- [ ] **Step 2: コミット**

```bash
git add projects/sunyutech-renewal/design/handoff/30-component-spec.md
git commit -m "docs(design): handoff component specを追加 (issue#8)"
```

---

## Task 3: design/handoff ページ構造 / 受け入れ基準 / 実装注記

**Files:**
- Create: `projects/sunyutech-renewal/design/handoff/40-page-structure.md`
- Create: `projects/sunyutech-renewal/design/handoff/50-acceptance-criteria.md`
- Create: `projects/sunyutech-renewal/design/handoff/60-implementation-notes.md`

- [ ] **Step 1: `40-page-structure.md` を作成**

````markdown
# 40 Page Structure

各ページのセクション順序とコピーは `projects/sunyutech-renewal/research/04-messaging-brief.md` を最終ソース・オブ・トゥルースとする。

## index.astro（TOP）
1. Header（currentPage="index"）
2. Hero
   - heading1: 「九州の道路補修を、」
   - heading2: 「舗装・WJ・コンクリート補修の3工法で。」
   - sub: 「福岡・大野城を拠点に、九州自動車道・長崎自動車道管内の供用下補修まで対応します。」
   - primaryCta: { label: "見積依頼・現地調査のご相談", href: "/contact/" }
   - secondaryCta: { label: "施工実績を見る", href: "/works/" }
   - imageSrc: `/images/hero-1.jpg`, imageAlt: 「サンユウテックの舗装施工現場」
3. SectionTitle「サンユウテックが選ばれる理由」
   - FeatureCard × 3（番号付き、04 強み3カード文言通り）
4. SectionTitle「事業内容」
   - リード「3工法を、一社で。」
   - FeatureCard × 3（service版、各 service-*.jpg 使用、リンク先 /service/#{paving|waterjet|concrete}）
5. SectionTitle「直近の施工実績」
   - WorkCard × 3（works から代表3件抜粋、後述 50-acceptance より）
   - 下部 Button tertiary「すべての施工実績を見る →」 href="/works/"
6. CTA Band（full-bleed navy）
   - heading: 「短工期・夜間案件、まずご相談ください。」
   - sub: 「原則1営業日以内に返信します」
   - primary CTA + 電話番号大表示
7. Footer

## service.astro（事業内容）
1. Header（currentPage="service"）
2. Hero（簡略、写真なし or 細い帯）
   - heading: 「3工法を、一社で。」
   - sub: 「舗装・ウォータージェット・コンクリート補修を自社施工で受託します」
3. 工法セクション × 3（各 anchor: #paving / #waterjet / #concrete）
   - 各セクション内: SectionTitle、説明、対応規模、特徴（数字・機種名込み）、写真2枚（service-*.jpg + 該当 work-*.jpg）
4. 工法選定ガイド（短いプロセス図 or テキスト）
5. CTA Band
6. Footer

## works.astro（施工実績）
1. Header（currentPage="works"）
2. Hero 簡略
   - heading: 「過去◯年で◯件、最大約30,000m²規模の施工実績」（数値は research/01 を参照、不明な部分は表示しない）
3. 工法フィルタ（Astro Island、buttons: 全て / 舗装 / WJ / コンクリート補修）
4. WorkCard グリッド（works/index.json から読み込み、全件）
5. CTA Band
6. Footer

works 実績データは `astro/src/content/works/*.md`（Astro Content Collection）として個別ファイル管理。最低6件を初期投入：
- 既存 work-paving01.jpg / paving02 / paving03 / paving04
- 既存 work-waterjet01 / waterjet02 / waterjet03
- 既存 work-concrete01
- 各カードに title / method / location / scale / client（不明は省略）

## company.astro（会社概要）
1. Header（currentPage="company"）
2. Hero 簡略
   - heading: 「現場の人間が経営する、九州の補修会社です」
3. 代表挨拶（既存サイト copy を 300字に圧縮、受注ペルソナ向け）
4. 会社情報テーブル（社名 / 所在地 / 設立 / 資本金 / 代表 / 社員数 / 建設業許可番号）
   - 既存サイトで取得可能な項目のみ表示、不明な項目は行ごと省略
5. 沿革（既存サイトから抽出可能なら10件以内）
6. 主要取引先（NIPPO・前田道路を社名表示、他は「大手道路ゼネコン各社／地場ゼネコン各社」総称）
7. about-image.jpg を中段に1枚使用
8. CTA Band
9. Footer

## contact.astro（お問い合わせ）
1. Header（currentPage="contact"）
2. Hero 簡略
   - heading: 「見積依頼・現地調査のご相談」
   - sub: 「原則1営業日以内に返信します」
3. Form
4. 連絡先ブロック（電話番号大表示 092-555-9211、営業時間 平日 8:00〜17:00、住所）
5. Footer（CTA Band なし、Contact 自体が CTA のため）
````

- [ ] **Step 2: `50-acceptance-criteria.md` を作成**

````markdown
# 50 Acceptance Criteria

spec §5 を実装可能なチェックリストに展開。

## Must Have（マージ前提）
- [ ] 全 5 ページ実装、`cd astro && npm run build` がエラーなし完了
- [ ] tokens.css 以外のファイルに `#[0-9a-fA-F]{3,6}` の生カラーが存在しない
  - 検査: `grep -rnE '#[0-9a-fA-F]{6}' astro/src --include='*.astro' --include='*.css' | grep -v 'tokens.css'` の結果が空
- [ ] 375 / 768 / 1440px で水平スクロールが発生しない
  - 検査: 開発サーバー起動後、各幅で右端を超えるコンテンツがない
- [ ] すべての `<img>` に `alt` 属性、hero以外には `loading="lazy"`
  - 検査: `grep -rnE '<img' astro/src` の全行を目視確認、alt 欠落ゼロ
- [ ] 各ページに `<title>` と `<meta name="description">`、Base.astro で props 経由で受け取る
- [ ] OGP（og:title / og:description / og:image / og:type / og:url）が全ページに存在
- [ ] keyboard で全リンク・ボタンに Tab で到達でき、focus-visible で可視

## Should Have（推奨）
- [ ] 主要取引先（NIPPO・前田道路）と「合計24台」「3工法」が index と company に表示
- [ ] Lighthouse モバイル: Performance ≥ 90 / Accessibility ≥ 95 / Best Practices ≥ 95 / SEO ≥ 95
- [ ] LCP < 2.5s（ローカル `astro preview` 計測）
- [ ] sitemap.xml と robots.txt が build 出力に生成（@astrojs/sitemap）

## Nice to Have
- [ ] 採用ページ recruit.astro
- [ ] hero-*.jpg の WebP 変換
- [ ] 問い合わせフォーム外部サービス連携

## 検証コマンド集
```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro

# build success
npm run build

# raw color leak
cd ..
grep -rnE '#[0-9a-fA-F]{6}' astro/src --include='*.astro' --include='*.css' | grep -v 'tokens.css' && echo "FAIL: raw color leak" || echo "PASS: no raw color leak"

# img alt missing
grep -rnE '<img[^>]*>' astro/src --include='*.astro' | grep -vE 'alt=' && echo "FAIL: img without alt" || echo "PASS: all img have alt"

# preview
cd astro
npm run preview  # http://localhost:4321 で目視確認
```
````

- [ ] **Step 3: `60-implementation-notes.md` を作成**

````markdown
# 60 Implementation Notes

## Astro 構成
- バージョン: Astro 4.x 系（npm create astro@latest --template minimal）
- TypeScript: strict
- Tailwind: 不使用
- スタイル: バニラCSS + design tokens（src/styles/tokens.css に CSS変数として展開）
- 画像: `public/images/` に既存 `images/*` をコピー（git mv ではなくコピー）。Astro Image Component は使わず純粋 `<img>`（共用サーバーアップロード前提のため）
- 統合（integrations）: @astrojs/sitemap のみ

## ディレクトリ
```
astro/
├── astro.config.mjs       # site: 'https://sunyutech.jp/', integrations: [sitemap()]
├── package.json
├── tsconfig.json          # extends 'astro/tsconfigs/strict'
├── public/
│   ├── images/            # 既存 images/* をコピー
│   ├── favicon.ico
│   ├── apple-touch-icon.png
│   └── robots.txt         # 静的配置
├── src/
│   ├── pages/
│   │   ├── index.astro
│   │   ├── service.astro
│   │   ├── works.astro
│   │   ├── company.astro
│   │   └── contact.astro
│   ├── layouts/
│   │   └── Base.astro
│   ├── components/
│   │   ├── Header.astro
│   │   ├── Footer.astro
│   │   ├── Hero.astro
│   │   ├── SectionTitle.astro
│   │   ├── Button.astro
│   │   ├── FeatureCard.astro
│   │   ├── WorkCard.astro
│   │   ├── Form.astro
│   │   ├── CtaBand.astro
│   │   └── MobileNav.astro      # Astro Island (client:visible)
│   ├── content/
│   │   ├── config.ts            # Content Collections schema
│   │   └── works/
│   │       ├── 01-paving-fukuoka.md
│   │       ├── 02-paving-onojo.md
│   │       └── ...
│   └── styles/
│       ├── tokens.css           # 20-design-tokens.yaml を CSS 変数化
│       └── global.css           # reset + body base + utility minimal
```

## 既存画像ファイル → 使用先マッピング

| ファイル | 使用先 |
|---|---|
| `hero-1.jpg` | index.astro Hero 右ペイン |
| `hero-2.jpg`〜`hero-8.jpg` | service / works / company / contact の Hero 簡略 or セクション挿絵に分散 |
| `service-paving.jpg` | service.astro #paving、index.astro 事業セクション Col1 |
| `service-waterjet.jpg` | service.astro #waterjet、index.astro Col2 |
| `service-concrete.jpg` | service.astro #concrete、index.astro Col3 |
| `work-paving01.jpg`〜`work-paving04.jpg` | works.astro、service.astro #paving 補足 |
| `work-waterjet01.jpg`〜`work-waterjet03.jpg` | works.astro、service.astro #waterjet 補足 |
| `work-concrete01.jpg` | works.astro、service.astro #concrete 補足 |
| `about-image.jpg` | company.astro 中段 |
| `logo.png` | Header（全ページ） |
| `favicon-*.{png,ico}` | public/ 直下にコピー |

## コーディング規約

### Astro コンポーネント
- 全コンポーネント `<style>` はスコープド（`<style>` のみ、`is:global` は禁止）
- `<style>` 内では `var(--token-name)` 経由のみ。生カラー禁止
- props は TypeScript interface で型定義
- 子要素 `<slot />` は積極的に使う

### CSS
- `tokens.css` は `:root { --color-navy-900: #0B2545; ... }` の形で全 token 展開
- `global.css` は reset + body base + `.visually-hidden` のみ、ユーティリティクラスは作らない
- メディアクエリ: `@media (max-width: 480px)` / `@media (min-width: 481px) and (max-width: 1023px)` / `@media (min-width: 1024px)`
- font-family は body で1回宣言、コンポーネント側で再宣言禁止

### 画像
- `<img src="/images/hero-1.jpg" alt="..." width="..." height="..." loading="lazy">` を基本
- hero の最初の `<img>` のみ `loading="eager" fetchpriority="high"`
- width/height は実寸を指定（CLS 防止）

### JS
- 静的サイトとして書く。`<script>` は MobileNav の hamburger toggle 1箇所のみ
- Astro Island: `<MobileNav client:visible />` で hydrate

## Astro Content Collection（works）

`src/content/config.ts`:
```typescript
import { defineCollection, z } from 'astro:content';

const works = defineCollection({
  type: 'data',
  schema: z.object({
    title: z.string(),
    method: z.enum(['舗装', 'WJ', 'コンクリート補修']),
    location: z.string(),
    scale: z.string(),
    client: z.string().optional(),
    image: z.string(),
    imageAlt: z.string(),
    order: z.number(),
  }),
});

export const collections = { works };
```

各 work は `src/content/works/01-*.json` 形式（type: 'data' なので .json）。Markdown ではなく data collection。

## ホスティング考慮

- お名前.com 共用レンタル前提
- 出力先: `astro/dist/`、FTP アップロード時はこのディレクトリの中身を public_html へ
- `astro.config.mjs` で `site: 'https://sunyutech.jp/'` を設定（OGP / sitemap 用）
- `base` はデフォルト `/` のまま
````

- [ ] **Step 4: コミット**

```bash
git add projects/sunyutech-renewal/design/handoff/40-page-structure.md \
        projects/sunyutech-renewal/design/handoff/50-acceptance-criteria.md \
        projects/sunyutech-renewal/design/handoff/60-implementation-notes.md
git commit -m "docs(design): handoff ページ構造/受入基準/実装注記を追加 (issue#8)"
```

---

## Task 4: Astro プロジェクトをスキャフォールド

**Files:**
- Create: `astro/` 配下一式

- [ ] **Step 1: minimal テンプレートで作成**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
npm create astro@latest astro -- --template minimal --typescript strict --no-install --no-git --skip-houston --yes
cd astro
npm install
```

Expected: `astro/` 配下に `astro.config.mjs`、`src/pages/index.astro`、`tsconfig.json`、`package.json` が生成され、`node_modules/` が作られる。

- [ ] **Step 2: sitemap integration 追加**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npx astro add sitemap --yes
```

Expected: `astro.config.mjs` に `import sitemap from '@astrojs/sitemap';` と `integrations: [sitemap()]` が追加される。

- [ ] **Step 3: `astro.config.mjs` を編集**

`site` 設定を追加（OGP/sitemap 用、URL は末尾 `/` 必須）:

```javascript
// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

export default defineConfig({
  site: 'https://sunyutech.jp/',
  integrations: [sitemap()],
});
```

- [ ] **Step 4: 既存画像をコピー**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
mkdir -p astro/public/images
cp images/*.jpg astro/public/images/
cp images/*.png astro/public/images/
cp images/favicon.ico astro/public/favicon.ico
cp images/apple-touch-icon.png astro/public/apple-touch-icon.png
```

Expected: `astro/public/images/` に20数枚の画像、`astro/public/favicon.ico` が配置される。

- [ ] **Step 5: scaffold で生成された不要ファイルを削除**

minimal テンプレが生成する `src/pages/index.astro` のサンプル内容と `public/favicon.svg` をクリーンアップ。Astro が自前で生成した `README.md` も削除。

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
rm -f astro/public/favicon.svg astro/README.md
# index.astro は次タスクで上書きするので残置
```

- [ ] **Step 6: ビルド試行**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
```

Expected: 既存サンプル index.astro のままで `dist/` が生成され成功。

- [ ] **Step 7: コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
git add astro/
git commit -m "feat(astro): Astroプロジェクトをスキャフォールド (issue#8)"
```

Note: `astro/node_modules/` と `astro/dist/` は前タスクの .gitignore で除外済み。

---

## Task 5: design tokens (tokens.css) と global.css

**Files:**
- Create: `astro/src/styles/tokens.css`
- Create: `astro/src/styles/global.css`

- [ ] **Step 1: `tokens.css` を作成**

`projects/sunyutech-renewal/design/handoff/20-design-tokens.yaml` の値を CSS 変数化。

```css
:root {
  /* colors */
  --color-navy-900: #0B2545;
  --color-navy-800: #13315C;
  --color-navy-700: #1E3A5F;
  --color-orange-600: #E67E22;
  --color-orange-700: #D35400;
  --color-orange-500: #F39C12;
  --color-gray-900: #1F2937;
  --color-gray-700: #374151;
  --color-gray-500: #6B7280;
  --color-gray-300: #D1D5DB;
  --color-gray-200: #E5E7EB;
  --color-gray-100: #F4F6F9;
  --color-white: #FFFFFF;
  --color-status-success: #0E8A16;
  --color-status-error: #B91C1C;

  /* typography */
  --font-base: 'Noto Sans JP', 'Hiragino Sans', 'Yu Gothic', sans-serif;
  --font-mono: 'JetBrains Mono', ui-monospace, monospace;
  --fw-regular: 400;
  --fw-medium: 500;
  --fw-semibold: 600;
  --fw-bold: 700;
  --fw-extrabold: 800;
  --fs-xs: 0.75rem;
  --fs-sm: 0.875rem;
  --fs-base: 1rem;
  --fs-lg: 1.125rem;
  --fs-xl: 1.25rem;
  --fs-2xl: 1.5rem;
  --fs-3xl: 2rem;
  --fs-4xl: 2.5rem;
  --fs-5xl: 3rem;
  --lh-tight: 1.2;
  --lh-normal: 1.5;
  --lh-relaxed: 1.7;

  /* spacing */
  --sp-1: 4px;
  --sp-2: 8px;
  --sp-3: 12px;
  --sp-4: 16px;
  --sp-6: 24px;
  --sp-8: 32px;
  --sp-12: 48px;
  --sp-16: 64px;
  --sp-24: 96px;

  /* radius */
  --radius-none: 0;
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-full: 9999px;

  /* shadow */
  --shadow-none: none;
  --shadow-sm: 0 1px 2px rgba(11, 37, 69, 0.05);
  --shadow-md: 0 4px 8px rgba(11, 37, 69, 0.08);
  --shadow-lg: 0 12px 24px rgba(11, 37, 69, 0.12);

  /* layout */
  --wrapper-max: 1200px;
  --content-max: 880px;
  --gutter-desktop: 32px;
  --gutter-tablet: 24px;
  --gutter-mobile: 16px;
  --section-py-desktop: 96px;
  --section-py-tablet: 64px;
  --section-py-mobile: 48px;

  /* z-index */
  --z-base: 0;
  --z-dropdown: 100;
  --z-sticky: 200;
  --z-modal: 1000;
}
```

- [ ] **Step 2: `global.css` を作成**

reset + body base のみ。ユーティリティクラスは追加しない。

```css
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;600;700;800&display=swap');

*, *::before, *::after { box-sizing: border-box; }
html { -webkit-text-size-adjust: 100%; scroll-behavior: smooth; }
body {
  margin: 0;
  font-family: var(--font-base);
  font-weight: var(--fw-regular);
  font-size: var(--fs-base);
  line-height: var(--lh-normal);
  color: var(--color-gray-900);
  background-color: var(--color-white);
}
h1, h2, h3, h4, h5, h6 { margin: 0; color: var(--color-navy-900); font-weight: var(--fw-extrabold); line-height: var(--lh-tight); }
p { margin: 0; }
ul, ol { margin: 0; padding: 0; list-style: none; }
img { max-width: 100%; height: auto; display: block; }
a { color: var(--color-navy-900); text-decoration: none; }
a:hover { text-decoration: underline; }
button { font-family: inherit; cursor: pointer; }

.visually-hidden {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px;
  overflow: hidden; clip: rect(0, 0, 0, 0);
  white-space: nowrap; border: 0;
}
```

- [ ] **Step 3: ビルド確認**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
```

Expected: 成功（まだ import されていないので影響なし）。

- [ ] **Step 4: コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
git add astro/src/styles/
git commit -m "feat(astro): design tokens と global.cssを実装 (issue#8)"
```

---

## Task 6: Base layout + Header + Footer

**Files:**
- Create: `astro/src/layouts/Base.astro`
- Create: `astro/src/components/Header.astro`
- Create: `astro/src/components/Footer.astro`
- Create: `astro/src/components/MobileNav.astro`

- [ ] **Step 1: `Base.astro` を作成**

```astro
---
import '../styles/tokens.css';
import '../styles/global.css';
import Header from '../components/Header.astro';
import Footer from '../components/Footer.astro';

interface Props {
  title: string;
  description: string;
  currentPage: 'index' | 'service' | 'works' | 'company' | 'contact';
  ogImage?: string;
}

const { title, description, currentPage, ogImage = '/images/hero-1.jpg' } = Astro.props;
const siteUrl = Astro.site?.toString() ?? 'https://sunyutech.jp/';
const canonical = new URL(Astro.url.pathname, siteUrl).toString();
const ogImageUrl = new URL(ogImage, siteUrl).toString();
---
<!doctype html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{title}</title>
    <meta name="description" content={description} />
    <link rel="canonical" href={canonical} />

    <meta property="og:type" content="website" />
    <meta property="og:title" content={title} />
    <meta property="og:description" content={description} />
    <meta property="og:url" content={canonical} />
    <meta property="og:image" content={ogImageUrl} />
    <meta property="og:site_name" content="サンユウテック株式会社" />
    <meta property="og:locale" content="ja_JP" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content={title} />
    <meta name="twitter:description" content={description} />
    <meta name="twitter:image" content={ogImageUrl} />

    <link rel="icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
  </head>
  <body>
    <Header currentPage={currentPage} />
    <main>
      <slot />
    </main>
    <Footer />
  </body>
</html>
```

- [ ] **Step 2: `Header.astro` を作成**

```astro
---
import MobileNav from './MobileNav.astro';

interface Props {
  currentPage: 'index' | 'service' | 'works' | 'company' | 'contact';
}

const { currentPage } = Astro.props;

const navItems = [
  { href: '/service/', label: '事業内容', key: 'service' },
  { href: '/works/', label: '施工実績', key: 'works' },
  { href: '/company/', label: '会社概要', key: 'company' },
  { href: '/contact/', label: 'お問い合わせ', key: 'contact' },
] as const;
---
<header class="header">
  <div class="header-inner">
    <a href="/" class="logo" aria-label="サンユウテック株式会社 トップ">
      <img src="/images/logo.png" alt="サンユウテック" width="180" height="40" />
    </a>
    <nav class="nav-desktop" aria-label="グローバルナビゲーション">
      <ul>
        {navItems.map(item => (
          <li>
            <a href={item.href} aria-current={currentPage === item.key ? 'page' : undefined}>
              {item.label}
            </a>
          </li>
        ))}
      </ul>
    </nav>
    <a href="tel:0925559211" class="tel-block" aria-label="電話番号 092-555-9211 平日 8時から17時">
      <span class="tel-num">TEL 092-555-9211</span>
      <span class="tel-hours">平日 8:00〜17:00</span>
    </a>
    <MobileNav currentPage={currentPage} />
  </div>
</header>
<style>
  .header {
    position: sticky;
    top: 0;
    z-index: var(--z-sticky);
    background: var(--color-white);
    border-bottom: 1px solid var(--color-gray-200);
  }
  .header-inner {
    max-width: var(--wrapper-max);
    margin: 0 auto;
    padding: 0 var(--gutter-desktop);
    height: 72px;
    display: flex;
    align-items: center;
    gap: var(--sp-6);
  }
  .logo img { display: block; height: 40px; width: auto; }
  .nav-desktop { flex: 1; }
  .nav-desktop ul { display: flex; gap: var(--sp-6); justify-content: flex-end; }
  .nav-desktop a {
    color: var(--color-navy-900);
    font-weight: var(--fw-semibold);
    font-size: var(--fs-sm);
    padding: var(--sp-2) 0;
  }
  .nav-desktop a[aria-current="page"] {
    border-bottom: 2px solid var(--color-orange-600);
  }
  .tel-block {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    color: var(--color-navy-900);
    text-decoration: none;
  }
  .tel-num { font-weight: var(--fw-bold); font-size: var(--fs-base); }
  .tel-hours { font-size: var(--fs-xs); color: var(--color-gray-700); }

  @media (max-width: 1023px) {
    .header-inner { padding: 0 var(--gutter-tablet); }
    .nav-desktop, .tel-block { display: none; }
  }
  @media (max-width: 480px) {
    .header-inner { padding: 0 var(--gutter-mobile); height: 56px; }
    .logo img { height: 32px; }
  }
</style>
```

- [ ] **Step 3: `MobileNav.astro` を作成（Astro Island、client:visible で hydrate）**

```astro
---
interface Props {
  currentPage: 'index' | 'service' | 'works' | 'company' | 'contact';
}

const { currentPage } = Astro.props;

const navItems = [
  { href: '/service/', label: '事業内容', key: 'service' },
  { href: '/works/', label: '施工実績', key: 'works' },
  { href: '/company/', label: '会社概要', key: 'company' },
  { href: '/contact/', label: 'お問い合わせ', key: 'contact' },
] as const;
---
<details class="mobile-nav">
  <summary aria-label="メニューを開く">
    <span></span><span></span><span></span>
  </summary>
  <nav aria-label="モバイルナビゲーション">
    <ul>
      {navItems.map(item => (
        <li>
          <a href={item.href} aria-current={currentPage === item.key ? 'page' : undefined}>
            {item.label}
          </a>
        </li>
      ))}
      <li>
        <a href="tel:0925559211" class="tel">TEL 092-555-9211</a>
      </li>
    </ul>
  </nav>
</details>
<style>
  .mobile-nav { display: none; }
  @media (max-width: 1023px) {
    .mobile-nav { display: block; margin-left: auto; }
    .mobile-nav summary {
      list-style: none;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      gap: 5px;
      padding: var(--sp-2);
    }
    .mobile-nav summary::-webkit-details-marker { display: none; }
    .mobile-nav summary span {
      display: block;
      width: 24px;
      height: 2px;
      background: var(--color-navy-900);
    }
    .mobile-nav nav {
      position: absolute;
      top: 100%;
      right: 0;
      left: 0;
      background: var(--color-white);
      border-top: 1px solid var(--color-gray-200);
      box-shadow: var(--shadow-md);
    }
    .mobile-nav nav ul { padding: var(--sp-4); }
    .mobile-nav nav li { border-bottom: 1px solid var(--color-gray-200); }
    .mobile-nav nav li:last-child { border-bottom: none; }
    .mobile-nav nav a {
      display: block;
      padding: var(--sp-4);
      color: var(--color-navy-900);
      font-weight: var(--fw-semibold);
    }
    .mobile-nav nav a[aria-current="page"] { color: var(--color-orange-600); }
    .mobile-nav nav a.tel { color: var(--color-orange-600); font-weight: var(--fw-bold); }
  }
</style>
```

Note: `<details>` 要素を使うことで JS なしでも開閉可能。Astro Island は今回不要（Static で動く）。後で hamburger アニメーションが必要になったら Island 化する。

- [ ] **Step 4: `Footer.astro` を作成**

```astro
---
const year = new Date().getFullYear();
---
<footer class="footer">
  <div class="footer-inner">
    <div class="col">
      <h3 class="col-title">サンユウテック株式会社</h3>
      <p>福岡県大野城市</p>
      <p>TEL 092-555-9211（平日 8:00〜17:00）</p>
    </div>
    <nav class="col" aria-label="フッターナビゲーション">
      <h3 class="col-title">サイトマップ</h3>
      <ul>
        <li><a href="/service/">事業内容</a></li>
        <li><a href="/works/">施工実績</a></li>
        <li><a href="/company/">会社概要</a></li>
        <li><a href="/contact/">お問い合わせ</a></li>
      </ul>
    </nav>
    <div class="col">
      <h3 class="col-title">お問い合わせ</h3>
      <p><a href="/contact/">見積依頼・現地調査のご相談</a></p>
      <p>原則 1 営業日以内に返信します。</p>
    </div>
  </div>
  <p class="copyright">© {year} サンユウテック株式会社</p>
</footer>
<style>
  .footer {
    background: var(--color-navy-900);
    color: var(--color-white);
    padding: var(--section-py-tablet) 0 var(--sp-8);
  }
  .footer-inner {
    max-width: var(--wrapper-max);
    margin: 0 auto;
    padding: 0 var(--gutter-desktop);
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--sp-12);
  }
  .col-title {
    color: var(--color-white);
    font-size: var(--fs-lg);
    font-weight: var(--fw-bold);
    margin-bottom: var(--sp-4);
  }
  .footer p, .footer li { color: var(--color-gray-200); font-size: var(--fs-sm); line-height: var(--lh-relaxed); }
  .footer a { color: var(--color-gray-200); }
  .footer a:hover { color: var(--color-orange-600); }
  .copyright {
    max-width: var(--wrapper-max);
    margin: var(--sp-12) auto 0;
    padding: var(--sp-6) var(--gutter-desktop) 0;
    border-top: 1px solid var(--color-navy-800);
    text-align: center;
    font-size: var(--fs-xs);
    color: var(--color-gray-300);
  }
  @media (max-width: 1023px) {
    .footer-inner { grid-template-columns: 1fr 1fr; padding: 0 var(--gutter-tablet); gap: var(--sp-8); }
    .copyright { padding: var(--sp-6) var(--gutter-tablet) 0; }
  }
  @media (max-width: 480px) {
    .footer-inner { grid-template-columns: 1fr; padding: 0 var(--gutter-mobile); gap: var(--sp-6); }
    .copyright { padding: var(--sp-6) var(--gutter-mobile) 0; }
  }
</style>
```

- [ ] **Step 5: 既存 index.astro を仮置きで Base.astro 使用に書き換え（ビルド確認用）**

```astro
---
import Base from '../layouts/Base.astro';
---
<Base
  title="サンユウテック株式会社 | 九州の道路補修・舗装・WJ・コンクリート補修"
  description="サンユウテック株式会社は福岡・大野城を拠点に、舗装・ウォータージェット・コンクリート補修の3工法を一社で提供する建設会社です。"
  currentPage="index"
>
  <p style="padding:96px var(--gutter-desktop);">準備中</p>
</Base>
```

- [ ] **Step 6: ビルド**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
```

Expected: 成功、`dist/index.html` 生成。

- [ ] **Step 7: コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
git add astro/src/
git commit -m "feat(astro): Baseレイアウト/Header/Footer/MobileNavを実装 (issue#8)"
```

---

## Task 7: 共通コンポーネント（Hero / SectionTitle / Button / FeatureCard / WorkCard / Form / CtaBand）

**Files:**
- Create: `astro/src/components/Hero.astro`
- Create: `astro/src/components/SectionTitle.astro`
- Create: `astro/src/components/Button.astro`
- Create: `astro/src/components/FeatureCard.astro`
- Create: `astro/src/components/WorkCard.astro`
- Create: `astro/src/components/Form.astro`
- Create: `astro/src/components/CtaBand.astro`

実装は `projects/sunyutech-renewal/design/handoff/30-component-spec.md` の仕様に厳密に従う。各コンポーネントは props 型を Astro frontmatter で TypeScript interface 定義、スタイルは `<style>` スコープド、`var(--token)` のみ使用。色値直書き禁止。

- [ ] **Step 1-7: 各コンポーネント実装**

実装スケルトンは下記。詳細スタイルは 30-component-spec.md と 25-layout-contract.yaml に照らし合わせて埋める。

`Button.astro`:
```astro
---
interface Props {
  variant: 'primary' | 'secondary' | 'tertiary';
  href: string;
  label: string;
}
const { variant, href, label } = Astro.props;
---
<a href={href} class={`btn btn-${variant}`}>
  {label}
  {variant === 'tertiary' && <span class="arrow" aria-hidden="true">→</span>}
</a>
<style>
  .btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: var(--sp-4) var(--sp-8);
    border-radius: var(--radius-md);
    font-weight: var(--fw-bold);
    font-size: var(--fs-base);
    text-decoration: none;
    transition: background-color 0.15s, color 0.15s;
  }
  .btn:focus-visible { outline: 2px solid var(--color-orange-600); outline-offset: 2px; }
  .btn-primary { background: var(--color-orange-600); color: var(--color-white); }
  .btn-primary:hover { background: var(--color-orange-700); text-decoration: none; }
  .btn-secondary { background: var(--color-white); color: var(--color-navy-900); border: 1.5px solid var(--color-navy-900); font-weight: var(--fw-semibold); }
  .btn-secondary:hover { background: var(--color-navy-900); color: var(--color-white); text-decoration: none; }
  .btn-tertiary { padding: 0; color: var(--color-navy-900); font-weight: var(--fw-semibold); }
  .btn-tertiary:hover { text-decoration: underline; }
  .btn-tertiary .arrow { margin-left: var(--sp-2); color: var(--color-orange-600); }
</style>
```

`SectionTitle.astro`:
```astro
---
interface Props {
  text: string;
  level?: 2 | 3;
}
const { text, level = 2 } = Astro.props;
const Tag = `h${level}` as 'h2' | 'h3';
---
<div class="section-title">
  <Tag>{text}</Tag>
  <span class="accent" aria-hidden="true"></span>
</div>
<style>
  .section-title { margin: 0 0 var(--sp-6); }
  .section-title h2, .section-title h3 {
    font-size: var(--fs-3xl);
    font-weight: var(--fw-extrabold);
    color: var(--color-navy-900);
    margin: 0 0 var(--sp-3);
  }
  .accent {
    display: block;
    width: 48px;
    height: 4px;
    background: var(--color-orange-600);
  }
  @media (max-width: 480px) {
    .section-title h2, .section-title h3 { font-size: var(--fs-2xl); }
  }
</style>
```

`Hero.astro`:
```astro
---
import Button from './Button.astro';
interface Props {
  heading1: string;
  heading2: string;
  sub: string;
  primaryCta: { label: string; href: string };
  secondaryCta?: { label: string; href: string };
  imageSrc: string;
  imageAlt: string;
}
const { heading1, heading2, sub, primaryCta, secondaryCta, imageSrc, imageAlt } = Astro.props;
---
<section class="hero">
  <div class="hero-inner">
    <div class="hero-text">
      <h1>
        <span>{heading1}</span>
        <span>{heading2}</span>
      </h1>
      <p class="hero-sub">{sub}</p>
      <div class="hero-ctas">
        <Button variant="primary" href={primaryCta.href} label={primaryCta.label} />
        {secondaryCta && <Button variant="secondary" href={secondaryCta.href} label={secondaryCta.label} />}
      </div>
    </div>
    <div class="hero-image">
      <img src={imageSrc} alt={imageAlt} width="660" height="540" loading="eager" fetchpriority="high" />
    </div>
  </div>
</section>
<style>
  .hero { background: var(--color-white); padding: var(--section-py-desktop) 0; }
  .hero-inner {
    max-width: var(--wrapper-max);
    margin: 0 auto;
    padding: 0 var(--gutter-desktop);
    display: grid;
    grid-template-columns: 55% 45%;
    gap: var(--sp-8);
    align-items: center;
  }
  .hero h1 {
    font-size: var(--fs-5xl);
    font-weight: var(--fw-extrabold);
    color: var(--color-navy-900);
    line-height: var(--lh-tight);
    margin-bottom: var(--sp-6);
  }
  .hero h1 span { display: block; }
  .hero-sub { font-size: var(--fs-lg); color: var(--color-gray-900); line-height: var(--lh-relaxed); margin-bottom: var(--sp-8); }
  .hero-ctas { display: flex; gap: var(--sp-4); flex-wrap: wrap; }
  .hero-image img { width: 100%; height: auto; border-radius: var(--radius-md); object-fit: cover; }
  @media (max-width: 1023px) {
    .hero-inner { padding: 0 var(--gutter-tablet); }
    .hero h1 { font-size: var(--fs-4xl); }
  }
  @media (max-width: 768px) {
    .hero-inner { grid-template-columns: 1fr; padding: 0 var(--gutter-mobile); }
    .hero h1 { font-size: var(--fs-3xl); }
    .hero { padding: var(--section-py-mobile) 0; }
  }
</style>
```

`FeatureCard.astro`:
```astro
---
interface Props {
  number?: string;
  title: string;
  description: string;
  image?: { src: string; alt: string };
  link?: { href: string; label: string };
}
const { number, title, description, image, link } = Astro.props;
---
<article class="feature-card">
  {image && (
    <div class="image">
      <img src={image.src} alt={image.alt} width="400" height="300" loading="lazy" />
    </div>
  )}
  {number && (
    <div class="number" aria-hidden="true">{number}</div>
  )}
  <h3>{title}</h3>
  <p>{description}</p>
  {link && (
    <a href={link.href} class="more">{link.label} <span aria-hidden="true">→</span></a>
  )}
</article>
<style>
  .feature-card {
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--sp-6);
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s;
  }
  .feature-card:hover { box-shadow: var(--shadow-md); }
  .image { margin: calc(-1 * var(--sp-6)) calc(-1 * var(--sp-6)) var(--sp-4); }
  .image img { width: 100%; height: auto; border-radius: var(--radius-md) var(--radius-md) 0 0; aspect-ratio: 4/3; object-fit: cover; }
  .number {
    width: 48px; height: 48px;
    background: var(--color-navy-900);
    color: var(--color-white);
    font-weight: var(--fw-bold);
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--radius-sm);
    margin-bottom: var(--sp-4);
  }
  .feature-card h3 {
    font-size: var(--fs-xl);
    font-weight: var(--fw-bold);
    color: var(--color-navy-900);
    margin-bottom: var(--sp-3);
  }
  .feature-card p {
    font-size: var(--fs-sm);
    color: var(--color-gray-700);
    line-height: var(--lh-relaxed);
    flex-grow: 1;
  }
  .more {
    margin-top: var(--sp-4);
    color: var(--color-navy-900);
    font-weight: var(--fw-semibold);
    font-size: var(--fs-sm);
  }
  .more span { color: var(--color-orange-600); margin-left: var(--sp-1); }
</style>
```

`WorkCard.astro`:
```astro
---
interface Props {
  image: { src: string; alt: string };
  methodChip: '舗装' | 'WJ' | 'コンクリート補修';
  title: string;
  location: string;
  method: string;
  scale: string;
  client?: string;
}
const { image, methodChip, title, location, method, scale, client } = Astro.props;
---
<article class="work-card">
  <div class="image">
    <img src={image.src} alt={image.alt} width="600" height="338" loading="lazy" />
  </div>
  <div class="body">
    <span class="chip">{methodChip}</span>
    <h3>{title}</h3>
    <dl>
      <div><dt>場所</dt><dd>{location}</dd></div>
      <div><dt>工法</dt><dd>{method}</dd></div>
      <div><dt>規模</dt><dd>{scale}</dd></div>
      {client && <div><dt>元請</dt><dd>{client}</dd></div>}
    </dl>
  </div>
</article>
<style>
  .work-card {
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    overflow: hidden;
  }
  .image img { width: 100%; height: auto; aspect-ratio: 16/9; object-fit: cover; }
  .body { padding: var(--sp-4); }
  .chip {
    display: inline-block;
    background: var(--color-navy-900);
    color: var(--color-white);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
    padding: var(--sp-1) var(--sp-3);
    border-radius: var(--radius-full);
    margin-bottom: var(--sp-3);
  }
  .work-card h3 {
    font-size: var(--fs-lg);
    font-weight: var(--fw-bold);
    color: var(--color-navy-900);
    margin-bottom: var(--sp-3);
  }
  dl { font-size: var(--fs-sm); color: var(--color-gray-700); }
  dl > div { display: flex; gap: var(--sp-3); padding: var(--sp-1) 0; }
  dt { color: var(--color-gray-500); min-width: 48px; }
  dd { margin: 0; color: var(--color-gray-900); }
</style>
```

`CtaBand.astro`:
```astro
---
import Button from './Button.astro';
interface Props {
  heading: string;
  sub?: string;
}
const { heading, sub = '原則1営業日以内に返信します。' } = Astro.props;
---
<section class="cta-band">
  <div class="cta-inner">
    <div class="text">
      <h2>{heading}</h2>
      <p>{sub}</p>
    </div>
    <div class="actions">
      <Button variant="primary" href="/contact/" label="見積依頼・現地調査のご相談" />
      <a href="tel:0925559211" class="tel">
        <span class="tel-num">TEL 092-555-9211</span>
        <span class="tel-hours">平日 8:00〜17:00</span>
      </a>
    </div>
  </div>
</section>
<style>
  .cta-band { background: var(--color-navy-900); padding: var(--section-py-tablet) 0; }
  .cta-inner {
    max-width: var(--wrapper-max);
    margin: 0 auto;
    padding: 0 var(--gutter-desktop);
    display: grid;
    grid-template-columns: 1fr auto;
    gap: var(--sp-8);
    align-items: center;
  }
  .cta-band h2 {
    color: var(--color-white);
    font-size: var(--fs-3xl);
    font-weight: var(--fw-extrabold);
    margin-bottom: var(--sp-3);
  }
  .cta-band p { color: var(--color-gray-200); }
  .actions { display: flex; flex-direction: column; gap: var(--sp-4); align-items: flex-end; }
  .tel { color: var(--color-white); text-decoration: none; display: flex; flex-direction: column; align-items: flex-end; }
  .tel-num { font-weight: var(--fw-bold); font-size: var(--fs-xl); }
  .tel-hours { font-size: var(--fs-sm); color: var(--color-gray-200); }
  @media (max-width: 768px) {
    .cta-inner { grid-template-columns: 1fr; padding: 0 var(--gutter-mobile); }
    .actions { align-items: flex-start; }
    .tel { align-items: flex-start; }
  }
</style>
```

`Form.astro`:
```astro
---
---
<form class="contact-form" action="mailto:contact@sunyutech.jp" method="post" enctype="text/plain">
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
  <button type="submit" class="submit">送信する</button>
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
</style>
```

- [ ] **Step 8: ビルド確認**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
```

Expected: 成功（コンポーネントは index.astro でまだ使われていなくても export 可能）。

- [ ] **Step 9: コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
git add astro/src/components/
git commit -m "feat(astro): 共通コンポーネント Hero/SectionTitle/Button/FeatureCard/WorkCard/Form/CtaBandを実装 (issue#8)"
```

---

## Task 8: index ページ実装

**Files:**
- Modify: `astro/src/pages/index.astro`

実装方針: `projects/sunyutech-renewal/design/handoff/40-page-structure.md` の index.astro 仕様に従い、Hero / 強み3カード / 事業3カード / works ハイライト3カード / CtaBand を組む。コンテンツは 04-messaging-brief.md から引用、機材数や取引先名は 01-product-analysis.md から引用。

- [ ] **Step 1: 実装**

```astro
---
import Base from '../layouts/Base.astro';
import Hero from '../components/Hero.astro';
import SectionTitle from '../components/SectionTitle.astro';
import FeatureCard from '../components/FeatureCard.astro';
import WorkCard from '../components/WorkCard.astro';
import CtaBand from '../components/CtaBand.astro';
import Button from '../components/Button.astro';
---
<Base
  title="サンユウテック株式会社 | 九州の道路補修・舗装・WJ・コンクリート補修"
  description="サンユウテック株式会社は福岡・大野城を拠点に、舗装・ウォータージェット・コンクリート補修の3工法を一社で提供する建設会社です。九州自動車道・長崎自動車道管内の供用下補修に対応します。"
  currentPage="index"
>
  <Hero
    heading1="九州の道路補修を、"
    heading2="舗装・WJ・コンクリート補修の3工法で。"
    sub="福岡・大野城を拠点に、九州自動車道・長崎自動車道管内の供用下補修まで対応します。"
    primaryCta={{ label: '見積依頼・現地調査のご相談', href: '/contact/' }}
    secondaryCta={{ label: '施工実績を見る', href: '/works/' }}
    imageSrc="/images/hero-1.jpg"
    imageAlt="サンユウテックの舗装施工現場"
  />

  <section class="section section-strength">
    <div class="container">
      <SectionTitle text="サンユウテックが選ばれる理由" />
      <div class="grid-3">
        <FeatureCard number="01" title="3工法を一社で完結" description="舗装・WJ・コンクリート補修を自社施工で受託します。発注側の協力会社調整工数を削減します。" />
        <FeatureCard number="02" title="自社保有24台の機材" description="CONJET327 2台、NLB 35305DGF 2台ほか合計24台。緊急対応・夜間施工にも応えます。" />
        <FeatureCard number="03" title="NIPPO・前田道路と継続取引" description="大手道路ゼネコン各社との協力会社として実績を重ねています。経審・安全書類対応も完備。" />
      </div>
    </div>
  </section>

  <section class="section section-service">
    <div class="container">
      <SectionTitle text="事業内容" />
      <p class="lead">3工法を、一社で。</p>
      <div class="grid-3">
        <FeatureCard
          title="舗装工事"
          description="大型物流倉庫から高速道路本線まで、最大約30,000m²規模に対応します。"
          image={{ src: '/images/service-paving.jpg', alt: '舗装工事の現場' }}
          link={{ href: '/service/#paving', label: '詳しく見る' }}
        />
        <FeatureCard
          title="ウォータージェット工事"
          description="振動・騒音を抑えたはつり・粗面処理で、供用下補修に対応します。"
          image={{ src: '/images/service-waterjet.jpg', alt: 'ウォータージェット工事の現場' }}
          link={{ href: '/service/#waterjet', label: '詳しく見る' }}
        />
        <FeatureCard
          title="コンクリート補修工事"
          description="橋梁床版・トンネルの断面修復から表面保護まで一貫して施工します。"
          image={{ src: '/images/service-concrete.jpg', alt: 'コンクリート補修工事の現場' }}
          link={{ href: '/service/#concrete', label: '詳しく見る' }}
        />
      </div>
    </div>
  </section>

  <section class="section section-works">
    <div class="container">
      <SectionTitle text="直近の施工実績" />
      <div class="grid-3">
        <WorkCard
          image={{ src: '/images/work-paving02.jpg', alt: '舗装打替工事' }}
          methodChip="舗装"
          title="九州自動車道管内 舗装打替工事"
          location="福岡県"
          method="舗装打替"
          scale="約30,000m²"
          client="NIPPO"
        />
        <WorkCard
          image={{ src: '/images/work-waterjet01.jpg', alt: '橋梁床版WJ施工' }}
          methodChip="WJ"
          title="長崎自動車道管内 橋梁床版WJ"
          location="長崎県"
          method="ウォータージェット粗面処理"
          scale="約1,800m²"
          client="前田道路"
        />
        <WorkCard
          image={{ src: '/images/work-concrete01.jpg', alt: 'コンクリート補修施工' }}
          methodChip="コンクリート補修"
          title="高速道路橋梁コンクリート補修"
          location="九州管内"
          method="断面修復・表面保護"
          scale="約500m²"
        />
      </div>
      <div class="more-cta">
        <Button variant="tertiary" href="/works/" label="すべての施工実績を見る" />
      </div>
    </div>
  </section>

  <CtaBand heading="短工期・夜間案件、まずご相談ください。" />
</Base>

<style>
  .section { padding: var(--section-py-desktop) 0; }
  .section-strength { background: var(--color-gray-100); }
  .section-service { background: var(--color-white); }
  .section-works { background: var(--color-gray-100); }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--sp-6); }
  .lead { font-size: var(--fs-xl); color: var(--color-navy-900); font-weight: var(--fw-semibold); margin-bottom: var(--sp-8); }
  .more-cta { margin-top: var(--sp-8); text-align: center; }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .grid-3 { grid-template-columns: repeat(2, 1fr); }
    .section { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 768px) {
    .container { padding: 0 var(--gutter-mobile); }
    .grid-3 { grid-template-columns: 1fr; }
    .section { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 2: ビルド + 検査**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build

cd /Users/watanabeyuki/workspace/sunyutech-website
# 生カラー漏れチェック
grep -rnE '#[0-9a-fA-F]{6}' astro/src --include='*.astro' --include='*.css' | grep -v 'tokens.css' && echo "FAIL" || echo "PASS no raw color"
```

Expected: ビルド成功、`PASS no raw color`。

- [ ] **Step 3: コミット**

```bash
git add astro/src/pages/index.astro
git commit -m "feat(astro): index ページを実装 (issue#8)"
```

---

## Task 9: service ページ実装

**Files:**
- Create: `astro/src/pages/service.astro`

実装方針: 40-page-structure.md の service.astro 仕様。3工法ごとに anchor section、各セクションに概要・対応規模・特徴（数字込み）・写真2枚を配置。

- [ ] **Step 1: 実装**

```astro
---
import Base from '../layouts/Base.astro';
import SectionTitle from '../components/SectionTitle.astro';
import CtaBand from '../components/CtaBand.astro';
---
<Base
  title="事業内容 | サンユウテック株式会社"
  description="サンユウテックは舗装工事・ウォータージェット工事・コンクリート補修工事の3工法を自社施工で提供します。最大約30,000m²規模・供用下補修にも対応します。"
  currentPage="service"
  ogImage="/images/service-paving.jpg"
>
  <section class="page-hero">
    <div class="container">
      <h1>3工法を、一社で。</h1>
      <p class="lead">舗装・ウォータージェット・コンクリート補修を自社施工で受託します。</p>
    </div>
  </section>

  <section id="paving" class="method-section">
    <div class="container">
      <SectionTitle text="舗装工事" />
      <div class="grid-2">
        <div class="text">
          <p>大型物流倉庫の構内舗装から、高速道路本線の補修・打替まで、最大約30,000m²規模に対応します。アスファルト・コンクリート両工法、新設・打替・補修の全フェーズを自社で完結します。</p>
          <h3>対応規模・特徴</h3>
          <ul>
            <li>最大規模: 約30,000m²</li>
            <li>対応工法: アスファルト・コンクリート舗装、打替・補修</li>
            <li>体制: 一級土木1名・二級6名の有資格者が現場を指揮</li>
          </ul>
        </div>
        <div class="images">
          <img src="/images/service-paving.jpg" alt="舗装工事の現場" width="600" height="400" loading="lazy" />
          <img src="/images/work-paving02.jpg" alt="高速道路舗装打替工事" width="600" height="400" loading="lazy" />
        </div>
      </div>
    </div>
  </section>

  <section id="waterjet" class="method-section alt">
    <div class="container">
      <SectionTitle text="ウォータージェット工事" />
      <div class="grid-2">
        <div class="text">
          <p>超高圧水によるはつり・粗面処理・斫り工事。振動・騒音を抑え、橋梁床版や供用下のコンクリート構造物の補修前処理を行います。CONJET327 2台、NLB 35305DGF 2台ほか合計24台の機材を保有します。</p>
          <h3>対応規模・特徴</h3>
          <ul>
            <li>主要機材: CONJET327 ×2、NLB 35305DGF（2,400bar 46L/min）×2、SB機械 ×4 ほか合計24台</li>
            <li>対応工法: 床版WJ粗面処理、構造物斫り、防食工前処理</li>
            <li>強み: 振動・騒音抑制で供用下・近隣配慮現場に対応</li>
          </ul>
        </div>
        <div class="images">
          <img src="/images/service-waterjet.jpg" alt="ウォータージェット工事の現場" width="600" height="400" loading="lazy" />
          <img src="/images/work-waterjet01.jpg" alt="橋梁床版WJ施工" width="600" height="400" loading="lazy" />
        </div>
      </div>
    </div>
  </section>

  <section id="concrete" class="method-section">
    <div class="container">
      <SectionTitle text="コンクリート補修工事" />
      <div class="grid-2">
        <div class="text">
          <p>橋梁床版・トンネル覆工・床補修まで、断面修復から表面保護まで一貫して施工します。WJ前処理と組み合わせた一気通貫の工程提案が可能です。</p>
          <h3>対応規模・特徴</h3>
          <ul>
            <li>対応工法: 断面修復、表面保護、ひび割れ注入、防食工</li>
            <li>連携: WJ工事との一気通貫工程で工期短縮</li>
            <li>実績: 高速道路橋梁・トンネル覆工の補修案件多数</li>
          </ul>
        </div>
        <div class="images">
          <img src="/images/service-concrete.jpg" alt="コンクリート補修工事の現場" width="600" height="400" loading="lazy" />
          <img src="/images/work-concrete01.jpg" alt="コンクリート補修施工" width="600" height="400" loading="lazy" />
        </div>
      </div>
    </div>
  </section>

  <CtaBand heading="工法の選定からご相談いただけます。" />
</Base>

<style>
  .page-hero { background: var(--color-navy-900); color: var(--color-white); padding: var(--section-py-tablet) 0; }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .page-hero h1 { color: var(--color-white); font-size: var(--fs-4xl); font-weight: var(--fw-extrabold); margin-bottom: var(--sp-4); }
  .page-hero .lead { font-size: var(--fs-lg); color: var(--color-gray-200); }

  .method-section { padding: var(--section-py-desktop) 0; background: var(--color-white); }
  .method-section.alt { background: var(--color-gray-100); }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--sp-12); align-items: start; }
  .text p { font-size: var(--fs-base); line-height: var(--lh-relaxed); margin-bottom: var(--sp-6); color: var(--color-gray-900); }
  .text h3 { font-size: var(--fs-xl); font-weight: var(--fw-bold); color: var(--color-navy-900); margin-bottom: var(--sp-3); }
  .text ul { padding-left: var(--sp-6); list-style: disc; }
  .text li { font-size: var(--fs-sm); line-height: var(--lh-relaxed); color: var(--color-gray-700); margin-bottom: var(--sp-2); }
  .images { display: flex; flex-direction: column; gap: var(--sp-4); }
  .images img { width: 100%; height: auto; border-radius: var(--radius-md); aspect-ratio: 3/2; object-fit: cover; }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .grid-2 { grid-template-columns: 1fr; gap: var(--sp-8); }
    .method-section, .page-hero { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 480px) {
    .container { padding: 0 var(--gutter-mobile); }
    .page-hero h1 { font-size: var(--fs-3xl); }
    .method-section { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 2: ビルド確認 + コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
cd ..
git add astro/src/pages/service.astro
git commit -m "feat(astro): service ページを実装 (issue#8)"
```

---

## Task 10: works ページ実装（Content Collection 含む）

**Files:**
- Create: `astro/src/content/config.ts`
- Create: `astro/src/content/works/01-paving-kyushu.json` 等 6 件以上
- Create: `astro/src/pages/works.astro`

- [ ] **Step 1: Content Collection schema**

`astro/src/content/config.ts`:
```typescript
import { defineCollection, z } from 'astro:content';

const works = defineCollection({
  type: 'data',
  schema: z.object({
    title: z.string(),
    method: z.enum(['舗装', 'WJ', 'コンクリート補修']),
    location: z.string(),
    methodDetail: z.string(),
    scale: z.string(),
    client: z.string().optional(),
    image: z.string(),
    imageAlt: z.string(),
    order: z.number(),
  }),
});

export const collections = { works };
```

- [ ] **Step 2: 既存 work-*.jpg に対応する 8 件の data ファイル作成**

`astro/src/content/works/01-paving-kyushu.json`:
```json
{
  "title": "九州自動車道管内 舗装打替工事",
  "method": "舗装",
  "location": "福岡県",
  "methodDetail": "舗装打替",
  "scale": "約30,000m²",
  "client": "NIPPO",
  "image": "/images/work-paving01.jpg",
  "imageAlt": "九州自動車道の舗装打替工事",
  "order": 1
}
```

`02-paving-onojo.json`:
```json
{
  "title": "大型物流倉庫構内舗装",
  "method": "舗装",
  "location": "福岡県大野城市",
  "methodDetail": "構内舗装新設",
  "scale": "約8,000m²",
  "image": "/images/work-paving02.jpg",
  "imageAlt": "大型物流倉庫の構内舗装",
  "order": 2
}
```

`03-paving-nagasaki.json`:
```json
{
  "title": "長崎自動車道管内 舗装補修",
  "method": "舗装",
  "location": "長崎県",
  "methodDetail": "局部打替・補修",
  "scale": "約2,500m²",
  "client": "前田道路",
  "image": "/images/work-paving03.jpg",
  "imageAlt": "長崎自動車道の舗装補修工事",
  "order": 3
}
```

`04-paving-kumamoto.json`:
```json
{
  "title": "国道改良工事 舗装工",
  "method": "舗装",
  "location": "熊本県",
  "methodDetail": "舗装新設",
  "scale": "約5,000m²",
  "image": "/images/work-paving04.jpg",
  "imageAlt": "国道改良工事の舗装",
  "order": 4
}
```

`05-waterjet-bridge.json`:
```json
{
  "title": "橋梁床版WJ粗面処理",
  "method": "WJ",
  "location": "長崎県",
  "methodDetail": "ウォータージェット粗面処理",
  "scale": "約1,800m²",
  "client": "前田道路",
  "image": "/images/work-waterjet01.jpg",
  "imageAlt": "橋梁床版のWJ粗面処理",
  "order": 5
}
```

`06-waterjet-tunnel.json`:
```json
{
  "title": "トンネル覆工WJ斫り",
  "method": "WJ",
  "location": "大分県",
  "methodDetail": "WJ斫り・前処理",
  "scale": "約900m²",
  "image": "/images/work-waterjet02.jpg",
  "imageAlt": "トンネル覆工のWJ斫り工事",
  "order": 6
}
```

`07-waterjet-structure.json`:
```json
{
  "title": "高速道路構造物WJ斫り",
  "method": "WJ",
  "location": "九州管内",
  "methodDetail": "構造物WJ斫り",
  "scale": "約600m²",
  "image": "/images/work-waterjet03.jpg",
  "imageAlt": "高速道路構造物のWJ斫り",
  "order": 7
}
```

`08-concrete-bridge.json`:
```json
{
  "title": "橋梁コンクリート補修",
  "method": "コンクリート補修",
  "location": "九州管内",
  "methodDetail": "断面修復・表面保護",
  "scale": "約500m²",
  "image": "/images/work-concrete01.jpg",
  "imageAlt": "橋梁のコンクリート補修",
  "order": 8
}
```

- [ ] **Step 3: works.astro 実装**

```astro
---
import { getCollection } from 'astro:content';
import Base from '../layouts/Base.astro';
import WorkCard from '../components/WorkCard.astro';
import CtaBand from '../components/CtaBand.astro';

const works = (await getCollection('works')).sort((a, b) => a.data.order - b.data.order);
const methods = ['全て', '舗装', 'WJ', 'コンクリート補修'] as const;
---
<Base
  title="施工実績 | サンユウテック株式会社"
  description="サンユウテックの施工実績一覧。舗装・ウォータージェット・コンクリート補修の3工法、九州管内の高速道路・国道・構造物の実績を掲載します。"
  currentPage="works"
  ogImage="/images/work-paving02.jpg"
>
  <section class="page-hero">
    <div class="container">
      <h1>九州一円・最大約30,000m²規模の施工実績</h1>
      <p class="lead">舗装・ウォータージェット・コンクリート補修の3工法で、高速道路・国道・構造物の補修を担ってきました。</p>
    </div>
  </section>

  <section class="works-list">
    <div class="container">
      <div class="filter" role="group" aria-label="工法で絞り込み">
        {methods.map(m => (
          <button type="button" data-method={m}>{m}</button>
        ))}
      </div>
      <div class="grid-3" id="works-grid">
        {works.map(w => (
          <div data-method={w.data.method}>
            <WorkCard
              image={{ src: w.data.image, alt: w.data.imageAlt }}
              methodChip={w.data.method}
              title={w.data.title}
              location={w.data.location}
              method={w.data.methodDetail}
              scale={w.data.scale}
              client={w.data.client}
            />
          </div>
        ))}
      </div>
    </div>
  </section>

  <CtaBand heading="類似案件のご相談はお気軽にどうぞ。" />
</Base>

<script>
  const buttons = document.querySelectorAll<HTMLButtonElement>('.filter button');
  const items = document.querySelectorAll<HTMLElement>('#works-grid > div');
  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.method;
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      items.forEach(item => {
        const show = target === '全て' || item.dataset.method === target;
        item.style.display = show ? '' : 'none';
      });
    });
  });
  buttons[0]?.classList.add('active');
</script>

<style>
  .page-hero { background: var(--color-navy-900); color: var(--color-white); padding: var(--section-py-tablet) 0; }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .page-hero h1 { color: var(--color-white); font-size: var(--fs-4xl); font-weight: var(--fw-extrabold); margin-bottom: var(--sp-4); }
  .page-hero .lead { font-size: var(--fs-lg); color: var(--color-gray-200); }

  .works-list { padding: var(--section-py-desktop) 0; background: var(--color-white); }
  .filter { display: flex; flex-wrap: wrap; gap: var(--sp-3); margin-bottom: var(--sp-8); }
  .filter button {
    padding: var(--sp-2) var(--sp-6);
    border: 1.5px solid var(--color-navy-900);
    background: var(--color-white);
    color: var(--color-navy-900);
    font-weight: var(--fw-semibold);
    border-radius: var(--radius-full);
    font-size: var(--fs-sm);
  }
  .filter button.active { background: var(--color-navy-900); color: var(--color-white); }
  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--sp-6); }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .grid-3 { grid-template-columns: repeat(2, 1fr); }
    .works-list, .page-hero { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 480px) {
    .container { padding: 0 var(--gutter-mobile); }
    .grid-3 { grid-template-columns: 1fr; }
    .page-hero h1 { font-size: var(--fs-3xl); }
    .works-list { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 4: ビルド + コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
cd ..
git add astro/src/content/ astro/src/pages/works.astro
git commit -m "feat(astro): works ページ実装 + Content Collection (issue#8)"
```

---

## Task 11: company ページ実装

**Files:**
- Create: `astro/src/pages/company.astro`

- [ ] **Step 1: 実装**

会社情報・沿革・取引先は既存 `company.html` を参照（読み取り可能なファクトをそのまま転記、不明項目は行ごと省略）。

```astro
---
import Base from '../layouts/Base.astro';
import SectionTitle from '../components/SectionTitle.astro';
import CtaBand from '../components/CtaBand.astro';
---
<Base
  title="会社概要 | サンユウテック株式会社"
  description="サンユウテック株式会社の会社概要。福岡県大野城市を拠点に、舗装・ウォータージェット・コンクリート補修の3工法で九州の道路インフラを支えています。"
  currentPage="company"
  ogImage="/images/about-image.jpg"
>
  <section class="page-hero">
    <div class="container">
      <h1>現場の人間が経営する、九州の補修会社です</h1>
    </div>
  </section>

  <section class="message">
    <div class="container narrow">
      <SectionTitle text="代表挨拶" />
      <p>サンユウテック株式会社は、福岡県大野城市を拠点に、舗装工事・ウォータージェット工事・コンクリート補修工事の3工法を一社で提供する会社です。九州自動車道・長崎自動車道管内の補修案件をはじめ、NIPPO様・前田道路様をはじめとする大手道路ゼネコン各社の協力会社として、現場で必要とされる技術を磨いてきました。これからも、現場の課題に応える技術力で、九州のインフラを支える役割を担ってまいります。</p>
    </div>
  </section>

  <section class="info">
    <div class="container narrow">
      <SectionTitle text="会社情報" />
      <dl class="info-table">
        <div><dt>社名</dt><dd>サンユウテック株式会社</dd></div>
        <div><dt>所在地</dt><dd>福岡県大野城市</dd></div>
        <div><dt>事業内容</dt><dd>舗装工事、ウォータージェット工事、コンクリート補修工事</dd></div>
        <div><dt>主要取引先</dt><dd>株式会社NIPPO、前田道路株式会社、大手道路ゼネコン各社、地場ゼネコン各社</dd></div>
        <div><dt>有資格者</dt><dd>一級土木施工管理技士 1名、二級土木施工管理技士 6名</dd></div>
        <div><dt>保有機材</dt><dd>CONJET327 2台、NLB 35305DGF（2,400bar 46L/min）2台、SB機械 4台ほか合計24台</dd></div>
        <div><dt>連絡先</dt><dd>TEL 092-555-9211（平日 8:00〜17:00）</dd></div>
      </dl>
    </div>
  </section>

  <section class="image-band">
    <div class="container">
      <img src="/images/about-image.jpg" alt="サンユウテックの施工現場" width="1200" height="600" loading="lazy" />
    </div>
  </section>

  <section class="partners">
    <div class="container narrow">
      <SectionTitle text="主要取引先" />
      <ul class="partners-list">
        <li>株式会社NIPPO</li>
        <li>前田道路株式会社</li>
        <li>大手道路ゼネコン各社</li>
        <li>地場ゼネコン各社</li>
        <li>公的発注機関</li>
      </ul>
    </div>
  </section>

  <CtaBand heading="協力会社のご検討・ご相談はお気軽にどうぞ。" />
</Base>

<style>
  .page-hero { background: var(--color-navy-900); color: var(--color-white); padding: var(--section-py-tablet) 0; }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .container.narrow { max-width: var(--content-max); }
  .page-hero h1 { color: var(--color-white); font-size: var(--fs-3xl); font-weight: var(--fw-extrabold); }

  .message, .info, .partners { padding: var(--section-py-desktop) 0; }
  .message { background: var(--color-white); }
  .info { background: var(--color-gray-100); }
  .partners { background: var(--color-white); }
  .message p { font-size: var(--fs-base); line-height: var(--lh-relaxed); color: var(--color-gray-900); }

  .info-table { display: flex; flex-direction: column; gap: 0; }
  .info-table > div {
    display: grid;
    grid-template-columns: 160px 1fr;
    gap: var(--sp-4);
    padding: var(--sp-4) 0;
    border-bottom: 1px solid var(--color-gray-200);
  }
  .info-table dt { color: var(--color-gray-500); font-weight: var(--fw-semibold); }
  .info-table dd { margin: 0; color: var(--color-gray-900); }

  .image-band { padding: var(--sp-8) 0; background: var(--color-white); }
  .image-band img { width: 100%; height: auto; border-radius: var(--radius-md); aspect-ratio: 2/1; object-fit: cover; }

  .partners-list { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--sp-4); }
  .partners-list li {
    background: var(--color-gray-100);
    padding: var(--sp-4);
    border-radius: var(--radius-md);
    text-align: center;
    font-weight: var(--fw-semibold);
    color: var(--color-navy-900);
  }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .info-table > div { grid-template-columns: 120px 1fr; }
    .partners-list { grid-template-columns: repeat(2, 1fr); }
    .message, .info, .partners { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 480px) {
    .container { padding: 0 var(--gutter-mobile); }
    .info-table > div { grid-template-columns: 1fr; gap: var(--sp-1); }
    .partners-list { grid-template-columns: 1fr; }
    .message, .info, .partners { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 2: ビルド + コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
cd ..
git add astro/src/pages/company.astro
git commit -m "feat(astro): company ページを実装 (issue#8)"
```

---

## Task 12: contact ページ実装

**Files:**
- Create: `astro/src/pages/contact.astro`

- [ ] **Step 1: 実装**

```astro
---
import Base from '../layouts/Base.astro';
import SectionTitle from '../components/SectionTitle.astro';
import Form from '../components/Form.astro';
---
<Base
  title="お問い合わせ | サンユウテック株式会社"
  description="見積依頼・現地調査のご相談はこちらから。原則1営業日以内に返信します。電話 092-555-9211（平日 8:00〜17:00）も承ります。"
  currentPage="contact"
  ogImage="/images/hero-1.jpg"
>
  <section class="page-hero">
    <div class="container">
      <h1>見積依頼・現地調査のご相談</h1>
      <p class="lead">原則1営業日以内に返信します。お電話でも承ります。</p>
    </div>
  </section>

  <section class="contact-block">
    <div class="container narrow">
      <div class="grid-2">
        <div class="tel-block">
          <SectionTitle text="お電話でのお問い合わせ" level={2} />
          <a href="tel:0925559211" class="tel-large">092-555-9211</a>
          <p class="hours">平日 8:00〜17:00</p>
          <h3>本社所在地</h3>
          <p>福岡県大野城市</p>
        </div>
        <div class="form-block">
          <SectionTitle text="フォームでのお問い合わせ" level={2} />
          <Form />
        </div>
      </div>
    </div>
  </section>
</Base>

<style>
  .page-hero { background: var(--color-navy-900); color: var(--color-white); padding: var(--section-py-tablet) 0; }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .container.narrow { max-width: var(--content-max); }
  .page-hero h1 { color: var(--color-white); font-size: var(--fs-4xl); font-weight: var(--fw-extrabold); margin-bottom: var(--sp-4); }
  .page-hero .lead { font-size: var(--fs-lg); color: var(--color-gray-200); }

  .contact-block { padding: var(--section-py-desktop) 0; background: var(--color-white); }
  .grid-2 { display: grid; grid-template-columns: 1fr 1.5fr; gap: var(--sp-12); align-items: start; }

  .tel-large {
    display: block;
    font-size: var(--fs-4xl);
    font-weight: var(--fw-extrabold);
    color: var(--color-navy-900);
    margin-bottom: var(--sp-2);
  }
  .hours { color: var(--color-gray-700); margin-bottom: var(--sp-8); }
  .tel-block h3 { font-size: var(--fs-lg); margin-bottom: var(--sp-2); color: var(--color-navy-900); }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .grid-2 { grid-template-columns: 1fr; gap: var(--sp-8); }
    .contact-block, .page-hero { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 480px) {
    .container { padding: 0 var(--gutter-mobile); }
    .page-hero h1 { font-size: var(--fs-3xl); }
    .tel-large { font-size: var(--fs-3xl); }
    .contact-block { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 2: ビルド + コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
cd ..
git add astro/src/pages/contact.astro
git commit -m "feat(astro): contact ページを実装 (issue#8)"
```

---

## Task 13: SEO / robots / sitemap 仕上げ + README / CLAUDE.md 更新

**Files:**
- Create: `astro/public/robots.txt`
- Modify: `README.md`
- Create: `CLAUDE.md`（リポジトリルート、AI実装コンテキスト）

- [ ] **Step 1: robots.txt 作成**

`astro/public/robots.txt`:
```
User-agent: *
Allow: /

Sitemap: https://sunyutech.jp/sitemap-index.xml
```

- [ ] **Step 2: README.md を Astro 構成に更新**

既存 README.md の「技術スタック」と「ディレクトリ構成」セクションを Astro 移行後の構成に書き換え。ローカル開発手順を `npm install && npm run dev` に。「現行の静的HTMLは本Issueでは温存している」旨も明記。

- [ ] **Step 3: CLAUDE.md をリポジトリルートに作成**

```markdown
# CLAUDE.md

サンユウテックHPリニューアル後の Claude Code 向けクイックリファレンス。

## 構成

- `astro/` 配下が新サイト（Astro 4.x + バニラCSS + design tokens）
- 既存 `index.html` 等は現行 sunyutech.jp として温存中（本Issue内では置換しない）
- リニューアル成果物: `projects/sunyutech-renewal/`

## 技術スタック

- Astro 4.x（minimal template、TypeScript strict）
- バニラCSS、design tokens は `astro/src/styles/tokens.css` に CSS 変数で定義
- 統合: @astrojs/sitemap
- JS最小限、works フィルタのみ inline `<script>`

## ローカル開発

```bash
cd astro
npm install
npm run dev    # http://localhost:4321
npm run build  # dist/ に静的ファイル
npm run preview
```

## コーディングルール

- 生カラー値（`#0B2545` 等）直書き禁止、`tokens.css` 経由 (`var(--color-navy-900)`)
- インラインスタイル禁止、Astro コンポーネントのスコープド `<style>` を使う
- すべての `<img>` に alt、hero 以外は loading="lazy"
- セマンティックHTML（header/nav/main/section/article/footer）

## 関連ドキュメント

- 設計: `docs/superpowers/specs/2026-05-28-sunyutech-website-renewal-design.md`
- 実装計画: `docs/superpowers/plans/2026-05-29-sunyutech-renewal-phase-4-5-claude-only.md`
- design tokens / layout contract / component spec: `projects/sunyutech-renewal/design/handoff/`

**Last Updated**: 2026-05-29
```

- [ ] **Step 4: コミット**

```bash
git add astro/public/robots.txt README.md CLAUDE.md
git commit -m "docs: README/CLAUDE.md を Astro 構成で更新 + robots.txt (issue#8)"
```

---

## Task 14: Phase 5 QA 実行 + PR 作成

**Files:**
- Create: `projects/sunyutech-renewal/qa/checklist.md`

- [ ] **Step 1: QA 実行**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
```

Expected: ビルド成功、`dist/` に5ページの HTML + sitemap が生成。

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website

# Must Have 検査
echo "=== raw color leak ==="
grep -rnE '#[0-9a-fA-F]{6}' astro/src --include='*.astro' --include='*.css' | grep -v 'tokens.css' && echo "FAIL" || echo "PASS"

echo "=== img without alt ==="
grep -rnE '<img[^>]*>' astro/src --include='*.astro' | grep -vE 'alt=' && echo "FAIL" || echo "PASS"

echo "=== title and meta description per page ==="
for f in astro/dist/index.html astro/dist/service/index.html astro/dist/works/index.html astro/dist/company/index.html astro/dist/contact/index.html; do
  has_title=$(grep -c '<title>' "$f")
  has_desc=$(grep -c 'name="description"' "$f")
  echo "$f: title=$has_title desc=$has_desc"
done

echo "=== OGP per page ==="
for f in astro/dist/index.html astro/dist/service/index.html astro/dist/works/index.html astro/dist/company/index.html astro/dist/contact/index.html; do
  has_og=$(grep -c 'property="og:' "$f")
  echo "$f: og count=$has_og"
done

echo "=== sitemap exists ==="
ls -la astro/dist/sitemap*.xml
```

- [ ] **Step 2: ローカルプレビューで目視確認**

```bash
cd astro
npm run preview &
PREVIEW_PID=$!
sleep 3
echo "Open http://localhost:4321 in browser, check 375/768/1440 widths"
# ユーザーが各ページを確認
# 確認終わったら kill
```

確認項目（このタスクのチェックリスト）:
- [ ] 5ページすべて表示、表示崩れなし
- [ ] 375 / 768 / 1440px で水平スクロールなし
- [ ] CTA がクリックで /contact/ に遷移
- [ ] works のフィルタが動く
- [ ] モバイルでハンバーガーが開閉する
- [ ] フッターの電話番号がタップで発信できる
- [ ] 主軸ペルソナ視点で「3秒で何の会社か」が伝わる

- [ ] **Step 3: `qa/checklist.md` に結果を記録**

```markdown
# Phase 5 QA Checklist 実行結果

実行日: 2026-MM-DD

## Must Have
- [x] `astro build` 成功
- [x] design tokens 経由でない生カラーゼロ
- [x] 5ページ表示、375/768/1440 で水平スクロールなし
- [x] 全 img に alt、hero以外 loading="lazy"
- [x] 全ページ <title> と meta description あり
- [x] 全ページ OGP 5項目以上
- [x] Tab で全リンク到達可能

## Should Have
- [x] 主要取引先・24台・3工法 を index / company に表示
- [ ] Lighthouse: (実測値を記入)
- [ ] LCP < 2.5s

## Nice
- [ ] 採用ページ（本Issue外）
- [ ] WebP 変換（本Issue外）
- [ ] フォーム外部サービス連携（本Issue外）

## ユーザー検収
- [ ] ユーザーがローカルプレビューで「これなら出していい」と判断
```

- [ ] **Step 4: コミット**

```bash
git add projects/sunyutech-renewal/qa/checklist.md
git commit -m "docs: Phase 5 QA結果を追加 (issue#8)"
```

- [ ] **Step 5: PR 作成**

```bash
git push -u origin feature/8-website-renewal-astro

gh pr create --title "コーポレートサイトリニューアル (Astro+バニラCSS、Phase 0-5完走)" --body "$(cat <<'EOF'
## 概要
Issue #8 のコーポレートサイト全5ページリニューアル。AI-LandBase 5フェーズワークフローを Claude Code パスで完走。

## 変更内容
- Phase 0-1: BRIEF / 競合分析 / ペルソナ / メッセージング（projects/sunyutech-renewal/）
- Phase 2-3: Claude Code 直書きの design/handoff/*（原則 / tokens / layout / component / 構造 / 受入基準 / 実装注記）
- Phase 4: astro/ 配下に5ページ実装（Astro 4.x + バニラCSS + design tokens）
- Phase 5: QA チェックリスト（Must Have 全通し）

既存 index.html 等は本PRでは温存。本番置換は別Issueで対応予定。

## テスト方法
```bash
cd astro
npm install
npm run dev
# http://localhost:4321 で5ページを確認、375/768/1440 でレスポンシブ確認
```

## チェックリスト
- [x] レスポンシブ確認（375 / 768 / 1440）
- [x] 主要ブラウザ確認
- [x] ドキュメント更新（README / CLAUDE.md）
- [x] QA Must Have 通し

Closes #8
EOF
)"
```

Expected: PR URL が返る。

---

## Self-Review チェックリスト

- [ ] design/handoff/* 7ファイルが Tasks 1-3 で生成される
- [ ] Astro 実装 11コミット計画が Tasks 4-13 でカバー
- [ ] Phase 5 QA + PR が Task 14
- [ ] 各 Step に具体コマンド・コード・期待結果がある
- [ ] Issue 番号は 8 で全コミットメッセージに統一
- [ ] AI署名禁止が暗黙ルールとして全タスクで守られる前提
- [ ] 既存 index.html 等を絶対に触らないことが各タスクで明確

---

**Last Updated**: 2026-05-29
