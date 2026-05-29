# Phase 5 QA Checklist 実行結果

実行日: 2026-05-29
対象コミット: feature/8-website-renewal-astro `9f34e8b` 時点
実行者: Claude Code（自動検査） + ユーザー目視（プレビュー）

## Must Have（マージ前提条件）

- [x] `astro build` 成功 — 5 page(s) built in 562ms（index / service / works / company / contact）
- [x] tokens.css 経由でない生カラーゼロ
  - 検査: `grep -rnE '#[0-9a-fA-F]{6}' astro/src --include='*.astro' --include='*.css' | grep -v 'tokens.css'`
  - 結果: 出力空 → PASS
- [x] 全 `<img>` に `alt` 属性
  - 検査: `grep -rhE '<img[^>]*>' astro/src --include='*.astro' | grep -vE 'alt='`
  - 結果: 出力空 → PASS
- [x] インラインスタイル不在
  - 検査: `grep -rhE 'style="' astro/src --include='*.astro'`
  - 結果: 出力空 → PASS
- [x] 全5ページに `<title>` と `<meta name="description">`
  - index/service/works/company/contact すべて title=1 desc=1
- [x] 全5ページに OGP 7タグ（og:type/title/description/url/image/site_name/locale）
- [x] 全5ページに Twitter Card 4タグ（card/title/description/image）
- [x] sitemap.xml 自動生成（`astro/dist/sitemap-0.xml` + `sitemap-index.xml`）
- [ ] 375 / 768 / 1440px で水平スクロールなし → **ユーザー目視待ち**
- [ ] Tab で全リンク・ボタン到達可能 → **ユーザー目視待ち**
- [ ] 主軸ペルソナ視点で「3秒で何の会社か」が伝わる → **ユーザー判断**

## Should Have（推奨）

- [x] 主要取引先（NIPPO・前田道路）と「合計24台」「3工法」が index と company に表示
  - 検査: `grep -E 'NIPPO|前田道路|24台|3工法' astro/src/pages/{index,company}.astro` で複数ヒット確認
- [ ] Lighthouse モバイル: Performance ≥ 90 / Accessibility ≥ 95 / Best Practices ≥ 95 / SEO ≥ 95 → **未計測**
- [ ] LCP < 2.5s → **未計測**
- [x] robots.txt + sitemap directive あり

## Nice to Have

- [ ] 採用ページ recruit.astro（本Issue外、別Issueへ）
- [ ] hero-*.jpg の WebP 変換（本Issue外、別Issueへ）
- [ ] 問い合わせフォーム外部サービス連携（本Issue外、別Issueへ）

## ユーザー検収

- [ ] ローカルプレビュー（`npm run preview` → http://localhost:4321）で 5ページ目視確認
- [ ] 主軸ペルソナ視点で「これなら出していい」と判断

## マージ判断

- Must Have 自動検査は全PASS、目視部分のみ未確認
- マージ可否: **ユーザー判断**（Must Have の目視3点 + Should Have の Lighthouse をどう扱うか）

---

**Last Updated**: 2026-05-29
