# image2-prompt.md（ChatGPT image2 投入用）

> このファイルは ChatGPT image2 に「順番に」投入してモックアップを生成するためのもの。
> 各セクション末尾に「Final prompt」を英語で用意してある。これをそのままコピペして使う。
> 文言・数値・写真名は `research/04-messaging-brief.md` の採用案に揃えてある（暫定文言は使わない）。

---

## 共通設定（毎回先頭に置く）

### 1. Deliverable

LP mockup（visual brief、not final art）。サンユウテック株式会社（日本の建設会社・舗装/ウォータージェット/コンクリート補修）のコーポレートサイト・トップページの各セクションを順番に生成する。本番写真は生成せず、既存クライアント写真（`images/hero-*.jpg` `images/service-*.jpg` `images/work-*.jpg` `images/about-image.jpg`）を流し込む前提のグレー枠＋ラベルで構図を提示する。

### 2. Audience

- 主: 高橋部長（45歳前後、九州大手道路ゼネコンの現場代理人・施工管理。現場経験豊富、技術と実績で判断、明日朝の発注会議に2〜3社挙げる局面）
- 副1: 田中さん（26歳、舗装工経験3年、九州内転職検討）
- 副2: 山田課長（50代、既存取引先 元請の協力会社管理 or 行政発注担当、経審・地域貢献度評価で会社情報を参照）

（詳細: `research/03-persona.md`）

### 3. Message

- キー（採用案・主軸ペルソナ向け）: 「九州の道路補修を、舗装・WJ・コンクリート補修の3工法で。」
- 副: 「福岡・大野城を拠点に、九州自動車道・長崎自動車道管内の供用下補修まで対応します。」
- 強み: 「3工法を一社で完結」「自社保有24台の機材」「NIPPO・前田道路と継続取引」
- 数字の根拠: 保有機材24台、3工法、九州4県＋高速道路管内、一級土木1名／二級6名、最大約30,000m²

（詳細は `research/04-messaging-brief.md` 参照）

### 4. Visual concept

- 主色 navy: `#0B2545`（信頼・専門性、見出し色・主要面材）
- 差し色 orange: `#E67E22`（CTA・小アクセントのみ、面積は5%以下）
- 背景 white + soft gray: `#F4F6F9`（セクション区切り）
- 文字色 base: `#1F2937`、heading: `#0B2545`
- フォント: Noto Sans JP（heading 800、body 400）
- 写真: クライアント既存写真を「使う前提」の構図。具体的に下記をラベルで指定する。
  - Hero: `images/hero-1.jpg` / `images/hero-2.jpg` / `images/hero-3.jpg` のいずれか
  - 事業: `images/service-paving.jpg` / `images/service-waterjet.jpg` / `images/service-concrete.jpg`
  - 実績: `images/work-paving01.jpg` `images/work-paving02.jpg` `images/work-waterjet01.jpg` `images/work-concrete01.jpg` 等
  - 会社: `images/about-image.jpg`
- 全体トーン: 建設業の実直さ。IT風キラキラ・笑顔の作業員ストック写真は禁止。

### 5. Composition（共通）

- 左寄せ heading、右側に建設写真ゾーン
- 視線誘導: 左上 → 右下
- CTA は主+副 2つ並列。主CTA はオレンジ塗り＋白文字、副CTA は白背景＋ネイビー枠＋ネイビー文字
- 写真は実物のリアル感を残す（過度なフィルタなし）
- 見出し直下に主CTA を単独で置き、フッター手前に再度同じ主CTA を置く2段構造

### 6. Layout contract（必ず守る数値）

- page wrapper max-width: **1200px**
- content max-width: **880px**
- gutter: desktop **32px** / tablet **24px** / mobile **16px**
- full-bleed: hero と CTA バンドのみ（背景色は全幅、内側テキストは 880px に収める）
- responsive columns: desktop **3** / tablet **2** / mobile **1**
- horizontal scroll: **forbidden at any viewport**

### 7. Exact in-image text

各セクション欄で個別に指定する（このセクションの直下「セクション別 prompt」を参照）。すべて日本語、敬体（〜します／〜可能です／〜いたします）。「！」「？」は使わない。

### 8. Constraints（毎回明示）

