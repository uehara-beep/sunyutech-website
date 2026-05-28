# サンユウテック HPリニューアル Phase 0–3 実装計画

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** AI-LandBase 5フェーズワークフローの Phase 0（BRIEF）、Phase 1（research）、Phase 2/3 用プロンプト納品までを完了し、ユーザーが ChatGPT image2 / Claude Design を実行する準備を整える。

**Architecture:** すべての成果物は `projects/sunyutech-renewal/` 配下に配置し、Issue 1本（feature ブランチ）で管理。Phase 2/3 はユーザーが外部ツールで実行するため、本プランは「投入用プロンプトを完成形で納品」までを範囲とする。Phase 4/5 は別プラン（handoff bundle 受領後に作成）。

**Tech Stack:** Markdown / Git (GitHub Flow) / gh CLI / WebFetch（競合LP取得）

**Spec参照:** `docs/superpowers/specs/2026-05-28-sunyutech-website-renewal-design.md`

---

## File Structure（本プランで作成・変更）

| パス | 役割 |
|---|---|
| `projects/sunyutech-renewal/BRIEF.md` | Phase 0 ブリーフ（spec §1–6 をベースに案件固有形式で書く） |
| `projects/sunyutech-renewal/research/01-product-analysis.md` | 自社プロダクト分析 |
| `projects/sunyutech-renewal/research/02-competitor-lp-report.md` | 競合3社（sb-futami-aj / kictec / daiichi-cutter）解析 |
| `projects/sunyutech-renewal/research/03-persona.md` | 主軸1名+副2名のペルソナ |
| `projects/sunyutech-renewal/research/04-messaging-brief.md` | セクション別コピー方針 |
| `projects/sunyutech-renewal/prompts/image2-prompt.md` | Phase 2 投入用（10項目構造） |
| `projects/sunyutech-renewal/prompts/claude-design-handoff.md` | Phase 3 投入用 |
| `projects/sunyutech-renewal/mockups/.gitkeep` | Phase 2 成果物の置き場（ユーザー保存） |
| `projects/sunyutech-renewal/design/handoff/.gitkeep` | Phase 3 成果物の置き場（ユーザー保存） |
| `.gitignore` | 既存に `astro/node_modules/`、`astro/dist/` を追記（次プランのための先行整備） |

---

## Task 1: ブランチ・Issue セットアップ

**Files:**
- Create: GitHub Issue（リモート、本spec を本文として貼付）
- Modify: ローカルブランチ（`main` 取込 → `feature/<N>-website-renewal-astro` 派生）

- [ ] **Step 1: main を最新化**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
git checkout main
git pull origin main
```

Expected: `Already up to date.` または fast-forward 取込完了。

- [ ] **Step 2: spec をIssue本文に整形して Issue 作成**

spec.md の §1–11 を HEREDOC で本文化し、`gh issue create` を実行する。

```bash
gh issue create \
  --repo uehara-beep/sunyutech-website \
  --title "コーポレートサイト全5ページのリニューアル（受注軸 / Astro移行 / ネイビー×オレンジ差し色）" \
  --label "feature" \
  --body "$(cat docs/superpowers/specs/2026-05-28-sunyutech-website-renewal-design.md)"
```

Expected: 出力に Issue URL（例: `https://github.com/uehara-beep/sunyutech-website/issues/4`）。番号 `N` をメモ。

- [ ] **Step 3: feature ブランチ作成**

`<N>` を Step 2 で取得した番号で置換。

```bash
git checkout -b feature/<N>-website-renewal-astro
```

Expected: `Switched to a new branch 'feature/<N>-website-renewal-astro'`

- [ ] **Step 4: 既存 spec を本ブランチで先行コミット**

spec.md は brainstorming 段階で書かれているがまだ未コミット。

```bash
git add docs/superpowers/specs/2026-05-28-sunyutech-website-renewal-design.md \
        docs/superpowers/plans/2026-05-28-sunyutech-renewal-phase-0-to-3.md \
        .gitignore
git commit -m "docs: リニューアル設計書とPhase 0–3計画を追加 (issue#<N>)"
```

Expected: 1 commit 作成、AI署名なし。

---

## Task 2: ディレクトリ枠と .gitignore 整備

**Files:**
- Create: `projects/sunyutech-renewal/{research,prompts,mockups,design/handoff}/.gitkeep`
- Modify: `.gitignore`

- [ ] **Step 1: 空ディレクトリ作成**

```bash
mkdir -p projects/sunyutech-renewal/research \
         projects/sunyutech-renewal/prompts \
         projects/sunyutech-renewal/mockups \
         projects/sunyutech-renewal/design/handoff
touch projects/sunyutech-renewal/mockups/.gitkeep \
      projects/sunyutech-renewal/design/handoff/.gitkeep
```

- [ ] **Step 2: .gitignore に Astro 用エントリ追記**

`.gitignore` 末尾に以下を追記（既存の `.DS_Store` と `.superpowers/` は維持）:

```
astro/node_modules/
astro/dist/
astro/.astro/
```

- [ ] **Step 3: コミット**

```bash
git add projects/ .gitignore
git commit -m "chore: リニューアル用ディレクトリと.gitignoreを整備 (issue#<N>)"
```

Expected: 1 commit。

---

## Task 3: Phase 0 BRIEF.md 作成

**Files:**
- Create: `projects/sunyutech-renewal/BRIEF.md`

- [ ] **Step 1: spec §1–6 を BRIEF.md に転記**

