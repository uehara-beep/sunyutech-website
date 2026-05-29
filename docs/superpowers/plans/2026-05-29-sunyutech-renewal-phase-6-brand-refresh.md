# サンユウテック HPリニューアル Phase 6 ブランドリフレッシュ実装計画

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement task-by-task.

**Goal:** ヒアリング結果（`research/05-brand-hearing-result.md`）に基づき、「高速道路リニューアル専門会社／Renew, not rebuild.」へポジショニング変更とブランド・リフレッシュを実装する。

**Architecture:**
- 全Hero に英語アイブロー「RENEW, NOT REBUILD.」追加（BrandSignature コンポーネント）
- Hero/強みカード/会社情報を 05 の事実に基づき差し替え
- 新セクション 強み・技術 / 保有機械・体制 を TOP に追加
- 新ページ recruit.astro 追加
- 数値カウントアップ + scroll reveal は Astro Island で最小限実装
- 一人称 弊社 → 当社、社名表記 株式会社サンユウテック を全文書で統一

**Tech Stack:** 既存 Astro 6.4.2 構成を維持

**Source of Truth:** `projects/sunyutech-renewal/research/05-brand-hearing-result.md`

---

## File Structure（本プランで作成・変更）

| パス | 役割 |
|---|---|
| `astro/src/components/BrandSignature.astro` | 「RENEW, NOT REBUILD.」アイブロー（再利用） |
| `astro/src/components/ProcessFlow.astro` | 4工程の幾何アイコン化（はつり→補修→防水→復旧） |
| `astro/src/components/StatGrid.astro` | 数値表示（カウントアップは Island で） |
| `astro/src/components/ScrollReveal.astro` | fade-up Island（最小） |
| `astro/src/pages/index.astro` | hero/強み/事業/実績/強み技術/保有機械体制/CTA に再構成 |
| `astro/src/pages/service.astro` | hero + 3工法 + ワンストップ図 |
| `astro/src/pages/works.astro` | hero 更新 |
| `astro/src/pages/company.astro` | 実値で書き換え + 経審P / 無事故5年 / 取引先「ほか」表記 |
| `astro/src/pages/contact.astro` | hero 更新 |
| `astro/src/pages/recruit.astro` | 新規（採用軸独立、骨組み） |
| `astro/src/components/Header.astro` | nav に「採用情報」追加 |
| `astro/src/components/Footer.astro` | BrandSignature 反映、社名・住所・取引先表記 |

---

## Task 1: BrandSignature コンポーネント

**Files:**
- Create: `astro/src/components/BrandSignature.astro`

- [ ] **Step 1: 実装**

```astro
---
interface Props {
  variant?: 'eyebrow' | 'footer';
}
const { variant = 'eyebrow' } = Astro.props;
---
<p class={`brand-sig brand-sig-${variant}`}>RENEW, NOT REBUILD.</p>
<style>
  .brand-sig {
    font-family: var(--font-base);
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--color-navy-900);
    font-weight: var(--fw-bold);
    margin: 0;
  }
  .brand-sig-eyebrow {
    font-size: var(--fs-sm);
    padding-bottom: var(--sp-3);
    border-bottom: 1px solid var(--color-gray-200);
    margin-bottom: var(--sp-4);
    width: max-content;
    max-width: 100%;
  }
  .brand-sig-footer {
    font-size: var(--fs-base);
    color: var(--color-white);
    letter-spacing: 0.22em;
  }
  @media (max-width: 480px) {
    .brand-sig-eyebrow { font-size: var(--fs-xs); }
  }
</style>
```

- [ ] **Step 2: コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
git add astro/src/components/BrandSignature.astro
git commit -m "feat(astro): BrandSignature コンポーネントを追加 (issue#8)"
```

---

## Task 2: Hero コンポーネントを eyebrow 対応にリファイン

**Files:**
- Modify: `astro/src/components/Hero.astro`

- [ ] **Step 1: BrandSignature を eyebrow として埋め込む**

`astro/src/components/Hero.astro` を以下に上書き:

```astro
---
import Button from './Button.astro';
import BrandSignature from './BrandSignature.astro';
interface Props {
  heading1: string;
  heading2: string;
  sub: string;
  primaryCta: { label: string; href: string };
  secondaryCta?: { label: string; href: string };
  imageSrc: string;
  imageAlt: string;
  showSignature?: boolean;
}
const { heading1, heading2, sub, primaryCta, secondaryCta, imageSrc, imageAlt, showSignature = true } = Astro.props;
---
<section class="hero">
  <div class="hero-inner">
    <div class="hero-text">
      {showSignature && <BrandSignature variant="eyebrow" />}
      <h1>
        <span>{heading1}</span>
        <span>{heading2}</span>
      </h1>
      <p class="hero-sub">{sub}</p>
      <div class="hero-ctas">
        <Button variant="primary" href={primaryCta.href} label={primaryCta.label} />
        {secondaryCta && <Button variant="secondary" href={secondaryCta.href} label={secondaryCta.label} />}
      </div>
    </div>
    <div class="hero-image">
      <img src={imageSrc} alt={imageAlt} width="660" height="540" loading="eager" fetchpriority="high" />
    </div>
  </div>
