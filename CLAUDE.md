# CLAUDE.md

サンユウテックHPリニューアル後の Claude Code 向けクイックリファレンス。

## 構成

- `astro/` 配下が新サイト（Astro 4.x〜6.x 系 + バニラCSS + design tokens）
- 既存 `index.html` 等は現行 sunyutech.jp として温存中（本Issue内では置換しない）
- リニューアル成果物: `projects/sunyutech-renewal/`

## 技術スタック

- Astro 6.x（minimal template、TypeScript strict）
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