spec の §1（目的とスコープ）、§2（ターゲットと訴求）、§3（ブランドガイドライン）、§4（競合参照）、§5（技術スタックと公開先）、§6（法務・コンプライアンス）の内容を、BRIEF.md 形式（AI-LandBase テンプレ準拠）に書き直す。

`projects/sunyutech-renewal/BRIEF.md` の構成:

```markdown
# サンユウテック株式会社コーポレートサイト リニューアル BRIEF

## プロダクト概要
（spec §1.1 を転記）

## ターゲット仮説（主軸 = 受注）
- 主: （spec §2.1 を転記）
- 副1: （§2.2 採用候補者）
- 副2: （§2.2 既存取引先）

## 訴求ゴール（CTA / KPI）
（spec §2.3 を転記）

## ブランドガイドライン
- 主色: ネイビー #0B2545（固定）
- 差し色: オレンジ #E67E22
- 背景: 白 / 淡グレー #F4F6F9
- フォント: Noto Sans JP
（spec §3 から転記、禁止事項を箇条書きで明記）

## 参照競合
- https://sb-futami-aj.co.jp/
- https://www.kictec.co.jp/
- https://www.daiichi-cutter.co.jp/

## 公開先・技術スタック
- 開発: Astro 4.x + TypeScript + バニラCSS
- ホスティング: お名前.com 共用レンタルサーバー
- ビルド出力: 静的HTML/CSS/JS
- 既存素材活用: images/* をそのまま使用

## 法務・コンプライアンス
- プライバシーポリシー: 必要
- 特定商取引法表記: 不要
- 利用規約: 不要

## 公開スケジュール（仮）
- 本Issue内: Phase 0–5 完走（ローカルプレビュー検収）
- 別Issue: 本番置換
```

- [ ] **Step 2: 内容レビュー**

以下を確認:
- spec との不整合がない（ネイビー固定、競合3社URL、お名前.com）
- 「主軸 = 受注」「既存素材活用」が明示されている

- [ ] **Step 3: コミット**

```bash
git add projects/sunyutech-renewal/BRIEF.md
git commit -m "docs: Phase 0 BRIEF.mdを追加 (issue#<N>)"
```

Expected: 1 commit。

---

## Task 4: Phase 1-1 自社プロダクト分析

**Files:**
- Create: `projects/sunyutech-renewal/research/01-product-analysis.md`

- [ ] **Step 1: 既存サイト・リポジトリ資産を再読込**

以下のファイルを読む:
- `index.html`, `service.html`, `works.html`, `company.html`, `contact.html`
- `README.md`, `CONTRIBUTING.md`
- `images/` のファイル名一覧（hero-1〜8、service-*、work-*、about-image、logo）
- `git log --oneline -30`

メモすべき情報:
- 事業領域の言葉づかい（舗装 / WJ / コンクリート補修 の3本柱）
- 主要取引先・実績の傾向（既存 index.html / works.html から抽出）
- 既存サイトのコピーから読み取れる「自社が強調したい点」
- 現在のサイトが弱い点（受注軸視点で）

- [ ] **Step 2: 01-product-analysis.md を執筆**

以下構成で書く（400–600字 + 箇条書き）:

```markdown
# 01 自社プロダクト分析

## 1. このプロダクト（会社）が解決する課題（3本）
1. （課題1: 例 — 道路インフラの維持管理。短工期・夜間施工が要求される）
2. （課題2: 例 — 工場・プラント床のコンクリート補修。稼働を止めずに補修する制約）
3. （課題3: 例 — 老朽舗装の更新。アスファルト・コンクリート両対応の柔軟性）

## 2. ターゲットのペインポイント（機能一覧から逆算）

### 受注側（ゼネコン施工管理 / 元請技術部）
- 〜〜〜
- 〜〜〜

### 採用候補者
- 〜〜〜

### 既存取引先
- 〜〜〜

## 3. 強みの言語化
- 技術: 舗装 + ウォータージェット + コンクリート補修の3工法を一社で提供
- 体制: （既存 company.html / 取引先一覧から推定）
- 実績: （works.html の写真枚数・現場種類から推定）

## 4. 既存サイトの弱点（受注軸視点）
- ヒーローが「会社の挨拶」中心で「何を依頼できる会社か」が3秒で伝わらない
- 施工実績の見出しが工法名のみで、規模・期間・成果が伝わらない
- CTA が画面下部のみで、スクロール離脱者を捕まえられない
- （他、再読込で発見した点を追記）
```

- [ ] **Step 3: コミット**

```bash
git add projects/sunyutech-renewal/research/01-product-analysis.md
git commit -m "docs: research/01 自社プロダクト分析を追加 (issue#<N>)"
```

---

## Task 5: Phase 1-2 競合LP調査

**Files:**
- Create: `projects/sunyutech-renewal/research/02-competitor-lp-report.md`

- [ ] **Step 1: 3社の LP を WebFetch で取得**

各URLを WebFetch し、以下プロンプトで解析:

```
このLPのトップページ構造を解析してください:
1. ヒーローセクション: メインコピー、サブコピー、CTA文言、ビジュアル
2. CTA 設計: 主CTA・副CTA、配置位置、回数
3. ビジュアルスタイル: メインカラー、フォント印象、写真 vs 図版比率
4. ターゲット層シグナル: 誰向けに書かれているか
5. 情報設計: 上から順にどのセクションが並んでいるか
6. 差別化できる余白: このLPが手薄な点・サンユウテックが取れる位置
```

実行: 同一メッセージ内に WebFetch を3並列で発行する（直列にしない）。各WebFetch の url とプロンプトは下記。