- DO NOT generate final photography. これは visual brief であり、本番写真は既存クライアント写真（`images/hero-*.jpg` `images/service-*.jpg` `images/work-*.jpg` `images/about-image.jpg`）を本実装で差し込む。生成時はグレー枠＋ファイル名ラベルで示す。
- DO NOT use orange (`#E67E22`) as background fill — only for primary CTA button and small accents. 面積は visible surface の 5% 以下。
- Navy `#0B2545` を主役（信頼・専門性の象徴）として heading と主要面材に使用すること。
- DO NOT use stock-photo smiling workers, IT-style sparkle illustrations, or cartoonish vectors.
- DO NOT include English-language headings or CTAs — all heading and button text in Japanese.
- DO NOT add company logos other than「サンユウテック」。
- DO NOT use the forbidden Japanese phrases: 「安心・安全」「お客様第一」「最高品質」「絶対」「DX」「イノベーション」（research/04 禁止語）。
- DO use concrete numbers and proper nouns: 「24台」「3工法」「九州4県」「最大約30,000m²」「CONJET327」「NLB 35305DGF」「NIPPO」「前田道路」「九州自動車道管内」「長崎自動車道管内」.

### 9. Quality

- 探索: low（方向性の散らし）
- 仕上げ候補: medium（採用案候補の絞り込み）
- 採用版: high（モックアップ確定）

### 10. Final prompt

セクション別に下記の各「Final prompt」を使う。

---

## セクション別 prompt

### [Hero セクション] — まずここから

**7. Exact in-image text（正確に引用）**

- Main headline: 「九州の道路補修を、舗装・WJ・コンクリート補修の3工法で。」
- Sub: 「福岡・大野城を拠点に、九州自動車道・長崎自動車道管内の供用下補修まで対応します。」
- Primary CTA: 「見積依頼・現地調査のご相談」
- Secondary CTA: 「施工実績を見る」
- Header phone: 「TEL 092-555-9211（平日 8:00〜17:00）」
- Header nav: 「事業内容 / 施工実績 / 会社概要 / 採用情報 / お問い合わせ」

**10. Final prompt（英語、コピペ用）**

```
Generate a hero-section mockup for a Japanese construction company's corporate website.

Company: SANYUTECH (サンユウテック株式会社) — a Kyushu-based pavement, water-jet, and concrete-repair contractor. Headquartered in Onojo City, Fukuoka. Works on Kyushu Expressway and Nagasaki Expressway in-service repair.

Audience: 45-year-old site managers at major Kyushu road-construction general contractors, evaluating subcontractors the night before a procurement meeting. Tone: dependable, technical, calm — not flashy.

Color system (strict):
- Primary navy #0B2545 (heading text, primary surfaces, nav bar)
- Accent orange #E67E22 (primary CTA button only, max ~5% of visible surface)
- Background white with soft gray #F4F6F9 to separate sections
- Body text #1F2937

Layout contract (strict, must obey):
- Outer page wrapper centered at max-width 1200px
- Inner content max-width 880px
- Desktop viewport 1440px, desktop gutter 32px
- Responsive columns: 3 desktop / 2 tablet / 1 mobile (here hero is single column block)
- No horizontal scroll at any viewport
- Hero is full-bleed background, inner text constrained to 880px

Composition:
- Thin top navigation bar: left shows wordmark 「サンユウテック」 in navy, right shows links 「事業内容 / 施工実績 / 会社概要 / 採用情報 / お問い合わせ」 with a small phone block on the far right: 「TEL 092-555-9211 平日 8:00〜17:00」
- Below the nav, two-pane hero. Left pane ~55% width:
  - Heading block left-aligned, navy color, Noto Sans JP weight 800, ~48-56px, two lines:
    Line 1: 「九州の道路補修を、」
    Line 2: 「舗装・WJ・コンクリート補修の3工法で。」
  - Sub line below, dark gray #1F2937, Noto Sans JP weight 400, ~18px:
    「福岡・大野城を拠点に、九州自動車道・長崎自動車道管内の供用下補修まで対応します。」
  - Two CTA buttons in a row beneath the sub:
    - Primary: solid orange #E67E22 background, white text, weight 700, ~16px, padding 16px 32px: 「見積依頼・現地調査のご相談」
    - Secondary: white background, navy #0B2545 1.5px border, navy text, weight 600: 「施工実績を見る」
- Right pane ~45% width: a soft-gray placeholder rectangle (rounded 8px) labeled in small monospace text: "PHOTO: images/hero-1.jpg (or hero-2.jpg / hero-3.jpg) — existing client image". DO NOT generate the photograph.

Typography:
- Heading Noto Sans JP weight 800, navy #0B2545
- Sub Noto Sans JP weight 400, #1F2937
- Buttons size ~16px weight 600-700

Exact in-image text (must appear verbatim, in Japanese):
- Heading line 1: 「九州の道路補修を、」
- Heading line 2: 「舗装・WJ・コンクリート補修の3工法で。」
- Sub: 「福岡・大野城を拠点に、九州自動車道・長崎自動車道管内の供用下補修まで対応します。」
- Primary CTA: 「見積依頼・現地調査のご相談」
- Secondary CTA: 「施工実績を見る」
- Header phone: 「TEL 092-555-9211 平日 8:00〜17:00」
- Header nav: 「事業内容 / 施工実績 / 会社概要 / 採用情報 / お問い合わせ」

Forbidden:
- Stock-photo people, smiling workers, IT-style sparkle / gradient illustrations
- Orange #E67E22 used as background fill or large block (CTA button only, max 5% visible surface)
- English text in headings or CTAs
- Logos other than「サンユウテック」
- Banned phrases: 「安心・安全」「お客様第一」「最高品質」「絶対」「DX」「イノベーション」
- Horizontal scroll at any viewport, content escaping the 880px inner width

Quality: medium (this is a layout brief, not final art).
```

