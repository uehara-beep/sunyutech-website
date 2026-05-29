# サンユウテック株式会社コーポレートサイト リニューアル 設計書

| 項目 | 内容 |
|---|---|
| 起票日 | 2026-05-28 |
| 対象リポジトリ | `~/workspace/sunyutech-website`（uehara-beep/sunyutech-website） |
| 採用ワークフロー | AI-LandBase/web `workflow/` 5フェーズ（Phase 0–5） |
| 進め方 | 案A: 原典フル試運転（Phase 0/1/4 は Claude Code、Phase 2 は ChatGPT image2、Phase 3 は Claude Design） |
| 管理単位 | 1 Issue / 1 ブランチ / Squash Merge で main に1コミット |
| マージ前提 | 「ものがよくなければマージしない」。本番置換は別Issueに切り出し、現行サイトは最後まで温存 |

---

## 1. 目的とスコープ

### 1.1 目的
- AI-LandBase の 5フェーズワークフローを本案件で初めて素通しで運用し、再現性のある制作プロセスとして検証する。
- 既存コーポレートサイト（HTML/CSS/JS 静的、5ページ、orange #E67E22 主体）を B2B 受注軸でリニューアルする。
- スタックを Astro + バニラCSS + design tokens に移行し、保守性と拡張性を確保する。

### 1.2 スコープ
- TOP / 事業内容 / 施工実績 / 会社概要 / お問い合わせ の **全5ページ刷新**。
- 本Issueの完了基準は「`astro/dist/` のローカルプレビュー（`astro preview` または `npx serve astro/dist`）でユーザーが5ページを確認し、Must Have を満たすと判断できる状態」まで。
- **本番 sunyutech.jp（お名前.com共用サーバー）への置換は別Issue**。本Issue内では実施しない。

### 1.3 スコープ外
- ホスティング移行（お名前.com 継続前提）。
- 動的バックエンド（SSR / メール送信サーバー）。問い合わせフォームの外部サービス選定は本Issue外で決定。
- 採用ページ（recruit.astro）新設は Nice to Have。本Issueでは実装しない。

---

## 2. ターゲットと訴求

### 2.1 主軸ペルソナ
- **公共・民間工事の発注担当**（ゼネコン施工管理 / 元請技術部）
  - 30〜50代、現場施工管理経験者
  - 「短工期・夜間・補修・特殊工法を任せられる協力会社」を技術と実績で評価する
  - 視覚的派手さより、技術根拠・対応範囲・実績の具体性を重視

### 2.2 副ペルソナ
- 採用候補者（20〜40代、舗装/補修系の経験者・未経験者）
- 既存取引先・地域行政（信頼性確認・会社情報参照）

### 2.3 訴求ゴール
- 主CTA: 「お問い合わせ」「見積依頼」
- 副CTA: 採用関連の問い合わせ
- 補助KPI: 主要取引先 / 施工実績 / 対応工法ページの平均滞在・直帰率

---

## 3. ブランドガイドライン

| 項目 | 指定 |
|---|---|
| 主色 | ネイビー `#0B2545`（信頼・専門性、固定） |
| 差し色 | オレンジ `#E67E22`（CTA・アクセントのみ。大面積禁止） |
| 背景 | 白基調 + 淡グレー `#F4F6F9`（セクション区切り） |
| 本文文字色 | `#1F2937` |
| 見出し色 | `#0B2545` |
| フォント | Noto Sans JP（既存継続）。見出し 700–800、本文 400 |

### 禁止事項
- ヒーロー背景全面オレンジなど、オレンジの大面積使用
- 写真の過度な加工（建設業の実直さを損なう）
- ストック素材の「キラキラなIT風」「笑顔の作業員」イラスト

---

## 4. 競合参照

| URL | 観点 |
|---|---|
| https://sb-futami-aj.co.jp/ | 同業近接、舗装系の構成参考 |
| https://www.kictec.co.jp/ | 全国展開・技術紹介の階層 |
| https://www.daiichi-cutter.co.jp/ | コンクリート補修・WJ系の見せ方 |

Phase 1 で `02-competitor-lp-report.md` に WebFetch 解析結果を集約する。

---

## 5. 技術スタックと公開先

