# サンユウテック株式会社 コーポレートサイト

サンユウテック株式会社のコーポレートサイトです。舗装工事・ウォータージェット工事・コンクリート補修工事の事業内容や会社情報を掲載しています。

**URL**: https://sunyutech.jp/

---

## 技術スタック

| 技術       | 用途             |
| ---------- | ---------------- |
| HTML5      | ページ構造       |
| CSS3       | スタイリング     |
| JavaScript | インタラクション |
| Google Fonts | Noto Sans JP   |

- フレームワーク不使用の静的HTMLサイト
- CSS変数によるテーマカラー管理
- バニラJSによるアニメーション・フォームバリデーション

---

## ディレクトリ構成

```
sunyutech-website/
├── index.html          # トップページ
├── company.html        # 会社概要
├── service.html        # 事業内容
├── works.html          # 施工実績
├── contact.html        # お問い合わせ
├── css/
│   └── style.css       # メインスタイルシート
├── js/
│   └── main.js         # メインJavaScript
├── images/
│   ├── logo.png        # 会社ロゴ
│   ├── hero-*.jpg      # ヒーロースライダー画像
│   ├── service-*.jpg   # 事業内容画像
│   ├── work-*.jpg      # 施工実績画像
│   └── about-image.jpg # 会社概要画像
├── CONTRIBUTING.md     # 開発ガイド
└── README.md           # このファイル
```

---

## ローカル開発

静的HTMLサイトのため、ビルドツールは不要です。

### ブラウザで直接開く

```bash
open index.html
```

### ローカルサーバーで確認（推奨）

```bash
# Python
python3 -m http.server 8000

# Node.js (npx)
npx serve .
```

`http://localhost:8000` でアクセスできます。

---

## デザイン仕様

### カラーパレット

| 変数名                 | 色コード   | 用途             |
| ---------------------- | ---------- | ---------------- |
| `--primary-orange`     | `#E67E22`  | メインカラー     |
| `--primary-orange-dark`| `#D35400`  | ホバー・アクセント |
| `--primary-orange-light`| `#F39C12` | ハイライト       |
| `--white`              | `#FFFFFF`  | 背景             |
| `--text-color`         | `#333333`  | 本文テキスト     |

### レスポンシブブレークポイント

| ブレークポイント | 対象デバイス |
| ---------------- | ------------ |
| `768px`          | タブレット   |
| `480px`          | スマートフォン |

---

## ページ構成

| ページ         | ファイル       | 内容                       |
| -------------- | -------------- | -------------------------- |
| トップ         | `index.html`   | ヒーロー、事業概要、強み   |
| 会社概要       | `company.html` | 会社情報、代表挨拶、沿革   |
| 事業内容       | `service.html` | 舗装・WJ・コンクリート補修 |
| 施工実績       | `works.html`   | 施工事例の紹介             |
| お問い合わせ   | `contact.html` | 問い合わせフォーム         |

---

## 開発ガイド

開発規約・Issue作成・コミット規約・PRフローについては [CONTRIBUTING.md](./CONTRIBUTING.md) を参照してください。

---

**Last Updated**: 2026-03-02