---

### [強み3点セクション]

**7. Exact in-image text**

- Section title: 「サンユウテックが選ばれる理由」
- Card 1 title: 「3工法を一社で完結」
- Card 1 sub: 「舗装・WJ・コンクリート補修を自社施工で受託します」
- Card 2 title: 「自社保有24台の機材」
- Card 2 sub: 「CONJET327 2台、NLB 35305DGF 2台ほか合計24台」
- Card 3 title: 「NIPPO・前田道路と継続取引」
- Card 3 sub: 「大手道路ゼネコン各社との協力会社として実績を重ねています」

**10. Final prompt**

```
Generate a "why choose us" (強み3点) section mockup, immediately following the hero, for the same Japanese construction company website (SANYUTECH).

Reuse the same color system, typography, and layout contract:
- Outer wrapper max-width 1200px, inner content max-width 880px
- Desktop gutter 32px / tablet 24px / mobile 16px
- Responsive columns: 3 desktop / 2 tablet / 1 mobile
- No horizontal scroll at any viewport
- Primary navy #0B2545 dominant, accent orange #E67E22 ≤5% (none in this section is acceptable)

Composition:
- Section title left-aligned, navy #0B2545, Noto Sans JP weight 800, ~32px: 「サンユウテックが選ばれる理由」
- Below it a 3-column grid (desktop). Each column is a card with:
  - A small navy filled square 48x48 at top (placeholder for a simple line icon — DO NOT design final icons, just show a navy square with a tiny number "01" / "02" / "03" in white inside)
  - Card title navy #0B2545 weight 700 ~22px
  - Card subtitle #1F2937 weight 400 ~15px (1-2 lines)
- Cards: background white, 1px border #E5E7EB, padding 24px, border-radius 8px
- Section background soft gray #F4F6F9

Exact in-image text (verbatim, Japanese):
- Section title: 「サンユウテックが選ばれる理由」
- Card 1: title 「3工法を一社で完結」 sub 「舗装・WJ・コンクリート補修を自社施工で受託します」
- Card 2: title 「自社保有24台の機材」 sub 「CONJET327 2台、NLB 35305DGF 2台ほか合計24台」
- Card 3: title 「NIPPO・前田道路と継続取引」 sub 「大手道路ゼネコン各社との協力会社として実績を重ねています」

Strict constraints (same as before):
- No stock photos, smiling workers, sparkle illustrations
- No orange backgrounds or large orange blocks; this section uses navy + white + soft gray only
- All headings and text in Japanese
- No banned phrases: 「安心・安全」「お客様第一」「最高品質」「絶対」「DX」「イノベーション」
- Concrete proper nouns must stay: 「CONJET327」「NLB 35305DGF」「NIPPO」「前田道路」「24台」「3工法」

Quality: medium.
```

---

### [事業ダイジェスト（→ service への誘導）セクション]

**7. Exact in-image text**

