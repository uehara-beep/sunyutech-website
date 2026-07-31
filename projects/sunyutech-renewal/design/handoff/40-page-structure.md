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