- WebFetch #1: url=`https://sb-futami-aj.co.jp/`、prompt は上記6項目を当該会社に当てる
- WebFetch #2: url=`https://www.kictec.co.jp/`、同上
- WebFetch #3: url=`https://www.daiichi-cutter.co.jp/`、同上

Expected: 3社それぞれの解析結果テキストが返る。

- [ ] **Step 2: 02-competitor-lp-report.md を執筆**

構成:

```markdown
# 02 競合LP調査

## A. SB二見 / https://sb-futami-aj.co.jp/

### ヒーローセクション
- メインコピー: 「（実引用）」
- CTA: 「（実引用）」
- ビジュアル: （説明）

### CTA設計
（観察結果）

### ビジュアルスタイル
- メインカラー: #XXXX 系
- フォント印象: （説明）
- 写真:図版 比率: （%）

### ターゲット層シグナル
（説明）

### 情報設計（上から）
1. Hero
2. ...

### サンユウテックが取れる余白
（差別化点）

---

## B. ケイコン / https://www.kictec.co.jp/
（A と同形式で）

---

## C. 第一カッター / https://www.daiichi-cutter.co.jp/
（A と同形式で）

---

## 横断比較表

| 項目 | SB二見 | ケイコン | 第一カッター | サンユウテックが取る位置 |
|---|---|---|---|---|
| ヒーロー主語 | 「（要約）」 | … | … | 「現場視点の技術提案」 |
| 主CTA | … | … | … | お問い合わせ + 見積依頼 |
| 主色 | … | … | … | ネイビー × オレンジ差し色 |
| 写真活用 | … | … | … | 既存施工写真フル活用 |
| 弱点 / 余白 | … | … | … | （結論） |
```

- [ ] **Step 3: コミット**

```bash
git add projects/sunyutech-renewal/research/02-competitor-lp-report.md
git commit -m "docs: research/02 競合LP調査を追加 (issue#<N>)"
```

---

## Task 6: Phase 1-3 ペルソナ生成

**Files:**
- Create: `projects/sunyutech-renewal/research/03-persona.md`

- [ ] **Step 1: 01 と 02 を統合してペルソナを書く**

主軸ペルソナ1名 + 副ペルソナ2名。各ペルソナに以下を含める:
- 属性（年齢・職種・経験年数）
- 行動トリガー（LP訪問の動機）
- 信頼シグナル（何が購買判断を後押しするか）
- 情報フィルター（図解 / 数字 / 証言 のどれが刺さるか）
- 主要な懸念（離脱ポイント）

```markdown
# 03 ペルソナ

## 主軸: 高橋部長（仮名・ゼネコン現場代理人）

### 属性
- 45歳、ゼネコン中堅・施工管理職、現場代理人歴15年
- 高速道路・国道改良工事のサブで舗装補修の協力会社を探している
- 元請の安全書類提出ルールに精通、夜間施工経験豊富

### 行動トリガー
- 元請から「工期2週間以内・夜間で舗装補修できる会社を見つけて」と振られた瞬間
- 既存協力会社のスケジュールが埋まっているとき
- 新工法（WJ、急速硬化）が要件に入っているとき

### 信頼シグナル
- 過去の同規模・同工種の施工実績写真と「いつ・どこで・何㎡」の数字
- 主要取引先ロゴ（大手ゼネコン名があると即決）
- 24時間連絡可能 / 夜間対応の体制明記

### 情報フィルター
- 図解・規模数値・取引先ロゴ > 代表挨拶 > 抽象スローガン

### 主要な懸念
- 「写真は綺麗だが、急ぎの案件で電話に出てくれるのか」
- 「ゼネコンに出せる安全書類が揃っているか」
- 「金額感が事前に読めない（見積依頼の心理的ハードル）」

---

## 副1: 田中さん（仮名・26歳・舗装工経験3年）

### 属性
- 中規模舗装会社で3年経験、もう少し技術幅を広げたい
- スマホで会社名を検索 → 採用ページに到達
- 同期と給与・休日・現場種類を比較しがち

### 行動トリガー
- 求人サイトでは情報が薄いので、会社サイトで雰囲気を確認
- 友人から「サンユウテックって聞いたことある？」と言われた

### 信頼シグナル
- 現場社員の顔と声（インタビュー記事 or 短文）
- 月給・週休・技術習得制度の具体性
- 「未経験OK」より「3年でWJ・補修も覚えられる」の方が刺さる

### 情報フィルター
- 写真・社員の言葉 > 制度説明 > 代表挨拶

### 主要な懸念
- 「3K（きつい・汚い・危険）のイメージで家族が反対する」
- 「夜勤ばかりじゃないか」

---

## 副2: 山田課長（仮名・既存取引先 道路維持系）

### 属性
- 地域行政の道路維持担当・50代
- サンユウテックとは10年来の取引、年度初めに発注先見直し時にサイトを見る

### 行動トリガー
- 上長から「協力会社の経営状況と新工法対応を確認しておけ」と指示された

### 信頼シグナル
- 会社概要の数字（資本金・社員数・所在地）
- ISO・建設業許可番号・各種認定
- 直近の重大インシデント（事故）が報じられていないこと

### 情報フィルター
- 公的情報（許可・認証） > 沿革 > 実績

### 主要な懸念
- 「世代交代が進んでいるか、今後10年も続けられる体制か」
```

- [ ] **Step 2: コミット**

```bash
git add projects/sunyutech-renewal/research/03-persona.md
git commit -m "docs: research/03 ペルソナを追加 (issue#<N>)"
```