| 区分 | 内容 |
|---|---|
| フレームワーク | Astro 4.x + TypeScript（strict） |
| スタイル | バニラCSS + design tokens（CSS変数）。Tailwind不使用 |
| ビルド出力 | 静的HTML/CSS/JS のみ |
| 想定ホスティング | お名前.com 共用レンタルサーバー（FTPアップロード型） |
| 既存資産流用 | `images/` 配下の写真資産はすべて `astro/public/images/` にコピーして継続使用 |

### 設計上の制約
- 動的サーバー機能（Node SSR、メール送信）は使わない
- すべて相対パスで完結させ、サブディレクトリ配置にも耐える構造とする
- JS は最小限。スライダー等が必要な場合のみ Astro Island で部分的に有効化

---

## 6. 法務・コンプライアンス

| 項目 | 要否 |
|---|---|
| プライバシーポリシー | **必要**（問い合わせフォームで個人情報取得） |
| 特定商取引法表記 | 不要（BtoB工事業） |
| 利用規約 | 不要（オンライン取引なし） |
| 採用情報の表現 | 採用ページ新設時に男女雇用機会均等法・職業安定法の表現確認（本Issue対象外） |

---

## 7. ディレクトリ構成

```
sunyutech-website/
├── index.html, company.html, service.html, works.html, contact.html  # 現行（本Issue完了まで変更なし）
├── css/style.css, js/main.js, images/*                                # 現行
│
├── docs/superpowers/specs/
│   └── 2026-05-28-sunyutech-website-renewal-design.md                 # 本spec
│
├── projects/sunyutech-renewal/                                         # ワークフロー成果物
│   ├── BRIEF.md                                                        # Phase 0
│   ├── research/
│   │   ├── 01-product-analysis.md
│   │   ├── 02-competitor-lp-report.md
│   │   ├── 03-persona.md
│   │   └── 04-messaging-brief.md
│   ├── prompts/
│   │   ├── image2-prompt.md                                            # Phase 2 投入用
│   │   └── claude-design-handoff.md                                    # Phase 3 投入用
│   ├── mockups/                                                        # Phase 2 成果物（ユーザー保存）
│   ├── design/handoff/                                                 # Phase 3 成果物（ユーザー保存）
│   └── qa/checklist.md                                                 # Phase 5
│
└── astro/                                                              # Phase 4 実装本体
    ├── astro.config.mjs, package.json, tsconfig.json
    ├── public/images/, public/favicon.ico
    ├── src/
    │   ├── pages/(index|company|service|works|contact).astro
    │   ├── layouts/Base.astro
    │   ├── components/(Header|Footer|Hero|SectionTitle|Button|FeatureCard|WorkCard|Form).astro
    │   └── styles/(tokens.css|global.css)
    └── CLAUDE.md
```

---

## 8. ワークフロー実行計画

### Phase 0: BRIEF.md（Claude Code · 本セッションで作成）
本spec の §1〜§6 を `projects/sunyutech-renewal/BRIEF.md` に書き出す。

### Phase 1: 調査・ペルソナ設計（Claude Code · 本セッションで作成）

| 成果物 | 主内容 |
|---|---|
| `research/01-product-analysis.md` | 提供価値仮説3つ / ペインポイント / 強み言語化 |
| `research/02-competitor-lp-report.md` | 競合3社 WebFetch 解析（観点: hero / CTA / カラー・タイポ / 写真比率 / ターゲットシグナル / 差別化余白） |
| `research/03-persona.md` | 主軸1名 + 副2名のペルソナ詳細 |
| `research/04-messaging-brief.md` | セクション別コピー方針 + キーフレーズ案 + 禁止語 |

### Phase 2: モックアップ生成（ChatGPT image2 · ユーザー実行）

私が `prompts/image2-prompt.md` を10項目構造で完成形納品する。
- Deliverable / Audience / Message / Visual concept / Composition / **Layout contract**（後述）/ Exact in-image text / Constraints / Quality / Final prompt
- **既存素材活用の明示**: 「これは構図と階層を確認する視覚ブリーフ。最終写真は既存クライアント写真（`images/hero-*.jpg` 等）を使う」を Constraints に記載

ユーザー作業: ChatGPT image2 に投入 → 採用版PNGを `projects/sunyutech-renewal/mockups/` に保存 → 本セッションへ復帰。

#### Layout contract（固定）
- wrapper max-width: 1200px
- content max-width: 880px
- gutter: desktop 32px / tablet 24px / mobile 16px
- full-bleed: hero と CTA バンドのみ
- responsive cols: desktop 3 / tablet 2 / mobile 1
- 横スクロール禁止