</section>
<style>
  .hero { background: var(--color-white); padding: var(--section-py-desktop) 0; }
  .hero-inner {
    max-width: var(--wrapper-max);
    margin: 0 auto;
    padding: 0 var(--gutter-desktop);
    display: grid;
    grid-template-columns: 55% 45%;
    gap: var(--sp-8);
    align-items: center;
  }
  .hero h1 {
    font-size: var(--fs-5xl);
    font-weight: var(--fw-extrabold);
    color: var(--color-navy-900);
    line-height: var(--lh-tight);
    margin-bottom: var(--sp-6);
  }
  .hero h1 span { display: block; }
  .hero-sub { font-size: var(--fs-lg); color: var(--color-gray-900); line-height: var(--lh-relaxed); margin-bottom: var(--sp-8); }
  .hero-ctas { display: flex; gap: var(--sp-4); flex-wrap: wrap; }
  .hero-image img { width: 100%; height: auto; border-radius: var(--radius-md); object-fit: cover; }
  @media (max-width: 1023px) {
    .hero-inner { padding: 0 var(--gutter-tablet); }
    .hero h1 { font-size: var(--fs-4xl); }
  }
  @media (max-width: 768px) {
    .hero-inner { grid-template-columns: 1fr; padding: 0 var(--gutter-mobile); }
    .hero h1 { font-size: var(--fs-3xl); }
    .hero { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 2: コミット**

```bash
git add astro/src/components/Hero.astro
git commit -m "feat(astro): Hero に BrandSignature eyebrow を実装 (issue#8)"
```

---

## Task 3: ProcessFlow コンポーネント（4工程の視覚化）

**Files:**
- Create: `astro/src/components/ProcessFlow.astro`

- [ ] **Step 1: 実装**

4工程「① 床版はつり → ② 断面修復 → ③ 床版防水 → ④ 舗装復旧」を navy + orange の幾何アイコンで横並びに。

```astro
---
const steps = [
  { num: '01', label: '床版はつり', sub: 'CONJET ロボット' },
  { num: '02', label: '断面修復', sub: '欠損部の再生' },
  { num: '03', label: '床版防水', sub: '層の新設' },
  { num: '04', label: '舗装復旧', sub: '一気に再生' },
] as const;
---
<div class="process-flow" role="list" aria-label="高速道路リニューアル ワンストップ4工程">
  {steps.map((s, i) => (
    <>
      <div class="step" role="listitem">
        <div class="num">{s.num}</div>
        <div class="text">
          <h4>{s.label}</h4>
          <p>{s.sub}</p>
        </div>
      </div>
      {i < steps.length - 1 && <div class="arrow" aria-hidden="true">→</div>}
    </>
  ))}
</div>
<style>
  .process-flow {
    display: grid;
    grid-template-columns: 1fr auto 1fr auto 1fr auto 1fr;
    gap: var(--sp-3);
    align-items: stretch;
    padding: var(--sp-8) 0;
  }
  .step {
    display: flex;
    flex-direction: column;
    gap: var(--sp-3);
    padding: var(--sp-6);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
  }
  .num {
    font-family: var(--font-base);
    font-size: var(--fs-2xl);
    font-weight: var(--fw-extrabold);
    color: var(--color-orange-600);
    letter-spacing: 0.05em;
  }
  .text h4 {
    font-size: var(--fs-lg);
    font-weight: var(--fw-bold);
    color: var(--color-navy-900);
    margin: 0 0 var(--sp-1);
  }
  .text p {
    font-size: var(--fs-xs);
    color: var(--color-gray-700);
    margin: 0;
  }
  .arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-navy-700);
    font-size: var(--fs-xl);
    font-weight: var(--fw-bold);
  }
  @media (max-width: 1023px) {
    .process-flow { grid-template-columns: 1fr 1fr; }
    .arrow { display: none; }
  }
  @media (max-width: 480px) {
    .process-flow { grid-template-columns: 1fr; }
  }
</style>
```

- [ ] **Step 2: コミット**

```bash
git add astro/src/components/ProcessFlow.astro
git commit -m "feat(astro): ProcessFlow コンポーネント追加 (issue#8)"
```

---

## Task 4: StatGrid + ScrollReveal コンポーネント

**Files:**
- Create: `astro/src/components/StatGrid.astro`
- Create: `astro/src/components/ScrollReveal.astro`

- [ ] **Step 1: ScrollReveal.astro**

最小Island。`<intersection-observer>` で fade-up を1回だけ発火。

```astro
---
---
<div class="reveal">
  <slot />
</div>
<script>
  const els = document.querySelectorAll<HTMLElement>('.reveal');
  if ('IntersectionObserver' in window && els.length) {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('is-visible');
          obs.unobserve(e.target);
        }
      });
    }, { rootMargin: '0px 0px -80px 0px' });
    els.forEach(el => obs.observe(el));
  } else {
    els.forEach(el => el.classList.add('is-visible'));
  }
</script>
<style>
  .reveal {
    opacity: 0;
    transform: translateY(16px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
  }
  .reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
  }
  @media (prefers-reduced-motion: reduce) {
    .reveal { opacity: 1; transform: none; transition: none; }
  }
</style>
```

- [ ] **Step 2: StatGrid.astro**

```astro
---
interface Stat {
  value: string;
  unit?: string;
  label: string;
  caveat?: string;
}
interface Props {
  stats: Stat[];
}
const { stats } = Astro.props;
---
<div class="stat-grid" role="list">
  {stats.map(s => (
    <article class="stat" role="listitem">
      <div class="value-row">
        <span class="value">{s.value}</span>
        {s.unit && <span class="unit">{s.unit}</span>}
      </div>
      <p class="label">{s.label}</p>
      {s.caveat && <p class="caveat">{s.caveat}</p>}
    </article>
  ))}
</div>
<style>
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--sp-6);
  }
  .stat {
    padding: var(--sp-6);
    background: var(--color-white);
    border-top: 3px solid var(--color-orange-600);
    border-left: 1px solid var(--color-gray-200);
    border-right: 1px solid var(--color-gray-200);
    border-bottom: 1px solid var(--color-gray-200);
    border-radius: 0 0 var(--radius-md) var(--radius-md);
  }
  .value-row {
    display: flex;
    align-items: baseline;
    gap: var(--sp-2);
    margin-bottom: var(--sp-3);
  }
  .value {
    font-size: var(--fs-5xl);
    font-weight: var(--fw-extrabold);
    color: var(--color-navy-900);
    line-height: 1;
    letter-spacing: -0.02em;
  }
  .unit {
    font-size: var(--fs-lg);
    font-weight: var(--fw-bold);
    color: var(--color-navy-800);
  }
  .label {
    font-size: var(--fs-sm);
    font-weight: var(--fw-semibold);
    color: var(--color-gray-900);
    margin: 0 0 var(--sp-1);
  }
  .caveat {
    font-size: var(--fs-xs);
    color: var(--color-gray-500);
    margin: 0;
  }
  @media (max-width: 1023px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 480px) {
    .stat-grid { grid-template-columns: 1fr; }
    .value { font-size: var(--fs-4xl); }
  }
</style>
```

- [ ] **Step 3: コミット**

```bash
git add astro/src/components/StatGrid.astro astro/src/components/ScrollReveal.astro
git commit -m "feat(astro): StatGrid と ScrollReveal を追加 (issue#8)"
```

---

## Task 5: Header に「採用情報」追加 + Footer リフレッシュ

**Files:**
- Modify: `astro/src/components/Header.astro`
- Modify: `astro/src/components/MobileNav.astro`
- Modify: `astro/src/components/Footer.astro`

- [ ] **Step 1: Header.astro / MobileNav.astro に recruit 追加**

`navItems` 配列に以下を追加（service の次、works の前）:

```typescript
{ href: '/recruit/', label: '採用情報', key: 'recruit' as const },
```

Props の `currentPage` 型に `'recruit'` を追加:

```typescript
currentPage: 'index' | 'service' | 'works' | 'company' | 'contact' | 'recruit';
```

同じ変更を MobileNav.astro にも行う。

- [ ] **Step 2: Footer.astro を BrandSignature 入り + 実情報に**

```astro
---
import BrandSignature from './BrandSignature.astro';
const year = new Date().getFullYear();
---
<footer class="footer">
  <div class="footer-inner">
    <div class="col col-brand">
      <BrandSignature variant="footer" />
      <p class="brand-name">株式会社サンユウテック</p>
      <p>福岡県大野城市御笠川6-2-5</p>
      <p>TEL 092-555-9211（平日 8:00〜17:00）</p>
    </div>
    <nav class="col" aria-label="フッターナビゲーション">
      <h3 class="col-title">サイトマップ</h3>
      <ul>
        <li><a href="/service/">事業内容</a></li>
        <li><a href="/works/">施工実績</a></li>
        <li><a href="/company/">会社案内</a></li>
        <li><a href="/recruit/">採用情報</a></li>
        <li><a href="/contact/">お問い合わせ</a></li>
      </ul>
    </nav>
    <div class="col">
      <h3 class="col-title">お問い合わせ</h3>
      <p><a href="/contact/">見積依頼・現地調査のご相談</a></p>
      <p>原則 翌営業日に一次回答します。</p>
    </div>
  </div>
  <p class="copyright">© {year} 株式会社サンユウテック</p>
</footer>
<style>
  .footer {
    background: var(--color-navy-900);
    color: var(--color-white);
    padding: var(--section-py-tablet) 0 var(--sp-8);
  }
  .footer-inner {
    max-width: var(--wrapper-max);
    margin: 0 auto;
    padding: 0 var(--gutter-desktop);
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr;
    gap: var(--sp-12);
  }
  .brand-name { font-size: var(--fs-lg); font-weight: var(--fw-bold); color: var(--color-white); margin: var(--sp-4) 0 var(--sp-3); }
  .col-title {
    color: var(--color-white);
    font-size: var(--fs-lg);
    font-weight: var(--fw-bold);
    margin-bottom: var(--sp-4);
  }
  .footer p, .footer li { color: var(--color-gray-200); font-size: var(--fs-sm); line-height: var(--lh-relaxed); }
  .footer a { color: var(--color-gray-200); }
  .footer a:hover { color: var(--color-orange-600); }
  .copyright {
    max-width: var(--wrapper-max);
    margin: var(--sp-12) auto 0;
    padding: var(--sp-6) var(--gutter-desktop) 0;
    border-top: 1px solid var(--color-navy-800);
    text-align: center;
    font-size: var(--fs-xs);
    color: var(--color-gray-300);
  }
  @media (max-width: 1023px) {
    .footer-inner { grid-template-columns: 1fr 1fr; padding: 0 var(--gutter-tablet); gap: var(--sp-8); }
    .copyright { padding: var(--sp-6) var(--gutter-tablet) 0; }
  }
  @media (max-width: 480px) {
    .footer-inner { grid-template-columns: 1fr; padding: 0 var(--gutter-mobile); gap: var(--sp-6); }
    .copyright { padding: var(--sp-6) var(--gutter-mobile) 0; }
  }
</style>
```

- [ ] **Step 3: コミット**

```bash
git add astro/src/components/Header.astro astro/src/components/MobileNav.astro astro/src/components/Footer.astro
git commit -m "feat(astro): Header/Footer に採用情報とBrandSignatureを反映 (issue#8)"
```

---

## Task 6: index.astro 全面書き換え

**Files:**
- Modify: `astro/src/pages/index.astro`

- [ ] **Step 1: 新セクション構成で実装**

セクション順:
1. Hero（BrandSignature + 新コピー）
2. ProcessFlow（ワンストップ4工程）
3. 強み3カード（新文言）
4. 事業内容ダイジェスト（既存維持・3工法）
5. 強み・技術（用途別の見せ方）
6. 保有機械・体制（StatGrid + 文字）
7. 直近の施工実績
8. CTA Band

```astro
---
import Base from '../layouts/Base.astro';
import Hero from '../components/Hero.astro';
import SectionTitle from '../components/SectionTitle.astro';
import FeatureCard from '../components/FeatureCard.astro';
import WorkCard from '../components/WorkCard.astro';
import CtaBand from '../components/CtaBand.astro';
import Button from '../components/Button.astro';
import ProcessFlow from '../components/ProcessFlow.astro';
import StatGrid from '../components/StatGrid.astro';
import ScrollReveal from '../components/ScrollReveal.astro';
---
<Base
  title="株式会社サンユウテック | 高速道路リニューアルの専門会社"
  description="株式会社サンユウテックは福岡・大野城を拠点に、高速道路の床版を「はつり→断面修復→床版防水→舗装復旧」まで一社で担う、リニューアル工事の専門会社です。"
  currentPage="index"
>
  <Hero
    heading1="削る、直す、復旧する。"
    heading2="高速道路リニューアルの専門会社。"
    sub="福岡・大野城を拠点に、高速道路の床版をはつりから舗装復旧まで一社で担います。"
    primaryCta={{ label: '見積依頼・現地調査のご相談', href: '/contact/' }}
    secondaryCta={{ label: '施工実績を見る', href: '/works/' }}
    imageSrc="/images/hero-1.jpg"
    imageAlt="サンユウテックの施工現場"
  />

  <section class="section section-process">
    <div class="container">
      <SectionTitle text="ワンストップ施工 4 工程" />
      <p class="lead">床版リニューアルを一社で完結します。</p>
      <ScrollReveal>
        <ProcessFlow />
      </ScrollReveal>
    </div>
  </section>

  <section class="section section-strength">
    <div class="container">
      <SectionTitle text="サンユウテックが選ばれる理由" />
      <div class="grid-3">
        <FeatureCard number="01" title="はつりから復旧まで、一社完結" description="床版はつりから断面修復・床版防水・舗装復旧まで自社一貫。工程を分断しません。" />
        <FeatureCard number="02" title="指名され続ける、継続の実績" description="舗装は継続・指名が8割超。無事故5年以上、元請からの安全表彰も受賞しています。" />
        <FeatureCard number="03" title="丸投げしない、自社施工" description="社員50名の約9割が現場・技術。施工管理10名で複数現場を並行対応します。" />
      </div>
    </div>
  </section>

  <section class="section section-service">
    <div class="container">
      <SectionTitle text="事業内容" />
      <p class="lead">3工法を、一社で。</p>
      <div class="grid-3">
        <FeatureCard
          title="舗装工事"
          description="インター部・高速本線・景観舗装まで、最大約12,000m²規模に対応します。"
          image={{ src: '/images/service-paving.jpg', alt: '舗装工事の現場' }}
          link={{ href: '/service/#paving', label: '詳しく見る' }}
        />
        <FeatureCard
          title="ウォータージェット工事"
          description="200MPa超の高圧ポンプとCONJETロボットで、床版はつり・目荒らしを精密に施工します。"
          image={{ src: '/images/service-waterjet.jpg', alt: 'ウォータージェット工事の現場' }}
          link={{ href: '/service/#waterjet', label: '詳しく見る' }}
        />
        <FeatureCard
          title="コンクリート補修工事"
          description="断面修復・床版防水・繊維シート補強まで、はつりから補修まで一体で施工します。"
          image={{ src: '/images/service-concrete.jpg', alt: 'コンクリート補修工事の現場' }}
          link={{ href: '/service/#concrete', label: '詳しく見る' }}
        />
      </div>
    </div>
  </section>

  <section class="section section-stats">
    <div class="container">
      <SectionTitle text="保有機械・体制" />
      <p class="lead">数字で語る、当社の地力。</p>
      <ScrollReveal>
        <StatGrid stats={[
          { value: '732', label: '経営事項審査 舗装 総合評定値', caveat: '客観的な信用力を示す指標' },
          { value: '5', unit: '年以上', label: '無事故・無災害を継続中', caveat: '元請からの安全表彰も受賞' },
          { value: '80', unit: '%超', label: '舗装の継続・指名率', caveat: '長期取引が信頼の証' },
          { value: '50', unit: '名', label: '社員数（うち現場・技術45名）', caveat: '徹底した現場主義' },
          { value: '10', unit: '社+', label: '取引のある主要道路ゼネコン', caveat: '全国の元請と継続関係' },
          { value: '2', unit: '台', label: 'CONJET 自動はつりロボット保有', caveat: '床版はつりの中核機材' },
        ]} />
      </ScrollReveal>
    </div>
  </section>

  <section class="section section-works">
    <div class="container">
      <SectionTitle text="直近の施工実績" />
      <div class="grid-3">
        <WorkCard
          image={{ src: '/images/work-paving02.jpg', alt: '高速道路舗装打替工事' }}
          methodChip="舗装"
          title="高速道路本線 舗装打替工事"
          location="九州管内"
          method="舗装打替"
          scale="約12,000m²"
        />
        <WorkCard
          image={{ src: '/images/work-waterjet01.jpg', alt: '橋梁床版WJ施工' }}
          methodChip="WJ"
          title="高速道路橋梁床版WJ施工"
          location="九州管内"
          method="CONJET 床版はつり・目荒らし"
          scale="200MPa超 / 大規模"
        />
        <WorkCard
          image={{ src: '/images/work-concrete01.jpg', alt: 'コンクリート補修施工' }}
          methodChip="コンクリート補修"
          title="高速道路橋梁コンクリート補修"
          location="九州管内"
          method="断面修復・床版防水・舗装復旧"
          scale="一気通貫施工"
        />
      </div>
      <div class="more-cta">
        <Button variant="tertiary" href="/works/" label="すべての施工実績を見る" />
      </div>
    </div>
  </section>

  <CtaBand heading="床版リニューアル案件、まずご相談ください。" />
</Base>

<style>
  .section { padding: var(--section-py-desktop) 0; }
  .section-process { background: var(--color-white); }
  .section-strength { background: var(--color-gray-100); }
  .section-service { background: var(--color-white); }
  .section-stats { background: var(--color-gray-100); }
  .section-works { background: var(--color-white); }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--sp-6); }
  .lead { font-size: var(--fs-xl); color: var(--color-navy-900); font-weight: var(--fw-semibold); margin-bottom: var(--sp-8); }
  .more-cta { margin-top: var(--sp-8); text-align: center; }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .grid-3 { grid-template-columns: repeat(2, 1fr); }
    .section { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 768px) {
    .container { padding: 0 var(--gutter-mobile); }
    .grid-3 { grid-template-columns: 1fr; }
    .section { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 2: コミット**

```bash
git add astro/src/pages/index.astro
git commit -m "feat(astro): index を高速リニューアル専門会社ポジションに刷新 (issue#8)"
```

---

## Task 7: company.astro 実値化

**Files:**
- Modify: `astro/src/pages/company.astro`

- [ ] **Step 1: 実値で全文書き換え**

```astro
---
import Base from '../layouts/Base.astro';
import SectionTitle from '../components/SectionTitle.astro';
import CtaBand from '../components/CtaBand.astro';
---
<Base
  title="会社案内 | 株式会社サンユウテック"
  description="株式会社サンユウテックの会社案内。福岡県大野城市御笠川を拠点に、業歴13年・社員50名で高速道路リニューアル工事を専門に施工しています。"
  currentPage="company"
  ogImage="/images/about-image.jpg"
>
  <section class="page-hero">
    <div class="container">
      <p class="brand-sig">RENEW, NOT REBUILD.</p>
      <h1>高速道路リニューアルを、九州から。</h1>
    </div>
  </section>

  <section class="message">
    <div class="container narrow">
      <SectionTitle text="当社について" />
      <p>株式会社サンユウテックは、2012年に福岡県大野城市で設立された、高速道路リニューアル工事の専門会社です。床版はつり・断面修復・床版防水・舗装復旧の4工程を自社一貫で施工できる希少な体制を持ち、全国の主要道路ゼネコン10社以上と継続的な取引関係にあります。</p>
      <p>業歴13年（第14期）、社員50名のうち45名が現場・技術。施工管理10名による複数現場の並行運営、無事故・無災害5年以上の継続、元請からの安全表彰受賞など、検証可能な事実で品質と信頼を裏付けています。</p>
    </div>
  </section>

  <section class="info">
    <div class="container narrow">
      <SectionTitle text="会社概要" />
      <dl class="info-table">
        <div><dt>社名</dt><dd>株式会社サンユウテック</dd></div>
        <div><dt>設立</dt><dd>2012年（業歴13年・現在第14期）</dd></div>
        <div><dt>資本金</dt><dd>1,000万円</dd></div>
        <div><dt>所在地</dt><dd>福岡県大野城市御笠川6-2-5</dd></div>
        <div><dt>事業内容</dt><dd>舗装工事、ウォータージェット工事、コンクリート補修工事</dd></div>
        <div><dt>建設業許可</dt><dd>一般建設業許可（とび・土工／建築／土木／舗装）</dd></div>
        <div><dt>経営事項審査 P 点</dt><dd>舗装 732 ／ とび・土工 568 ／ 土木一式 553</dd></div>
        <div><dt>社員数</dt><dd>50名（現場・技術 45名／間接 5名）</dd></div>
        <div><dt>有資格者</dt><dd>1級土木施工管理技士 1名、2級土木施工管理技士 3名 ほか</dd></div>
        <div><dt>保有機材</dt><dd>CONJET 自動はつりロボット 2台、200MPa超 高圧ポンプ 2台、アスファルトフィニッシャー 1台 ほか</dd></div>
        <div><dt>安全実績</dt><dd>無事故・無災害 5年以上継続、元請からの安全表彰受賞</dd></div>
        <div><dt>その他</dt><dd>インボイス登録、社会保険・労災 完備</dd></div>
        <div><dt>連絡先</dt><dd>TEL 092-555-9211（平日 8:00〜17:00）</dd></div>
      </dl>
    </div>
  </section>

  <section class="image-band">
    <div class="container">
      <img src="/images/about-image.jpg" alt="サンユウテックの施工現場" width="1200" height="600" loading="lazy" />
    </div>
  </section>

  <section class="partners">
    <div class="container narrow">
      <SectionTitle text="主要取引先" />
      <p class="partners-note">全国の主要道路ゼネコン 10 社以上と継続的に取引しています。掲載可能な先のみ五十音順で列記します。</p>
      <ul class="partners-list">
        <li>大手道路ゼネコン各社</li>
        <li>地場ゼネコン各社</li>
        <li>公的発注機関</li>
        <li>ほか</li>
      </ul>
    </div>
  </section>

  <CtaBand heading="協力会社のご検討・ご相談はお気軽にどうぞ。" />
</Base>

<style>
  .page-hero { background: var(--color-navy-900); color: var(--color-white); padding: var(--section-py-tablet) 0; }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .container.narrow { max-width: var(--content-max); }
  .page-hero .brand-sig {
    font-size: var(--fs-sm);
    letter-spacing: 0.18em;
    color: var(--color-orange-600);
    font-weight: var(--fw-bold);
    margin: 0 0 var(--sp-3);
  }
  .page-hero h1 { color: var(--color-white); font-size: var(--fs-3xl); font-weight: var(--fw-extrabold); }

  .message, .info, .partners { padding: var(--section-py-desktop) 0; }
  .message { background: var(--color-white); }
  .info { background: var(--color-gray-100); }
  .partners { background: var(--color-white); }
  .message p { font-size: var(--fs-base); line-height: var(--lh-relaxed); color: var(--color-gray-900); margin-bottom: var(--sp-4); }

  .info-table { display: flex; flex-direction: column; gap: 0; }
  .info-table > div {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: var(--sp-4);
    padding: var(--sp-4) 0;
    border-bottom: 1px solid var(--color-gray-200);
  }
  .info-table dt { color: var(--color-gray-500); font-weight: var(--fw-semibold); }
  .info-table dd { margin: 0; color: var(--color-gray-900); }

  .image-band { padding: var(--sp-8) 0; background: var(--color-white); }
  .image-band img { width: 100%; height: auto; border-radius: var(--radius-md); aspect-ratio: 2/1; object-fit: cover; }

  .partners-note { font-size: var(--fs-sm); color: var(--color-gray-700); margin-bottom: var(--sp-6); }
  .partners-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--sp-4); }
  .partners-list li {
    background: var(--color-gray-100);
    padding: var(--sp-4);
    border-radius: var(--radius-md);
    text-align: center;
    font-weight: var(--fw-semibold);
    color: var(--color-navy-900);
  }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .info-table > div { grid-template-columns: 140px 1fr; }
    .message, .info, .partners { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 480px) {
    .container { padding: 0 var(--gutter-mobile); }
    .info-table > div { grid-template-columns: 1fr; gap: var(--sp-1); }
    .partners-list { grid-template-columns: 1fr; }
    .message, .info, .partners { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 2: コミット**

```bash
git add astro/src/pages/company.astro
git commit -m "feat(astro): company を実値・新ポジションに書き換え (issue#8)"
```

---

## Task 8: service / works / contact のページHero更新 + 新規 recruit.astro

**Files:**
- Modify: `astro/src/pages/service.astro`
- Modify: `astro/src/pages/works.astro`
- Modify: `astro/src/pages/contact.astro`
- Create: `astro/src/pages/recruit.astro`

- [ ] **Step 1: service.astro の Hero を新ポジションに**

`<h1>` を「3工法を、一社で。」のままに維持し、サブ文を「高速道路リニューアル工事の中核を、舗装・WJ・コンクリート補修の3工法で支えます。」に。本文部分（3工法 anchor sections）は既存のまま維持。

- [ ] **Step 2: works.astro の Hero 更新**

`<h1>` を「全国の高速道路リニューアルを、九州から支える。」、sub を「舗装・ウォータージェット・コンクリート補修の3工法で、高速道路の補修施工を担ってきました。」に。本体（フィルタ + WorkCard グリッド）は維持。

- [ ] **Step 3: contact.astro の本文は維持、heading 微調整**

`<h1>` 「見積依頼・現地調査のご相談」のまま、sub を「ご相談・お問い合わせには、原則翌営業日に一次回答いたします。」に。本体は維持。

- [ ] **Step 4: recruit.astro 新規作成**

```astro
---
import Base from '../layouts/Base.astro';
import SectionTitle from '../components/SectionTitle.astro';
import CtaBand from '../components/CtaBand.astro';
---
<Base
  title="採用情報 | 株式会社サンユウテック"
  description="株式会社サンユウテックの採用情報。高速道路リニューアル工事の専門会社で、現場・技術職を中心に募集しています。"
  currentPage="recruit"
  ogImage="/images/about-image.jpg"
>
  <section class="page-hero">
    <div class="container">
      <p class="brand-sig">RENEW, NOT REBUILD.</p>
      <h1>現場で、技術で、長く続ける。</h1>
      <p class="lead">高速道路リニューアル工事の専門会社で、一緒に働く仲間を募集しています。</p>
    </div>
  </section>

  <section class="why">
    <div class="container narrow">
      <SectionTitle text="当社で働く理由" />
      <ul class="reasons">
        <li><strong>技術が積み上がる。</strong>床版はつりから舗装復旧まで、現場で工程を通しで経験できる希少な環境です。</li>
        <li><strong>続けられる体制。</strong>無事故・無災害5年以上、社員50名のうち現場・技術45名。継続して長く働ける現場主義の体制です。</li>
        <li><strong>九州から全国へ。</strong>主戦場は九州、全国の高速道路リニューアル案件にも出張で携われます。</li>
      </ul>
    </div>
  </section>

  <section class="positions">
    <div class="container narrow">
      <SectionTitle text="募集職種" />
      <p class="note">具体的な募集要項は、お問い合わせいただいた方に個別にご案内します。</p>
      <ul class="job-list">
        <li>
          <h3>現場作業員（舗装／ウォータージェット／コンクリート補修）</h3>
          <p>未経験から始めて、3工法の現場経験を積めます。</p>
        </li>
        <li>
          <h3>施工管理</h3>
          <p>1級・2級土木施工管理技士の有資格者、または資格取得意欲のある方。複数現場を任せられる体制を一緒に作ります。</p>
        </li>
        <li>
          <h3>技術系総合職</h3>
          <p>大学・専門学校で土木・建築系を学ばれた方。現場経験を経て、施工管理・技術提案まで広く担っていただきます。</p>
        </li>
      </ul>
    </div>
  </section>

  <CtaBand heading="採用に関するお問い合わせはこちら。" />
</Base>

<style>
  .page-hero { background: var(--color-navy-900); color: var(--color-white); padding: var(--section-py-tablet) 0; }
  .container { max-width: var(--wrapper-max); margin: 0 auto; padding: 0 var(--gutter-desktop); }
  .container.narrow { max-width: var(--content-max); }
  .brand-sig {
    font-size: var(--fs-sm);
    letter-spacing: 0.18em;
    color: var(--color-orange-600);
    font-weight: var(--fw-bold);
    margin: 0 0 var(--sp-3);
  }
  .page-hero h1 { color: var(--color-white); font-size: var(--fs-4xl); font-weight: var(--fw-extrabold); margin-bottom: var(--sp-4); }
  .page-hero .lead { font-size: var(--fs-lg); color: var(--color-gray-200); }

  .why, .positions { padding: var(--section-py-desktop) 0; }
  .why { background: var(--color-white); }
  .positions { background: var(--color-gray-100); }

  .reasons { display: flex; flex-direction: column; gap: var(--sp-4); }
  .reasons li {
    background: var(--color-gray-100);
    border-left: 4px solid var(--color-orange-600);
    padding: var(--sp-4) var(--sp-6);
    font-size: var(--fs-base);
    line-height: var(--lh-relaxed);
    color: var(--color-gray-900);
  }
  .reasons strong { color: var(--color-navy-900); }

  .note { font-size: var(--fs-sm); color: var(--color-gray-700); margin-bottom: var(--sp-6); }
  .job-list { display: flex; flex-direction: column; gap: var(--sp-4); }
  .job-list li {
    background: var(--color-white);
    padding: var(--sp-6);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
  }
  .job-list h3 { font-size: var(--fs-lg); color: var(--color-navy-900); margin-bottom: var(--sp-2); }
  .job-list p { font-size: var(--fs-sm); color: var(--color-gray-700); }

  @media (max-width: 1023px) {
    .container { padding: 0 var(--gutter-tablet); }
    .page-hero, .why, .positions { padding: var(--section-py-tablet) 0; }
  }
  @media (max-width: 480px) {
    .container { padding: 0 var(--gutter-mobile); }
    .page-hero h1 { font-size: var(--fs-3xl); }
    .why, .positions { padding: var(--section-py-mobile) 0; }
  }
</style>
```

- [ ] **Step 5: ビルド + コミット**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build
cd ..
git add astro/src/pages/
git commit -m "feat(astro): service/works/contact ヘッダー刷新 + recruit新設 (issue#8)"
```

---

## Task 9: トーン書き換え + QA + コミット

**Files:**
- Modify: 全 Markdown / Astro ファイルで「弊社」を「当社」に置換

- [ ] **Step 1: 全ファイルで 弊社→当社 を置換**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website
grep -rl '弊社' astro/src projects/sunyutech-renewal --include='*.astro' --include='*.md' 2>/dev/null | xargs sed -i '' 's/弊社/当社/g' 2>/dev/null
grep -rn '弊社' astro/src projects/sunyutech-renewal --include='*.astro' --include='*.md' 2>/dev/null && echo "still present" || echo "PASS"
```

- [ ] **Step 2: QA 自動検査**

```bash
cd /Users/watanabeyuki/workspace/sunyutech-website/astro
npm run build

cd /Users/watanabeyuki/workspace/sunyutech-website
grep -rnE '#[0-9a-fA-F]{6}' astro/src --include='*.astro' --include='*.css' | grep -v 'tokens.css' && echo "FAIL: raw color" || echo "PASS"
grep -rhE '<img[^>]*>' astro/src --include='*.astro' | grep -vE 'alt=' && echo "FAIL: img alt" || echo "PASS"
grep -rhE 'style="' astro/src --include='*.astro' && echo "FAIL: inline" || echo "PASS"
ls astro/dist/recruit/index.html astro/dist/sitemap*.xml
```

- [ ] **Step 3: qa/checklist.md 更新**

```bash
echo "
## Phase 6 ブランドリフレッシュ追記

実行日: 2026-05-29

- [x] BrandSignature「RENEW, NOT REBUILD.」全ページ反映
- [x] Hero 全面書き換え（高速道路リニューアル専門会社ポジション）
- [x] 強み3カード差し替え（一社完結 / 指名継続 / 自社施工）
- [x] 会社情報 実値化（設立2012 / 経審P732 / 大野城市御笠川 / 社員50名）
- [x] 取引先表示を「ほか」表記に統一
- [x] ProcessFlow 視覚化（4工程）
- [x] StatGrid 数値表示（732 / 5+ / 80% / 50 / 10+ / 2）
- [x] ScrollReveal Island 追加
- [x] recruit ページ新規追加
- [x] 弊社 → 当社 統一
" >> projects/sunyutech-renewal/qa/checklist.md
```

- [ ] **Step 4: コミット + push**

```bash
git add astro/src projects/sunyutech-renewal/qa/checklist.md
git commit -m "refactor: 一人称を当社に統一 + Phase 6 QA結果追記 (issue#8)"
git push origin feature/8-website-renewal-astro
```

---

**Last Updated**: 2026-05-29