---

## Task 7: Phase 1-4 メッセージングブリーフ

**Files:**
- Create: `projects/sunyutech-renewal/research/04-messaging-brief.md`

- [ ] **Step 1: セクション別コピー方針を書く**

ペルソナと競合分析を踏まえ、各ページのセクション別キーフレーズを2-3本ずつ提示。

```markdown
# 04 メッセージングブリーフ

## トーン基本方針
- 主軸: 受注（B2B / ゼネコン施工管理）
- 一人称: 「弊社」「サンユウテック」
- 文末: 「〜します」「〜可能です」（敬体）。「！」「？」は使わない
- 数字を必ず入れる（年数・件数・規模・対応エリア）

## 禁止語
- 「安心・安全」（業界共通すぎる、差別化不能）
- 「お客様第一」（同上）
- 「最高品質」「絶対」（根拠なき最上級）
- 「DX」「イノベーション」（建設現場とトーン不一致）

## 業界用語の許容線
- 許容: 舗装・WJ（ウォータージェット）・打継ぎ・路盤・転圧・コア抜き
- 説明付き許容: 急速硬化・ハツリ・MSE工法
- 不可: 業界外で全く通じない略語（生コン以外の俗称等）

---

## TOP（index）

### Hero
- メイン: 「短工期・夜間・補修。現場の難所を、現場の技術で。」
- 副: 「舗装 / ウォータージェット / コンクリート補修の3工法をワンストップで提供」
- 候補2: 「道路を、止めない。 / 24時間体制の補修・舗装・WJ施工」
- 候補3: 「ゼネコン現場代理人に選ばれる、3つの工法。」

### 強み3点
1. 「3工法を一社で完結」 — 別社手配の手間を削減
2. 「夜間 / 短工期に強い」 — 過去X年間のYY件実績
3. 「主要取引先○○社と継続取引」 — ロゴで提示

### 事業ダイジェスト（→ service へ）
- 各工法の1行説明 + 主要対応規模

### 実績ハイライト（→ works へ）
- 直近3件をピックアップ。各「いつ・どこで・何㎡・工法」を1行に

### CTA（フォーム誘導）
- 主: 「お問い合わせ・見積依頼」
- 副: 「採用情報」

---

## 事業内容（service）

### イントロ
- 「3工法を、一社で。」 + 自社の対応範囲表

### 各工法セクション（舗装 / WJ / コンクリート補修）
- 工法とは（30字）
- どんな現場で使うか（具体例3つ、ペルソナの「これ自分の現場だ」反応を狙う）
- サンユウテックの対応規模・特徴（数字つき）
- 過去施工写真2-3枚（既存 images/service-* + work-* 流用）

### 工法選定ガイド（差別化セクション）
- 「補修すべきか、打ち替えすべきか」など、発注側の意思決定を助ける短いガイド
- 競合がやっていない「相談に値する会社」シグナル

---

## 施工実績（works）

### イントロ
- 「過去○年で○件、○㎡を超える施工実績」

### 工法別フィルタ
- 舗装 / WJ / コンクリート補修 でフィルタ可能（実装は次プラン）

### 各実績カード
- 写真1枚（既存 images/work-* から）
- 場所（市区町村まで、番地は出さない）
- 工法
- 規模（㎡ or m）
- 期間（YYYY-MM 〜 YYYY-MM）
- 一言ハイライト（30字）

---

## 会社概要（company）

### 代表挨拶
- 既存挨拶を「受注ペルソナが読んでも違和感ない」レベルに圧縮（300字程度）
- 「現場の人間が経営している会社」シグナルを残す

### 会社情報
- 表形式: 社名 / 所在地 / 設立 / 資本金 / 代表 / 社員数 / 建設業許可番号 / ISO等認証

### 沿革
- 主要マイルストーン10件以内

### 主要取引先
- ロゴ提示が望ましい（既存 index.html に列挙あり、流用）

---

## お問い合わせ（contact）

### イントロ
- 「見積依頼・現地調査のご相談はこちら。原則1営業日以内に返信します」

### フォーム項目（実装は次プラン）
- 会社名（必須）
- ご担当者名（必須）
- 電話番号（必須）
- メール（必須）
- 工事種別（プルダウン: 舗装 / WJ / コンクリート補修 / その他）
- 工事規模・場所・希望時期（自由記述）
- お問い合わせ内容（自由記述）

### 補足
- 電話番号大表示（モバイル CTA として効く）
- 営業時間明記
- フォーム送信時の送信手段は本Issue外で決定（mailto: フォールバック想定）

---

## ペルソナ別の優先メッセージ早見表

| ペルソナ | 主に刺さるページ | キーメッセージ |
|---|---|---|
| 高橋部長（受注） | index / service / works / contact | 短工期・夜間・実績数字・取引先ロゴ |
| 田中さん（採用） | company（採用情報セクション or 別ページ） | 現場社員の声・3年でWJ習得 |
| 山田課長（取引先） | company | 許可番号・認証・沿革・体制 |
```

- [ ] **Step 2: コミット**

```bash
git add projects/sunyutech-renewal/research/04-messaging-brief.md
git commit -m "docs: research/04 メッセージングブリーフを追加 (issue#<N>)"
```

---

## Task 8: Phase 2 ChatGPT image2 投入用プロンプト作成

**Files:**
- Create: `projects/sunyutech-renewal/prompts/image2-prompt.md`

- [ ] **Step 1: AI-LandBase 10項目構造に従って完成形プロンプトを書く**