### Phase 3: 設計システムへの正規化（Claude Design · ユーザー実行）

私が `prompts/claude-design-handoff.md` を完成形納品する。
- 入力: 採用版mockup PNG + `research/04-messaging-brief.md`
- 「画像を literal にコピーするな、再利用可能な設計規則に変換しろ」プロンプト
- 出力依頼: design principles / design tokens (YAML) / layout contract (YAML) / component rules (Header/Hero/FeatureCard/WorkCard/SectionTitle/Button/Form) / page structure / acceptance criteria / implementation notes
- **既存素材活用の明示**: implementation notes に「画像 src は `/images/<既存ファイル名>` を必ず参照する」と記載
- Export: **Claude Code handoff bundle**

ユーザー作業: Claude Design に投入 → handoff bundle を `projects/sunyutech-renewal/design/handoff/` に保存 → 本セッションへ復帰。

### Phase 4: Astro 実装（Claude Code · 本セッションで実施）

スキャフォールド: `cd astro && npm create astro@latest .`（minimal、TypeScript strict、依存追加なし）。
既存 `images/` を `astro/public/images/` にコピー（git mv ではなくコピーで両立）。

#### コミット分割（PR内コミット履歴用、Squashで1コミット化）
1. `feat(astro): Astroプロジェクトをスキャフォールド (issue#N)`
2. `feat(astro): design tokens と global.css を実装 (issue#N)`
3. `feat(astro): Base レイアウトと Header / Footer (issue#N)`
4. `feat(astro): 共通コンポーネント Hero / SectionTitle / Button / FeatureCard / WorkCard / Form (issue#N)`
5. `feat(astro): index ページ実装 (issue#N)`
6. `feat(astro): service ページ実装 (issue#N)`
7. `feat(astro): works ページ実装 (issue#N)`
8. `feat(astro): company ページ実装 (issue#N)`
9. `feat(astro): contact ページ実装 (issue#N)`
10. `feat(astro): SEO / OGP / sitemap / robots (issue#N)`
11. `docs: README と CLAUDE.md を Astro 構成で更新 (issue#N)`

#### 実装ルール（既存 CONTRIBUTING.md と整合）
- インラインスタイル禁止 → Astro `<style>` スコープド or `styles/*.css`
- CSS変数（design tokens）必須、生の色コード直書き禁止
- すべての `<img>` に `alt` + （hero以外は）`loading="lazy"`
- セマンティックHTML（`header / nav / main / section / article / footer`）
- JS最小限、スライダー等は Astro Island で1コンポーネントのみ

### Phase 5: QA（Claude Code · 本セッションで実施）

`projects/sunyutech-renewal/qa/checklist.md` に保存。

#### Must Have（マージ前提条件）
- [ ] 5ページすべて Astro で実装され `astro build` が成功する
- [ ] design tokens 経由でない色・サイズ・余白を持たない（grep で `#[0-9a-fA-F]{3,6}` を `tokens.css` 以外で検出ゼロ）
- [ ] 375 / 768 / 1440px で横スクロールなし、各ブレークポイントで主要セクション表示確認
- [ ] すべての画像に `alt`、リンクは Tab で辿れる
- [ ] OGP / Twitter Card 設定、`<title>` `<meta description>` を各ページで最適化
- [ ] 主軸ペルソナの「ゼネコン施工管理職」が見て、3秒で「何をやる会社か」「どこに問い合わせるか」が分かる（messaging-brief整合）

#### Should Have（推奨）
- [ ] 主要取引先ロゴ・実績件数を index と company に表示
- [ ] Lighthouse Performance ≥ 90、Accessibility ≥ 95、Best Practices ≥ 95、SEO ≥ 95（モバイル）
- [ ] Core Web Vitals: LCP < 2.5s
- [ ] sitemap.xml / robots.txt 自動生成（Astro integration）

#### Nice to Have
- [ ] 採用ページ（recruit.astro）の新設
- [ ] 既存 hero-*.jpg を WebP 変換し容量削減
- [ ] お問い合わせフォーム外部サービス連携

---

## 9. Git / Issue 運用

`sanyu_dx_platform/CONTRIBUTING.md` と既存 `sunyutech-website/CONTRIBUTING.md` を準拠。