- Section title: 「事業内容」
- Lead: 「3工法を、一社で。」
- Card 1 title: 「舗装工事」
- Card 1 sub: 「大型物流倉庫から高速道路本線まで、最大約30,000m²規模に対応します」
- Card 2 title: 「ウォータージェット工事」
- Card 2 sub: 「振動・騒音を抑えたはつり・粗面処理で、供用下補修に対応します」
- Card 3 title: 「コンクリート補修工事」
- Card 3 sub: 「橋梁床版・トンネルの断面修復から表面保護まで一貫して施工します」
- Inline link per card: 「詳しく見る →」
- Section CTA: 「3工法の詳細と対応規模をご覧いただけます」

**10. Final prompt**

```
Generate a "service overview" (事業内容) section mockup, following the why-choose-us section.

Reuse the same color system, typography, and layout contract:
- Outer wrapper max-width 1200px, inner content max-width 880px
- Gutter: desktop 32px / tablet 24px / mobile 16px
- Responsive columns: 3 desktop / 2 tablet / 1 mobile
- No horizontal scroll at any viewport
- Navy #0B2545 dominant, accent orange #E67E22 used only on the section CTA link arrow (≤5% surface)

Composition:
- Section title left-aligned, navy weight 800 ~32px: 「事業内容」
- Lead line under the title, navy weight 600 ~20px: 「3工法を、一社で。」
- Below the lead, a 3-column grid (desktop) / 2-col tablet / 1-col mobile. Each column:
  - At top, a wide rectangular soft-gray placeholder labeled in small monospace text with the exact image filename:
    - Col 1: "PHOTO: images/service-paving.jpg"
    - Col 2: "PHOTO: images/service-waterjet.jpg"
    - Col 3: "PHOTO: images/service-concrete.jpg"
    DO NOT generate the actual photograph — render only the labeled placeholder rectangle (rounded 8px).
  - Service title below the photo, navy weight 700 ~24px
  - One- to two-line description, #1F2937 weight 400 ~15px
  - At the bottom of each card, a small navy text link: 「詳しく見る →」 (the arrow may be orange #E67E22 as the only accent)
- Below the grid, a centered secondary CTA line in #1F2937 weight 400 ~14px: 「3工法の詳細と対応規模をご覧いただけます」, followed by a white-background navy-border button labeled 「事業内容を見る」 (this button text is optional; if rendered, keep it secondary style).

Exact in-image text (verbatim, Japanese):
- Section title: 「事業内容」
- Lead: 「3工法を、一社で。」
- Col 1: photo label "PHOTO: images/service-paving.jpg", title 「舗装工事」, sub 「大型物流倉庫から高速道路本線まで、最大約30,000m²規模に対応します」, link 「詳しく見る →」
- Col 2: photo label "PHOTO: images/service-waterjet.jpg", title 「ウォータージェット工事」, sub 「振動・騒音を抑えたはつり・粗面処理で、供用下補修に対応します」, link 「詳しく見る →」
- Col 3: photo label "PHOTO: images/service-concrete.jpg", title 「コンクリート補修工事」, sub 「橋梁床版・トンネルの断面修復から表面保護まで一貫して施工します」, link 「詳しく見る →」
- Section CTA line: 「3工法の詳細と対応規模をご覧いただけます」

Forbidden (same list):
- Stock photos, smiling workers, sparkle illustrations
- Orange used anywhere other than the small "→" accent (no orange backgrounds, no orange blocks)
- English headings or button text
- Banned phrases: 「安心・安全」「お客様第一」「最高品質」「絶対」「DX」「イノベーション」
- Replacing concrete numbers with vague claims — keep 「最大約30,000m²」「3工法」 verbatim

Quality: medium.
```

---

### [実績ハイライト（→ works への誘導）セクション]

**7. Exact in-image text**

- Section title: 「直近の施工実績」
- Section lead: 「九州一円、3工法の施工実績」
- Card 1: 場所「熊本県内」/ 工法「舗装（コンクリート舗装）」/ 規模「約30,000m²」/ 元請「大手道路ゼネコン」/ ハイライト「最大規模、自社の大型ミキサー8台で連続打設」
- Card 2: 場所「九州自動車道管内」/ 工法「ウォータージェット」/ 規模「床版上面はつり」/ 元請「公的発注機関」/ ハイライト「供用下夜間施工、振動・騒音を抑制」
- Card 3: 場所「福岡県内」/ 工法「舗装」/ 規模「約7,000m²」/ 元請「大手道路ゼネコン」/ ハイライト「大型物流倉庫の重荷重対応舗装」
- Section CTA: 「九州一円の施工実績を工法別にご覧いただけます」
- CTA button: 「施工実績を見る」