```markdown
# image2-prompt.md（ChatGPT image2 投入用）

> このファイルは ChatGPT image2 に「順番に」投入してモックアップを生成するためのもの。
> 各セクション末尾に「Final prompt」を英語で用意してある。これをそのままコピペして使う。

---

## 共通設定（毎回先頭に置く）

### 1. Deliverable
LP mockup（visual brief、not final art）。サンユウテック株式会社（日本の建設会社・舗装/WJ/コンクリート補修）のコーポレートサイト・トップページの各セクションを順番に生成する。

### 2. Audience
- 主: ゼネコン施工管理 / 元springs技術部の中堅職（45歳前後、現場経験豊富、技術と実績で判断）
- 副: 採用候補者（26歳前後、舗装工経験3年）、既存取引先（地域行政50代）

### 3. Message
キー: 「短工期・夜間・補修。現場の難所を、現場の技術で。」
副: 「舗装 / ウォータージェット / コンクリート補修の3工法をワンストップで」
（詳細は research/04-messaging-brief.md 参照）

### 4. Visual concept
- 主色 navy: #0B2545（信頼・専門性）
- 差し色 orange: #E67E22（CTA・アクセント、面積は5%以下）
- 背景 white + soft gray: #F4F6F9
- 文字色 base: #1F2937、heading: #0B2545
- フォント: Noto Sans JP（heading 800、body 400）
- 写真: クライアント既存写真（hero-*.jpg、service-*.jpg、work-*.jpg）を「使う前提」の構図
- 全体トーン: 建設業の実直さ。IT風キラキラ・笑顔の作業員ストック写真は禁止

### 5. Composition（共通）
- 左寄せ heading、右側に建設写真
- 視線誘導: 左上 → 右下
- CTA は主+副 2つ並列、主オレンジ・副白×ネイビー枠
- 写真は実物のリアル感を残す（過度なフィルタなし）

### 6. Layout contract（必ず守る数値）
- page wrapper max-width: **1200px**
- content max-width: **880px**
- gutter: desktop **32px** / tablet **24px** / mobile **16px**
- full-bleed: hero と CTA バンドのみ
- responsive columns: desktop **3** / tablet **2** / mobile **1**
- horizontal scroll: **forbidden at any viewport**

### 8. Constraints（毎回明示）
- DO NOT generate final photography. これは visual brief であり、本番写真は既存クライアント写真を使う。
- DO NOT use orange as background fill — only for CTA, accents, small highlights.
- DO NOT use stock-photo smiling workers, IT-style sparkle illustrations, or cartoonish vectors.
- DO NOT include English-language headings — all heading text in Japanese.
- DO NOT add company logos other than「サンユウテック」.

### 9. Quality
- 探索: low
- 仕上げ候補: medium
- 採用版: high

---

## セクション別 prompt

### [Hero セクション] — まずここから

**7. Exact in-image text（正確に引用）**

- Main headline: 「短工期・夜間・補修。現場の難所を、現場の技術で。」
- Sub: 「舗装 / ウォータージェット / コンクリート補修の3工法をワンストップで」
- Primary CTA: 「お問い合わせ・見積依頼」
- Secondary CTA: 「施工実績を見る」

**10. Final prompt（英語、コピペ用）**

```
Generate a hero-section mockup for a Japanese construction company's corporate website.

Company: SANYUTECH — pavement, water-jet, and concrete-repair contractor.

Audience: middle-aged general-contractor site managers (45 years old). Tone: dependable, technical, calm — not flashy.

Color system:
- Primary navy #0B2545 (used for heading text and primary surfaces)
- Accent orange #E67E22 (used ONLY for the primary CTA button and small accents — must not exceed ~5% of the visible surface)
- Background white with soft gray #F4F6F9 used to separate the section
- Body text #1F2937

Layout contract (strict):
- Outer page wrapper centered at max-width 1200px
- Inner content max-width 880px
- Desktop view (1440px viewport)
- Left-aligned heading block taking ~55% width, right side reserved for a construction photograph placeholder (do NOT generate the photograph — render a soft gray placeholder rectangle with the label "PHOTO: existing client image" in small text)
- Two CTA buttons stacked horizontally: primary orange "お問い合わせ・見積依頼", secondary white-with-navy-border "施工実績を見る"
- A thin navigation bar above showing logo on the left and links "事業内容 / 施工実績 / 会社概要 / お問い合わせ"

Typography:
- Heading font Noto Sans JP, weight 800, size ~48-56px, navy color, three-line composition
- Subheading Noto Sans JP weight 400, size ~18px, dark gray
- Buttons size ~16px weight 600

Exact in-image text (must appear verbatim, in Japanese):
- Heading: 「短工期・夜間・補修。現場の難所を、現場の技術で。」
- Sub: 「舗装 / ウォータージェット / コンクリート補修の3工法をワンストップで」
- Primary CTA: 「お問い合わせ・見積依頼」
- Secondary CTA: 「施工実績を見る」

Forbidden:
- Stock-photo people, smiling workers
- IT-style sparkle / gradient illustrations
- Orange used as a background fill or large block
- English text in headings or CTAs
- Logos other than「サンユウテック」

Quality: medium (this is a layout brief, not final art).
```

---

### [強み3点セクション]

**7. Exact in-image text**

- Section title: 「サンユウテックが選ばれる理由」
- Card 1 title: 「3工法を、一社で。」
- Card 1 sub: 「舗装・WJ・コンクリート補修を自社で完結」
- Card 2 title: 「夜間・短工期に強い。」
- Card 2 sub: 「過去5年で○○件の夜間施工実績」
- Card 3 title: 「主要ゼネコンと継続取引。」
- Card 3 sub: 「ロゴ列をここに配置」

**10. Final prompt**

```
Generate a "why choose us" section mockup, immediately following the hero, for the same Japanese construction company website.

