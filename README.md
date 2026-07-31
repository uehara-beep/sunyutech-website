# サンユウテック株式会社 コーポレートサイト

サンユウテック株式会社のコーポレートサイトです。舗装工事・ウォータージェット工事・コンクリート補修工事の事業内容や会社情報を掲載しています。

**URL**: https://sunyutech.jp/

> ⚠ **現在2構成が並存中**（Issue #8 リニューアル進行中）
> - **現行**: ルート直下の `index.html` ほか 5ページ（フレームワーク不使用の静的HTML）。本番 sunyutech.jp として稼働中、本Issueでは温存。
> - **リニューアル中**: `astro/` 配下に Astro 4.x〜6.x 系 + バニラCSS + design tokens で実装中。本番置換は別Issueで対応予定。

---

## 現行サイト（ルート直下）

### 技術スタック

| 技術       | 用途             |
| ---------- | ---------------- |
| HTML5      | ページ構造       |
| CSS3       | スタイリング     |
| JavaScript | インタラクション |
| Google Fonts | Noto Sans JP   |

- フレームワーク不使用の静的HTMLサイト
- CSS変数によるテーマカラー管理
- バニラJSによるアニメーション・フォームバリデーション

### ディレクトリ構成

```
sunyutech-website/
├── index.html          # トップページ
├── company.html        # 会社概要
├── service.html        # 事業内容
├── works.html          # 施工実績
├── contact.html        # お問い合わせ
├── css/style.css
├── js/main.js
├── images/             # 既存写真資産（astro/public/images/ にもコピー済）
├── CONTRIBUTING.md
└── README.md
```

### ローカル開発

```bash
# Python
python3 -m http.server 8000

# Node.js (npx)
npx serve .
```

`http://localhost:8000` でアクセスできます。

### ページ構成

| ページ         | ファイル       | 内容                       |
| -------------- | -------------- | -------------------------- |
| トップ         | `index.html`   | ヒーロー、事業概要、強み   |
| 会社概要       | `company.html` | 会社情報、代表挨拶、沿革   |
| 事業内容       | `service.html` | 舗装・WJ・コンクリート補修 |
| 施工実績       | `works.html`   | 施工事例の紹介             |
| お問い合わせ   | `contact.html` | 問い合わせフォーム         |

---

## リニューアル中サイト（`astro/` 配下）

### 技術スタック

| 技術             | 用途                       |
| ---------------- | -------------------------- |
| Astro 4.x〜6.x   | 静的サイト生成             |
| TypeScript strict | 型安全                    |
| バニラCSS + design tokens | スタイリング      |
| Noto Sans JP     | フォント                   |
| @astrojs/sitemap | sitemap自動生成            |

- フレームワーク: Astro（Tailwind不使用）
- design tokens は `astro/src/styles/tokens.css` に CSS 変数として展開
- ホスティング想定: お名前.com 共用レンタルサーバー（FTPアップロード型）
- ビルド出力: `astro/dist/` 配下の静的HTML/CSS/JSのみ

### ローカル開発

```bash
cd astro
npm install
npm run dev      # http://localhost:4321
npm run build    # dist/ に静的ファイル生成
npm run preview  # ビルド出力をプレビュー
```

### ディレクトリ構成

```
astro/
├── astro.config.mjs
├── package.json
├── tsconfig.json
├── public/
│   ├── images/             # ルート images/ をコピー
│   ├── favicon.ico
│   ├── apple-touch-icon.png
│   └── robots.txt
└── src/
    ├── pages/(index|service|works|company|contact).astro
    ├── layouts/Base.astro
    ├── components/(Header|Footer|Hero|...).astro
    ├── content/works/*.json   # 施工実績 Content Collection
    ├── content.config.ts
    └── styles/(tokens.css|global.css)
```

### デザイン仕様

| 変数              | 値        | 用途                   |
| ----------------- | --------- | ---------------------- |
| `--color-navy-900`| `#0B2545` | 主色（信頼・専門性）   |
| `--color-orange-600`| `#E67E22` | 差し色（CTAのみ、5%以下） |
| `--color-gray-100`| `#F4F6F9` | セクション背景         |
| `--color-gray-900`| `#1F2937` | 本文文字色             |
| `--wrapper-max`   | `1200px`  | ページ全体幅           |
| `--content-max`   | `880px`   | 内側コンテンツ幅       |

---

## 関連ドキュメント

- [CONTRIBUTING.md](./CONTRIBUTING.md) — 開発規約・Issue/Git/コミットフロー
- [CLAUDE.md](./CLAUDE.md) — Claude Code 向けクイックリファレンス
- `docs/superpowers/specs/2026-05-28-sunyutech-website-renewal-design.md` — リニューアル設計書
- `docs/superpowers/plans/2026-05-28-sunyutech-renewal-phase-0-to-3.md` — Phase 0-3 実装計画
- `docs/superpowers/plans/2026-05-29-sunyutech-renewal-phase-4-5-claude-only.md` — Phase 4-5 実装計画
- `projects/sunyutech-renewal/` — ワークフロー成果物（BRIEF / research / design / handoff）

---

**Last Updated**: 2026-05-29