| 項目 | 内容 |
|---|---|
| Issue | 1本でリニューアル全体を管理。本spec を要件本文に貼る |
| ブランチ | `feature/<Issue番号>-website-renewal-astro`（main から派生） |
| コミット | Conventional Commits + `(issue#N)`。AI署名トレーラー禁止 |
| マージ戦略 | Squash and Merge（PR内コミットは履歴のみ、main は1コミット） |
| マージ判断 | Must Have が全通し、ユーザーがローカルプレビューで「出していい」と判断したら merge。それ以前は merge しない |
| 別Issue | 本番 sunyutech.jp 置換は別Issueで扱う |
| 16h上限の扱い | CONTRIBUTING.md の「1Issue ≤ 16h」原則を本案件では意図的に超過。理由: ワークフロー試運転として 5フェーズの一貫性検証が目的であり、フェーズ単位でIssue分割すると Phase 2/3（外部ツール）の往復で文脈分断が発生するため。ユーザー合意済み（2026-05-28） |

---

## 10. リスクと対応

| リスク | 対応 |
|---|---|
| ChatGPT image2 / Claude Design の利用枠消費 | Phase 2 で探索を完結させてから Phase 3 に入る。Phase 3 は handoff 1往復に限定 |
| 既存写真の解像度・トリミング不整合 | Phase 4 で WebP 変換を Should に置く。本Issueでは元画像そのまま使用 |
| お名前.com 共用サーバーの相対パス問題 | Astro `base` 設定をデフォルト `/` のまま、相対パス基準で実装 |
| お問い合わせフォームの送信手段未確定 | 本Issueでは `mailto:` フォールバックで実装、外部サービス連携は別Issue |
| Layout contract の解釈ブレ | Phase 2 image2 プロンプトに数値を明示固定、Phase 3 handoff にも同値を再記 |

---

## 11. Definition of Done（本Issue）

- [ ] `projects/sunyutech-renewal/BRIEF.md` 作成済み
- [ ] `research/01-04*.md` 4本作成済み
- [ ] `prompts/image2-prompt.md` 完成形で作成済み
- [ ] Phase 2 mockup（ユーザー作業）が `mockups/` に揃っている
- [ ] `prompts/claude-design-handoff.md` 完成形で作成済み
- [ ] Phase 3 handoff bundle（ユーザー作業）が `design/handoff/` に揃っている
- [ ] `astro/` に5ページ実装、`astro build` 成功
- [ ] `qa/checklist.md` の Must Have が全通し
- [ ] PR レビュー承認後、ユーザーが merge 判断

---

---

## 12. Pivot — 2026-05-29 Claude-only 経路へ切替

ヒーロー mockup を ChatGPT image2 で 1案生成し評価したが、現場でのトーンと細部統制（特に nav セパレータ・写真比率・サブテキスト密度）が要求水準に届かなかった。Phase 2 の反復で詰めるより、Claude Code 直書きのほうが速度・整合性の両面で勝ると判断。以下に切替える。

### 切替内容

| 項目 | 切替前 | 切替後 |
|---|---|---|
| Phase 2（ChatGPT image2） | mockup PNG 5枚 | **スキップ**。1案のみ参考として `projects/sunyutech-renewal/mockups/hero-final.png` に保存（任意） |
| Phase 3（Claude Design） | handoff bundle 生成 | **スキップ**。Claude Code が `projects/sunyutech-renewal/design/handoff/` 配下に design principles / tokens / layout contract / component spec / page structure / acceptance criteria / implementation notes を直接執筆 |
| Phase 4 / 5 | 同 | 維持（Astro実装 + QA） |

### 旧成果物の扱い

- `projects/sunyutech-renewal/prompts/image2-prompt.md` は試行ログとして残置（次プロジェクトのワークフロー学習用）
- `prompts/claude-design-handoff.md` は作成しない
- `mockups/hero-final.png` は手元保存していれば視覚参照、無ければ問題なし

### 新プラン

`docs/superpowers/plans/2026-05-29-sunyutech-renewal-phase-4-5-claude-only.md` で
- design/handoff/* の Claude 直書き
- Astro 4.x 実装（spec §8 Phase 4 の11コミット構成を維持）
- Phase 5 QA Must Have 検証 + マージ判断

を扱う。

---

**Last Updated**: 2026-05-29