Reuse the same color system, typography, and layout contract.

Composition:
- Section title left-aligned, navy color, weight 800, size ~32px: 「サンユウテックが選ばれる理由」
- Below it a 3-column grid (desktop), each column a card with:
  - A small icon area at top (gray placeholder square 48x48 — do NOT design icons)
  - Card title navy weight 700 size ~22px
  - Card subtitle dark gray weight 400 size ~15px (2 lines)
- Cards background white with subtle 1px border #E5E7EB, padding 24px, border-radius 8px
- Section background soft gray #F4F6F9

Exact in-image text:
- Section title: 「サンユウテックが選ばれる理由」
- Card 1: title「3工法を、一社で。」sub「舗装・WJ・コンクリート補修を自社で完結」
- Card 2: title「夜間・短工期に強い。」sub「過去5年で○○件の夜間施工実績」
- Card 3: title「主要ゼネコンと継続取引。」sub「ロゴ列をここに配置」

Strict constraints (same as before): no stock photos, no orange backgrounds, no English headings, no sparkle illustrations.

Quality: medium.
```

---

### [事業ダイジェスト（→ service への誘導）セクション]

**7. Exact in-image text**

- Section title: 「事業内容」
- 3 service titles: 「舗装工事」「ウォータージェット工事」「コンクリート補修工事」
- 各 sub: 「対応規模・主要工法を一行で」
- CTA: 「事業内容を詳しく見る」

**10. Final prompt**

```
Generate a "service overview" section mockup, following the why-choose-us section.

Reuse the same color system, typography, and layout contract (1200px wrapper, 880px content, gutter 32px desktop).

Composition:
- Section title left-aligned: 「事業内容」
- Below it a 3-column grid (desktop), 2-col tablet, 1-col mobile. Each column shows:
  - A large rectangular photo placeholder at top labeled "PHOTO: service-paving.jpg" (or service-waterjet.jpg / service-concrete.jpg)
  - Service title navy weight 700 size ~24px below the photo
  - One-line description weight 400 size ~15px
  - A small "詳しく見る →" link in navy at bottom
- Below the grid, a single centered secondary CTA button "事業内容を詳しく見る" (white background, navy border)

Exact in-image text:
- Title: 「事業内容」
- Col 1: PHOTO label "service-paving.jpg", title「舗装工事」, sub「アスファルト・コンクリート両対応。新設・打替・補修まで一貫」
- Col 2: PHOTO label "service-waterjet.jpg", title「ウォータージェット工事」, sub「超高圧水で既存舗装・コンクリートを安全にハツリ・斫り」
- Col 3: PHOTO label "service-concrete.jpg", title「コンクリート補修工事」, sub「打継ぎ・断面修復・床補修まで現場規模に応じて対応」
- CTA: 「事業内容を詳しく見る」

Same forbidden list as before. Quality: medium.
```

---

### [実績ハイライトセクション]

**7. Exact in-image text**

- Section title: 「直近の施工実績」
- 3 work cards: 「（実work-*.jpg のラベルを使う）」「場所・工法・規模・期間」を表示
- CTA: 「すべての施工実績を見る」

**10. Final prompt**

```
Generate a "recent works" section mockup, following the service overview.

Reuse the design system (navy, soft gray background sections, 1200px wrapper).

Composition:
- Section title left-aligned: 「直近の施工実績」
- A 3-column grid (desktop), each card:
  - Wide aspect photo placeholder at top labeled with one of: "PHOTO: work-paving01.jpg", "PHOTO: work-waterjet02.jpg", "PHOTO: work-concrete01.jpg"
  - Below the photo, a metadata row: small navy chip showing the construction method (e.g., 「舗装」「WJ」「コンクリート補修」)
  - Card title navy weight 700 size ~20px (e.g., 「○○市国道○○号 舗装打替工事」)
  - 4-line metadata in monospace-ish weight 400 size ~13px:
    - 場所: ○○市
    - 工法: 舗装打替
    - 規模: 約2,400㎡
    - 期間: 2025-08 〜 2025-09
- Below the grid, a centered secondary CTA "すべての施工実績を見る"
- Section background white

Exact in-image text uses the four metadata lines per card as listed above (場所/工法/規模/期間 with realistic dummy values).

Same forbidden list. Quality: medium.
```

---

### [CTA バンドセクション]

**7. Exact in-image text**

- Heading: 「短工期・夜間案件、まずご相談ください。」
- Sub: 「原則1営業日以内に返信します」
- Primary CTA: 「お問い合わせ・見積依頼」
- 副情報: 電話番号と営業時間

**10. Final prompt**

```
Generate a closing CTA band mockup, the last full-width section before the footer.

Background: full-bleed navy #0B2545. Text color white. Width spans 100% of viewport (NOT constrained to 1200px), but inner text constrained to 880px content width.

Composition:
- Left-aligned heading white weight 800 size ~36px: 「短工期・夜間案件、まずご相談ください。」
- Below it sub-text white weight 400 size ~16px: 「原則1営業日以内に返信します」
- Right-aligned (same row, desktop) a stack:
  - Primary CTA orange #E67E22 background, white text, weight 700 size ~16px, padding 16px 32px: 「お問い合わせ・見積依頼」
  - Below the button, telephone number white weight 600 size ~18px: 「TEL: 0X-XXXX-XXXX」
  - Operating hours weight 400 size ~13px: 「平日 8:30 - 17:30」
