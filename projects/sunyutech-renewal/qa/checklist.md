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

- [x] 採用ページ recruit.astro 追加（Phase 6 で本Issue内対応）
- [ ] hero-*.jpg の WebP 変換（別Issueへ）
- [ ] 問い合わせフォーム外部サービス連携（別Issueへ）

## ユーザー検収

- [ ] ローカルプレビュー（`npm run preview` → http://localhost:4321）で 6ページ目視確認
- [ ] 主軸ペルソナ視点で「これなら出していい」と判断

## マージ判断

- Must Have 自動検査は全PASS、目視部分のみ未確認
- マージ可否: **ユーザー判断**

---

## Phase 6 ブランドリフレッシュ追記

実行日: 2026-05-29
対象コミット: `59faef1` 時点（弊社→当社置換前）

### 反映内容
- [x] BrandSignature「RENEW, NOT REBUILD.」全ページ反映（hero eyebrow + footer signature）
- [x] Hero 全面書き換え（高速道路リニューアル専門会社ポジション）
  - メイン: 「削る、直す、復旧する。高速道路リニューアルの専門会社。」
- [x] 強み3カード差し替え（一社完結 / 指名継続 / 自社施工）
- [x] 会社情報 実値化（設立2012 / 経審P732 / 大野城市御笠川6-2-5 / 社員50名 / 無事故5年以上）
- [x] 取引先表示を「ほか」表記に統一（NIPPO・前田道路の社名は前面から除外）
- [x] ProcessFlow セクション追加（はつり→断面修復→床版防水→舗装復旧 の4工程視覚化）
- [x] StatGrid 数値表示（経審P732 / 無事故5年以上 / 継続指名80%超 / 社員50名 / 取引10社+ / CONJET2台）
- [x] ScrollReveal Island 追加（fade-up 演出、prefers-reduced-motion 対応）
- [x] 採用ページ recruit.astro 新規追加
- [x] Header / MobileNav に「採用情報」リンク追加
- [x] Footer 刷新（BrandSignature footer variant + 株式会社サンユウテック + 大野城市御笠川6-2-5）
- [x] 弊社 → 当社 統一（research/04 と 05 を含む全文書）
- [x] 全ページ heading に英語アイブロー「RENEW, NOT REBUILD.」

### Phase 6 自動検査結果
- [x] `astro build` 成功 — 6 page(s) built（index / service / works / company / contact / recruit）
- [x] 生カラー検査: PASS（tokens.css 以外で 0 件）
- [x] インラインスタイル検査: PASS（0件）
- [x] img alt 検査: PASS（全件 alt 属性あり）
- [x] sitemap-index.xml + sitemap-0.xml 生成
- [x] 弊社残存検査: PASS（0件）

### Phase 6 ユーザー検収待ち
- [ ] プレビュー（http://localhost:4321）で6ページ目視
- [ ] 「RENEW, NOT REBUILD.」配置の最終OK
- [ ] ProcessFlow / StatGrid の見え方OK
- [ ] 主軸ペルソナ視点で「高速道路リニューアル専門会社」が伝わるか

---

**Last Updated**: 2026-05-29
