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