**10. Final prompt**

```
Generate a "recent works" (直近の施工実績) section mockup, following the service overview, for SANYUTECH.

Reuse the design system:
- Outer wrapper max-width 1200px, inner content max-width 880px
- Gutter: desktop 32px / tablet 24px / mobile 16px
- Responsive columns: 3 desktop / 2 tablet / 1 mobile
- No horizontal scroll at any viewport
- Navy #0B2545 dominant, soft-gray section background optional; accent orange #E67E22 only on chips or the CTA button (≤5% surface)
- Section background white (to contrast with the soft-gray why-choose-us above)

Composition:
- Section title left-aligned, navy weight 800 ~32px: 「直近の施工実績」
- Section lead under the title, navy weight 600 ~18px: 「九州一円、3工法の施工実績」
- A 3-column grid (desktop), each card:
  - Wide aspect (16:10) soft-gray photo placeholder rounded 8px, small monospace label inside:
    - Card 1: "PHOTO: images/work-paving02.jpg"
    - Card 2: "PHOTO: images/work-waterjet01.jpg"
    - Card 3: "PHOTO: images/work-paving01.jpg"
  - Below the photo, a small navy filled chip (rounded 4px, padding 4px 10px, white text weight 600 ~12px) showing the construction method:
    - Card 1 chip: 「舗装」
    - Card 2 chip: 「ウォータージェット」
    - Card 3 chip: 「舗装」
  - Card title navy weight 700 ~20px, one-line highlight:
    - Card 1: 「最大規模、自社の大型ミキサー8台で連続打設」
    - Card 2: 「供用下夜間施工、振動・騒音を抑制」
    - Card 3: 「大型物流倉庫の重荷重対応舗装」
  - 4-line metadata block in mono-ish weight 400 ~13px, #1F2937:
    - Card 1:
        場所: 熊本県内
        工法: 舗装（コンクリート舗装）
        規模: 約30,000m²
        元請: 大手道路ゼネコン
    - Card 2:
        場所: 九州自動車道管内
        工法: ウォータージェット
        規模: 床版上面はつり
        元請: 公的発注機関
    - Card 3:
        場所: 福岡県内
        工法: 舗装
        規模: 約7,000m²
        元請: 大手道路ゼネコン
- Below the grid, a centered line #1F2937 weight 400 ~14px: 「九州一円の施工実績を工法別にご覧いただけます」, followed by a secondary CTA button (white background, navy border, navy text, weight 600 ~16px): 「施工実績を見る」

Exact in-image text: use the metadata lines and labels above verbatim (Japanese only). Period: keep 「期間」 OFF the cards (per research/04 the period is not yet confirmed and placeholders are forbidden).

Forbidden (same list):
- Stock photos, smiling workers, sparkle illustrations
- Orange backgrounds; orange limited to the chip border or CTA button border accent if needed (≤5% surface). Chips in this design are navy filled, not orange.
- Inventing fake 期間 / 元請社名 / dummy m² values — use only the values listed above
- Banned phrases: 「安心・安全」「お客様第一」「最高品質」「絶対」「DX」「イノベーション」
- English headings

Quality: medium.
```

---

### [CTA バンド + 会社紹介ティーザーセクション]

**7. Exact in-image text**

- Heading: 「見積依頼・現地調査のご相談はこちら」
- Sub: 「原則1営業日以内に、施工管理担当よりご返信いたします」
- Primary CTA: 「見積依頼・現地調査のご相談」
- Secondary CTA: 「採用情報を見る」
- 電話: 「TEL 092-555-9211」
- 営業時間: 「平日 8:00〜17:00」
- 会社紹介ティーザー（band の下、separate strip）: 「保有機材24台、一級土木1名／二級6名。九州一円・高速道路管内に対応します。」
- 会社紹介リンク: 「会社概要を見る →」
- 会社紹介写真ラベル: "PHOTO: images/about-image.jpg"

**10. Final prompt**