- On mobile, stack heading → sub → CTA → tel → hours vertically.

Exact in-image text as above.

Forbidden: orange used anywhere except the CTA button itself; do not fill the whole band orange.

Quality: high (this is a candidate adoption render).
```

---

## イテレーション戦略

1. Hero を low で2-3案 → 方向が決まったら medium → high で採用版
2. その後、強み・事業・実績・CTA を同じビジュアル系で順番に生成
3. 採用版PNGを `projects/sunyutech-renewal/mockups/` にファイル名 `hero-vN.png` 等で保存
4. medium 版でブレた部分があれば「Xだけ変えて他はそのまま」で差分編集

## 採用後の作業（ユーザーが完了したら本セッションに戻る）

- `projects/sunyutech-renewal/mockups/` に最低5枚（hero / strength / service / works / cta）が揃った状態にする
- 戻ってきたら Task 10（Phase 3 handoff prompt）を実行
```

- [ ] **Step 2: コミット**

```bash
git add projects/sunyutech-renewal/prompts/image2-prompt.md
git commit -m "docs: Phase 2 image2投入用プロンプトを追加 (issue#<N>)"
```

---

## Task 9: USER CHECKPOINT — Phase 2 ChatGPT image2 実行

**Files:**
- User updates: `projects/sunyutech-renewal/mockups/` （hero / strength / service / works / cta の採用版PNG）

このタスクは **ユーザーが ChatGPT image2 で実行する**。Claude Code 側はここで一旦停止する。

- [ ] **Step 1: ユーザーが ChatGPT image2 で `image2-prompt.md` を順番に投入**

順序:
1. 共通設定（1-6, 8, 9）を会話冒頭に貼る
2. Hero の「Final prompt」を投入 → 採用版が出るまで反復
3. 強み3点・事業・実績・CTA を同様に
4. 採用版PNG（5枚以上）を `projects/sunyutech-renewal/mockups/` にダウンロード保存（命名: `hero-final.png` など）

- [ ] **Step 2: ユーザーが mockups を本リポジトリにコミット**

```bash
git add projects/sunyutech-renewal/mockups/
git commit -m "docs: Phase 2 mockup PNGを追加 (issue#<N>)"
```

- [ ] **Step 3: ユーザーが本セッションに戻り「mockup完了」と伝える**

その後 Claude Code が Task 10 に進む。

---

## Task 10: Phase 3 Claude Design handoff プロンプト作成

**Files:**
- Create: `projects/sunyutech-renewal/prompts/claude-design-handoff.md`

> ⚠ このタスクは Task 9 完了後に実施する。

- [ ] **Step 1: mockups を確認して handoff プロンプトに反映**

採用された mockup から、Claude Design に明示すべき項目を洗い出す:
- 採用 hero の構図の特徴（写真比率、heading の改行位置 等）
- 強みカードの border / shadow / padding の傾向
- 事業セクションの写真サイズ感
- CTA バンドの背景色（navy 確定）

- [ ] **Step 2: claude-design-handoff.md を執筆**

```markdown
# claude-design-handoff.md（Claude Design 投入用）

> Phase 2 で採用された mockup PNG（projects/sunyutech-renewal/mockups/）と
> research/04-messaging-brief.md を Claude Design に upload してから、
> 以下のプロンプトを投入する。

## 上に何を upload するか
- `mockups/hero-final.png`
- `mockups/strength-final.png`
- `mockups/service-final.png`
- `mockups/works-final.png`
- `mockups/cta-final.png`
- `research/04-messaging-brief.md`

## 投入プロンプト（コピペ用）

```
Use the uploaded mockup images as a visual direction reference, NOT as a literal final
specification. Your job is to infer a reusable visual system and produce a coherent,
production-friendly design package for a Japanese construction company's corporate website.

Company: SANYUTECH (建設業 / 舗装・ウォータージェット・コンクリート補修).
Audience: B2B — general-contractor site managers, with secondary recruitment and brand goals.

Normalize the visual direction into the following deliverables:

1. **Design principles** (3-5 bullet points: tone, hierarchy, density, photography stance)

2. **Design tokens** (YAML):
   - colors: navy primary #0B2545, orange accent #E67E22, background white, soft gray #F4F6F9, text base #1F2937, heading #0B2545, borders, hover states
   - typography: Noto Sans JP, weights (400/600/700/800), font-size scale, line-height
   - spacing scale (4px base, 8/12/16/24/32/48/64/96)
   - border-radius scale
   - shadow scale
   - z-index scale

3. **Layout contract** (YAML):
   - wrapper max-width 1200px
   - content max-width 880px
   - gutter: desktop 32 / tablet 24 / mobile 16
   - breakpoints: mobile <480, tablet 481-1023, desktop ≥1024
   - full-bleed sections (hero, CTA band) vs constrained
   - responsive columns: desktop 3 / tablet 2 / mobile 1
   - horizontal scroll forbidden at all viewports

4. **Component rules** for:
   - Header (logo + nav + mobile hamburger)
   - Footer (sitemap + company info + copyright)
   - Hero (heading block + photo block + CTAs)
   - SectionTitle (left-aligned, navy)
   - Button (primary orange / secondary navy outline / tertiary text)
   - FeatureCard (icon area + title + description)
   - WorkCard (photo + method chip + title + 4-line metadata)
   - Form (input / textarea / select with focus states)
   Each component: HTML structure, props/slots, states (default/hover/focus/disabled), responsive behavior, accessibility notes.

