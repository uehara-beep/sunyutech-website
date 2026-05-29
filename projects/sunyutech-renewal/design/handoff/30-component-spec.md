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
