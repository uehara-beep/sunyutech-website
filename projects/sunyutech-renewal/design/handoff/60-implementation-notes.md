# 60 Implementation Notes

## Astro 構成
- バージョン: Astro 4.x 系（npm create astro@latest --template minimal）
- TypeScript: strict
- Tailwind: 不使用
- スタイル: バニラCSS + design tokens（src/styles/tokens.css に CSS変数として展開）
- 画像: `public/images/` に既存 `images/*` をコピー（git mv ではなくコピー）。Astro Image Component は使わず純粋 `<img>`（共用サーバーアップロード前提のため）
- 統合（integrations）: @astrojs/sitemap のみ

## ディレクトリ
```
astro/
├── astro.config.mjs       # site: 'https://sunyutech.jp/', integrations: [sitemap()]
├── package.json
├── tsconfig.json          # extends 'astro/tsconfigs/strict'
├── public/
│   ├── images/            # 既存 images/* をコピー
│   ├── favicon.ico
│   ├── apple-touch-icon.png
│   └── robots.txt         # 静的配置
├── src/
│   ├── pages/
│   │   ├── index.astro
│   │   ├── service.astro
│   │   ├── works.astro
│   │   ├── company.astro
│   │   └── contact.astro
│   ├── layouts/
│   │   └── Base.astro
│   ├── components/
│   │   ├── Header.astro
│   │   ├── Footer.astro
│   │   ├── Hero.astro
│   │   ├── SectionTitle.astro
│   │   ├── Button.astro
│   │   ├── FeatureCard.astro
│   │   ├── WorkCard.astro
│   │   ├── Form.astro
│   │   ├── CtaBand.astro
│   │   └── MobileNav.astro      # Astro Island (client:visible)
│   ├── content/
│   │   ├── config.ts            # Content Collections schema
│   │   └── works/
│   │       ├── 01-paving-fukuoka.md
│   │       ├── 02-paving-onojo.md
│   │       └── ...
│   └── styles/
│       ├── tokens.css           # 20-design-tokens.yaml を CSS 変数化
│       └── global.css           # reset + body base + utility minimal
```

## 既存画像ファイル → 使用先マッピング

| ファイル | 使用先 |
|---|---|
| `hero-1.jpg` | index.astro Hero 右ペイン |
| `hero-2.jpg`〜`hero-8.jpg` | service / works / company / contact の Hero 簡略 or セクション挿絵に分散 |
| `service-paving.jpg` | service.astro #paving、index.astro 事業セクション Col1 |
| `service-waterjet.jpg` | service.astro #waterjet、index.astro Col2 |
| `service-concrete.jpg` | service.astro #concrete、index.astro Col3 |
| `work-paving01.jpg`〜`work-paving04.jpg` | works.astro、service.astro #paving 補足 |
| `work-waterjet01.jpg`〜`work-waterjet03.jpg` | works.astro、service.astro #waterjet 補足 |
| `work-concrete01.jpg` | works.astro、service.astro #concrete 補足 |
| `about-image.jpg` | company.astro 中段 |
| `logo.png` | Header（全ページ） |
| `favicon-*.{png,ico}` | public/ 直下にコピー |

## コーディング規約

### Astro コンポーネント
- 全コンポーネント `<style>` はスコープド（`<style>` のみ、`is:global` は禁止）
- `<style>` 内では `var(--token-name)` 経由のみ。生カラー禁止
- props は TypeScript interface で型定義
- 子要素 `<slot />` は積極的に使う

### CSS
- `tokens.css` は `:root { --color-navy-900: #0B2545; ... }` の形で全 token 展開
- `global.css` は reset + body base + `.visually-hidden` のみ、ユーティリティクラスは作らない
- メディアクエリ: `@media (max-width: 480px)` / `@media (min-width: 481px) and (max-width: 1023px)` / `@media (min-width: 1024px)`
- font-family は body で1回宣言、コンポーネント側で再宣言禁止

### 画像
- `<img src="/images/hero-1.jpg" alt="..." width="..." height="..." loading="lazy">` を基本
- hero の最初の `<img>` のみ `loading="eager" fetchpriority="high"`
- width/height は実寸を指定（CLS 防止）

### JS
- 静的サイトとして書く。`<script>` は MobileNav の hamburger toggle 1箇所のみ
- Astro Island: `<MobileNav client:visible />` で hydrate

## Astro Content Collection（works）

`src/content/config.ts`:
```typescript
import { defineCollection, z } from 'astro:content';

const works = defineCollection({
  type: 'data',
  schema: z.object({
    title: z.string(),
    method: z.enum(['舗装', 'WJ', 'コンクリート補修']),
    location: z.string(),
    scale: z.string(),
    client: z.string().optional(),
    image: z.string(),
    imageAlt: z.string(),
    order: z.number(),
  }),
});

export const collections = { works };
```

各 work は `src/content/works/01-*.json` 形式（type: 'data' なので .json）。Markdown ではなく data collection。

## ホスティング考慮

- お名前.com 共用レンタル前提
- 出力先: `astro/dist/`、FTP アップロード時はこのディレクトリの中身を public_html へ
- `astro.config.mjs` で `site: 'https://sunyutech.jp/'` を設定（OGP / sitemap 用）
- `base` はデフォルト `/` のまま