5. **Page structure** for 5 pages:
   - index.astro: hero, why-choose-us(3 cards), service overview(3 cards), recent works(3 cards), CTA band, footer
   - service.astro: hero(simplified), service-intro, 3 method sections (paving/WJ/concrete-repair), method-selection-guide, CTA band
   - works.astro: hero(simplified), filter UI(by method), grid of work cards, CTA band
   - company.astro: hero(simplified), president-message, company-info-table, history-timeline, partners-logos, CTA band
   - contact.astro: hero(simplified), form, telephone-block, map(optional), footer

6. **Acceptance criteria** (Must Have / Should Have / Nice to Have, mapped to the QA checklist in docs/superpowers/specs/2026-05-28-sunyutech-website-renewal-design.md §5)

7. **Implementation notes** (CRITICAL):
   - Target stack: Astro 4.x + TypeScript (strict) + vanilla CSS with design tokens. NOT Tailwind.
   - Image sources: ALL <img> src MUST reference existing files at `/images/<filename>` — do NOT request new image generation.
   - Photo file mapping:
     - Hero photo on index.astro: `/images/hero-1.jpg` (or rotate hero-1..8)
     - service.astro: `/images/service-paving.jpg`, `/images/service-waterjet.jpg`, `/images/service-concrete.jpg`
     - works.astro: `/images/work-paving0N.jpg`, `/images/work-waterjet0N.jpg`, `/images/work-concrete01.jpg`
     - company.astro: `/images/about-image.jpg`
     - Logo (all pages): `/images/logo.png`
     - favicon set: `/favicon.ico` and PNG variants
   - No inline styles; use Astro component scoped <style> or src/styles/*.css.
   - No raw color values outside src/styles/tokens.css.
   - Every <img> needs alt attribute; non-hero <img> gets loading="lazy".
   - Semantic HTML throughout.
   - JS minimal — Astro Island only when interactive UI required (e.g., works filter, mobile menu).

Export format: **Claude Code handoff bundle** (preferred). Save the bundle for download.

Do not copy any uploaded image literally. Translate visual direction into reusable design rules.
```

## 採用後の作業（ユーザーが完了したら本セッションに戻る）

- handoff bundle を unzip → `projects/sunyutech-renewal/design/handoff/` に配置
- 主要ファイル例:
  - `00-brief.md`
  - `10-design-principles.md`
  - `20-design-tokens.yaml`
  - `25-layout-contract.yaml`
  - `30-component-spec.md`
  - `40-page-structure.md`
  - `50-acceptance-criteria.md`
  - `60-implementation-notes.md`
- 戻ってきたら次プラン（Phase 4 Astro 実装）を Claude Code が書く
```

- [ ] **Step 3: コミット**

```bash
git add projects/sunyutech-renewal/prompts/claude-design-handoff.md
git commit -m "docs: Phase 3 Claude Design投入用プロンプトを追加 (issue#<N>)"
```

---

## Task 11: USER CHECKPOINT — Phase 3 Claude Design 実行

**Files:**
- User updates: `projects/sunyutech-renewal/design/handoff/*`

このタスクは **ユーザーが Claude Design で実行する**。Claude Code 側はここで再び停止する。

- [ ] **Step 1: ユーザーが Claude Design に mockup + messaging-brief を upload + プロンプト投入**

- [ ] **Step 2: 出力された handoff bundle を unzip して `design/handoff/` に配置**

- [ ] **Step 3: ユーザーが本リポジトリにコミット**

```bash
git add projects/sunyutech-renewal/design/handoff/
git commit -m "docs: Phase 3 Claude Design handoff bundleを追加 (issue#<N>)"
```

- [ ] **Step 4: ユーザーが本セッションに戻り「handoff完了」と伝える**

---

## Task 12: 次プラン（Phase 4–5）作成の起点

> ⚠ Task 11 完了後に実施する。

- [ ] **Step 1: Claude Code が handoff bundle を読み込み、Phase 4–5 用の新プランを書く**

新プランパス: `docs/superpowers/plans/YYYY-MM-DD-sunyutech-renewal-phase-4-5.md`

新プランで扱う範囲:
- Astro スキャフォールド
- design tokens / global.css 実装
- 共通レイアウト / コンポーネント実装（Header / Footer / Hero / SectionTitle / Button / FeatureCard / WorkCard / Form）
- 5ページ実装（index / service / works / company / contact）
- SEO / OGP / sitemap / robots
- README / CLAUDE.md 更新
- Phase 5 QA チェックリスト実行
- PR 作成 + マージ判断

- [ ] **Step 2: writing-plans スキルを再起動して新プランを書く**

ユーザーは Claude Code に「次プランを作って」と指示するだけでよい。

---

## Self-Review チェックリスト（このプランを書いた直後に Claude Code が走らせる）

- [ ] spec §1–11 すべてに対応するタスクがあるか
- [ ] Task 1–11 の各ステップに具体コマンド / 具体テキスト / 具体期待結果があるか
- [ ] Issue 番号 `<N>` プレースホルダは「Task 1 Step 2 で取得 → 以降置換」と明示されているか
- [ ] AI署名トレーラーがコミット例に混入していないか
- [ ] ユーザーチェックポイント（Task 9, 11）の停止/再開条件が明確か
- [ ] 「Phase 4–5 は別プラン」という分割理由が読者に伝わるか

---

**Last Updated**: 2026-05-28