```
Generate a closing CTA band mockup, the last full-width section before the footer, for SANYUTECH. Also include a thin company-teaser strip immediately below the CTA band that points to the company-profile page using the existing client photo "images/about-image.jpg".

Layout contract (strict):
- CTA band: full-bleed background navy #0B2545, text color white, width spans 100% of viewport (NOT constrained to 1200px), inner text constrained to 880px content width
- Company-teaser strip beneath: outer wrapper 1200px, inner content 880px, background soft gray #F4F6F9, navy text
- Gutter: desktop 32px / tablet 24px / mobile 16px
- Responsive: desktop 3-col / tablet 2-col / mobile 1-col model (here the CTA band is a 2-area layout: text-left, CTA-right on desktop; stacked on mobile). No horizontal scroll at any viewport.
- Navy #0B2545 is the dominant surface in the CTA band. Orange #E67E22 limited to the primary CTA button only (≤5% visible surface).

CTA band composition:
- Left area (~60% width on desktop, full width on mobile):
  - Heading white weight 800 ~36px: 「見積依頼・現地調査のご相談はこちら」
  - Sub white weight 400 ~16px: 「原則1営業日以内に、施工管理担当よりご返信いたします」
- Right area (~40% width, stacked column):
  - Primary CTA button: solid orange #E67E22 background, white text, weight 700 ~16px, padding 16px 32px, rounded 6px: 「見積依頼・現地調査のご相談」
  - Secondary CTA: transparent background, white 1.5px border, white text, weight 600 ~15px, padding 12px 24px, rounded 6px: 「採用情報を見る」
  - Telephone number white weight 700 ~22px: 「TEL 092-555-9211」
  - Operating hours white weight 400 ~13px: 「平日 8:00〜17:00」
- On mobile (<768px), stack vertically: heading → sub → primary CTA → secondary CTA → tel → hours.

Company-teaser strip composition (immediately below the navy band, separated by 0 margin):
- Background soft gray #F4F6F9
- Two-column desktop layout inside the 880px content:
  - Left ~50%: a rectangular photo placeholder (rounded 8px) labeled in small monospace: "PHOTO: images/about-image.jpg"
  - Right ~50%:
    - Small navy label weight 600 ~13px: 「会社紹介」
    - Heading navy weight 800 ~22px: 「保有機材24台、一級土木1名／二級6名。九州一円・高速道路管内に対応します。」
    - Inline navy link weight 600 ~14px with orange "→": 「会社概要を見る →」
- On mobile, stack: photo placeholder → label → heading → link.

Exact in-image text (verbatim, Japanese):
- Heading: 「見積依頼・現地調査のご相談はこちら」
- Sub: 「原則1営業日以内に、施工管理担当よりご返信いたします」
- Primary CTA: 「見積依頼・現地調査のご相談」
- Secondary CTA: 「採用情報を見る」
- Tel: 「TEL 092-555-9211」
- Hours: 「平日 8:00〜17:00」
- Company-teaser label: 「会社紹介」
- Company-teaser heading: 「保有機材24台、一級土木1名／二級6名。九州一円・高速道路管内に対応します。」
- Company-teaser link: 「会社概要を見る →」
- Photo label inside teaser: "PHOTO: images/about-image.jpg"

Forbidden:
- Filling the entire band with orange (orange limited to primary CTA button only, ≤5% surface)
- Stock-photo smiling workers in the teaser placeholder — only label, no generated photograph
- English text in any heading, button, or link
- Banned phrases: 「安心・安全」「お客様第一」「最高品質」「絶対」「DX」「イノベーション」
- Inventing a fake phone number — use 092-555-9211 verbatim
- Horizontal scroll at any viewport

Quality: high (this is a candidate adoption render).
```

---

## イテレーション戦略

1. Hero を low で2-3案 → 方向が決まったら medium → high で採用版
2. その後、強み → 事業ダイジェスト → 実績ハイライト → CTAバンド+会社ティーザー を同じビジュアル系で順番に生成
3. medium 版でブレた部分があれば「Xだけ変えて他はそのまま」で差分編集（特に navy/orange の面積比、880px の内側収まり、フォトプレースホルダのラベル正確性）
4. 採用版PNGを `projects/sunyutech-renewal/mockups/` にファイル名 `hero-vN.png` `strength-vN.png` `service-vN.png` `works-vN.png` `cta-vN.png` で保存
5. 全採用版がそろってから Phase 3（Claude Design への handoff）へ進む

## 採用後の作業（ユーザーが完了したら本セッションに戻る）

- `projects/sunyutech-renewal/mockups/` に最低5枚（hero / strength / service / works / cta）が揃った状態にする
- 戻ってきたら Task 10（Phase 3 handoff prompt）を実行
- handoff prompt では本ファイルの「Exact in-image text」と Layout contract を Claude Design 側にも引き継ぐ

---

**Last Updated**: 2026-05-28
