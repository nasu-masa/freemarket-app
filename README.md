# freemarket.app "補完版" README

# ◎ coachtechフリマ

本アプリは、coachtech が提示する仕様書をもとに作成したフリマアプリです。
ユーザー登録、ログイン、商品一覧、商品詳細、出品、購入、プロフィール編集など、
フリマアプリとして必要な基本機能を実装しています。

# ◎ 🐳 開発環境構築

## ◆ リポジトリのクローン

bash

```jsx
git clone git@github.com:nasu-masa/freemarket-app.git
cd freemarket-app
```

## ◆ Docker ビルド & 起動

bash

```jsx
docker-compose up -d --build
```

## ◆ Laravel セットアップ

bash

```jsx
docker-compose exec php bash

composer install

cp .env.example .env  ※環境変数適宣変更

php artisan key:generate

php artisan migrate
php artisan db:seed

php artisan storage:link

# ここまでで「開発環境」が完成
# ------------------------------------
# ここから「テスト環境」を作る

cp .env .env.testing
php artisan key:generate --env=testing
```

※本アプリでは、商品購入時の決済に **Stripe** を使用しています。

### ◆ インストール

```jsx
composer require stripe/stripe-php
```

### ◆ Stripe の環境変数（.env）

```jsx
STRIPE_KEY = your_stripe_public_key;
STRIPE_SECRET = your_stripe_secret_key;
```

※ `.env.example` にもダミー値を記載しています。

## ◆ 開発環境 URL

| 機能                  | URL                       |
| --------------------- | ------------------------- |
| トップページ          | http://localhost/         |
| ユーザー登録          | http://localhost/register |
| phpMyAdmin            | http://localhost:8080/    |
| MailHog（メール確認） | http://localhost:8025/    |

# ◎ 🧩 使用技術（実行環境）

- **PHP 8.x**
- **Laravel 8**
- **MySQL 8.0.32**
- **nginx 1.21.1**
- **Docker / Docker Compose**
- **CSS**
- **Laravel Fortify（認証）**

# ◎ 🐳 docker-compose.yml（構成）

yaml

```jsx
version: '3.8'

services:
    nginx:
        image: nginx:1.21.1
        ports:
            - "80:80"
        volumes:
            - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
            - ./src:/var/www/
        depends_on:
            - php

    php:
        build: ./docker/php
        volumes:
            - ./src:/var/www/

    mysql:
        image: mysql:8.0.32
        environment:
            MYSQL_ROOT_PASSWORD: root
            MYSQL_DATABASE: laravel_db
            MYSQL_USER: laravel_user
            MYSQL_PASSWORD: laravel_pass
        command:
            mysqld --default-authentication-plugin=mysql_native_password
        volumes:
            - mysql-data:/var/lib/mysql
            - ./docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf

    mailhog:
        image: mailhog/mailhog
        ports:
            - "1025:1025"
            - "8025:8025"

    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        environment:
            - PMA_ARBITRARY=1
            - PMA_HOST=mysql
            - PMA_USER=laravel_user
            - PMA_PASSWORD=laravel_pass
        depends_on:
            - mysql
        ports:
            - 8080:80

volumes:
	mysql-data:
```

# ◎ 📁 ディレクトリ構造（主要部分のみ）

```jsx
src/
├── app/
│   ├── Actions/                # Fortify関連アクション
│   ├── Http/
│   │   ├── Controllers/        # 各種コントローラー
│   │   ├── Middleware/
│   │   └── Requests/           # バリデーション
│   ├── Models/                 # モデル
│   ├── Notifications/          # メール通知
│   ├── Providers/              # FortifyServiceProvider など
│   └── Services/               # ビジネスロジック
│
├── config/                     # fortify.php / category.php など
│
├── database/
│   ├── migrations/             # テーブル定義
│   ├── seeders/                # 初期データ
│   └── factories/              # テスト用データ
│
├── public/
│   ├── assets/
│   ├── css/
│   ├── js/
│   └── products/               # 商品画像
│
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/
│       ├── items/
│       ├── mypage/
│       ├── purchase/
│       └── emails/
│
├── routes/
│   └── web.php
│
└── tests/
    └── Feature/                # 機能テスト
```

# ◎ 画面遷移 × コントローラー 一覧

| 画面名称               | パス                          | メソッド | コントローラー       | アクション   | 認証 | 説明                                                 |
| ---------------------- | ----------------------------- | -------- | -------------------- | ------------ | ---- | ---------------------------------------------------- |
| 商品一覧（トップ）     | `/`                           | GET      | ItemController       | index        | 不要 | 商品一覧表示                                         |
| 商品一覧（マイリスト） | `/?tab=mylist`                | GET      | ItemController       | index        | 必須 | マイリストタブ表示                                   |
| 会員登録画面           | `/register`                   | GET      | RegisterController   | show         | 不要 | 新規登録フォーム表示（処理は Fortify が担当）        |
| 会員登録処理           | `/register`                   | POST     | Fortify（内部処理）  | create       | 不要 | 新規登録処理（Controller ではなく Fortify が実行）   |
| ログイン画面           | `/login`                      | GET      | LoginController      | show         | 不要 | ログインフォーム表示（処理は Fortify が担当）        |
| ログイン処理           | `/login`                      | POST     | Fortify（内部処理）  | authenticate | 不要 | ログイン処理（Controller ではなく Fortify が実行）   |
| ログアウト             | `/logout`                     | POST     | Fortify（内部処理）  | logout       | 必須 | ログアウト処理（Controller ではなく Fortify が実行） |
| 商品詳細               | `/item/{item_id}`             | GET      | ItemController       | show         | 不要 | 商品詳細                                             |
| いいね追加             | `/item/{item_id}/like`        | POST     | MyListItemController | store        | 必須 | マイリスト追加                                       |
| コメント投稿           | `/item/{item_id}/comments`    | POST     | CommentController    | store        | 必須 | コメント送信                                         |
| 購入確認               | `/purchase/{item_id}`         | GET      | PurchaseController   | create       | 必須 | 購入確認画面                                         |
| 購入処理               | `/purchase/{item_id}`         | POST     | PurchaseController   | store        | 必須 | 購入処理                                             |
| 住所変更               | `/purchase/address/{item_id}` | GET      | AddressController    | editAddress  | 必須 | 購入時の住所変更                                     |
| 住所変更処理           | `/purchase/address/{item_id}` | PUT      | AddressController    | updateAdress | 必須 | 住所変更処理                                         |
| 出品画面               | `/sell`                       | GET      | ItemController       | create       | 必須 | 出品フォーム                                         |
| 出品処理               | `/sell`                       | POST     | ItemController       | store        | 必須 | 出品処理                                             |
| マイページ             | `/mypage`                     | GET      | ProfileController    | index        | 必須 | マイページ                                           |
| プロフィール編集       | `/mypage/profile`             | GET      | ProfileController    | edit         | 必須 | プロフィール編集                                     |
| プロフィール更新       | `/mypage/profile`             | PUT      | ProfileController    | update       | 必須 | プロフィール更新                                     |
| 購入履歴タブ           | `/mypage?page=buy`            | GET      | ProfileController    | index        | 必須 | 購入履歴                                             |
| 出品履歴タブ           | `/mypage?page=sell`           | GET      | ProfileController    | index        | 必須 | 出品履歴                                             |

# ◎ モデル一覧（Model）

| モデルファイル名 | 説明                                                                                 |
| ---------------- | ------------------------------------------------------------------------------------ |
| User.php         | Laravel標準構成に基づき作成                                                          |
| Address.php      | ユーザーの住所情報（郵便番号・都道府県・市区町村・番地・建物名）を管理               |
| Category.php     | 商品カテゴリを管理                                                                   |
| Comment.php      | 商品へのコメントを管理                                                               |
| Item.php         | 出品された商品データ（タイトル・説明・価格・カテゴリ・ブランド・状態・出品者）を管理 |
| ItemImage.php    | 商品画像を管理する中間モデル                                                         |
| MylistItem.php   | ユーザーのお気に入り（マイリスト）を管理する中間モデル                               |
| Purchase.php     | 購入情報（購入者・商品・購入日時・金額）を管理                                       |

# ◎ ビュー一覧（Bladeファイル）

| 画面名称                         | Bladeファイル名                 |
| -------------------------------- | ------------------------------- |
| 商品一覧画面（トップ画面）       | items/index.blade.php           |
| 会員登録画面                     | auth/register.blade.php         |
| ログイン画面                     | auth/login.blade.php            |
| 商品詳細画面                     | items/show.blade.php            |
| 商品購入画面                     | purchase/create.blade.php       |
| 送付先住所変更画面               | purchase/address_edit.blade.php |
| 商品出品画面                     | items/create.blade.php          |
| プロフィール画面                 | mypage/index.blade.php          |
| プロフィール編集画面（設定画面） | mypage/profile_edit.blade.php   |
| メール認証誘導画面               | auth/verify_email.blade.php     |

# ◎ CSS ファイル一覧

| ファイル名         | 説明                                                 |
| ------------------ | ---------------------------------------------------- |
| common.css         | 全ページ共通のレイアウト・基本スタイル               |
| components.css     | ボタン・カード・フォームなどの共通 UI コンポーネント |
| sanitize.css       | ブラウザ差異を吸収するリセット CSS                   |
| utility.css        | 余白などユーティリティクラス                         |
| pages/auth.css     | 会員登録・ログイン画面の個別スタイル                 |
| pages/email.css    | メール認証関連画面のスタイル                         |
| pages/items.css    | 商品一覧・商品詳細ページのスタイル                   |
| pages/purchase.css | 購入画面・住所変更画面のスタイル                     |

# ◎ JavaScript ファイル一覧

| ファイル名               | 説明                                                   |
| ------------------------ | ------------------------------------------------------ |
| address-sync.js          | 住所 UI の値を hidden フィールドへ同期する処理         |
| flash.js                 | フラッシュメッセージの自動フェードアウトなどの表示制御 |
| image-preview.js         | 出品時の画像プレビュー表示                             |
| jump-scroll.js           | ページ内の特定位置へスムーズスクロールする処理         |
| payment-method-select.js | 支払い方法の選択 UI 制御                               |
| price-input-format.js    | 価格入力欄のフォーマット（カンマ付与など）             |
| select-ui-control.js     | セレクトボックスの UI 制御                             |

# ◎ 🧩 主な機能一覧（仕様書 US001〜US009 に準拠）

## ◆ 認証（US001〜US003）

- 会員登録（メール認証あり）
- ログイン / ログアウト
- 初回プロフィール設定
- 認証メール再送
- 未認証ユーザーのアクセス制御

## ◆ 商品一覧（US004）

- 全商品の一覧表示
- 購入済み商品の「Sold」表示
- 自分の出品商品を非表示
- いいね一覧（マイリスト）
- 商品名の部分一致検索

## ◆ 商品詳細（US005）

- 商品情報の表示（画像・名前・ブランド・価格・カテゴリ・状態）
- コメント一覧表示
- コメント投稿（バリデーションあり）
- いいね登録 / 解除

## ◆ 商品購入（US006）

- 購入前情報の表示（商品・価格・住所）
- 支払い方法選択（コンビニ / カード）
- Stripe 決済画面への遷移
- 購入後の「Sold」反映
- 配送先住所の変更

## ◆ プロフィール（US007〜US008）

- プロフィール表示（画像・名前・出品一覧・購入一覧）
- プロフィール編集（画像・住所・ユーザー名）

## ◆ 商品出品（US009）

- 商品情報の登録（カテゴリ複数選択・状態・名前・ブランド・説明・価格）
- 商品画像アップロード（storage 保存）

# ◎ 🗂 テーブル仕様書 & ER図

本アプリケーションは、coachtech が提示する仕様書（US001〜US009）に基づき

データベース設計を行っています。

以下に **ER図** と **テーブル仕様書** を掲載します。

---

## ◆ ER図（Entity Relationship Diagram）

```jsx
![ER図](https://raw.githubusercontent.com/nasu-masa/freemarket-app/main/docs/er.png)
```

ER図では以下のエンティティを定義しています：

- users
- items
- item_images
- categories
- category_item（中間テーブル）
- comments
- purchases
- addresses
- my_list_items

---

## ◆ テーブル仕様書

```jsx
![テーブル仕様書](https://raw.githubusercontent.com/nasu-masa/freemarket-app/main/docs/table_spec.png)
```

テーブル仕様書では以下の内容を定義しています：

- カラム名
- データ型
- NULL 許可
- デフォルト値
- 外部キー制約
- カーディナリティ（1対多、多対多 など）

本アプリのマイグレーションファイルは、

このテーブル仕様書と完全に一致するように実装しています。

# ◎ 💳 決済サービス（Stripe）

本アプリでは、商品購入時の決済に **Stripe** を使用しています。

## ◆ 決済フロー

- 商品詳細ページから「購入する」をクリック
- Stripe Checkout にリダイレクト
- 決済完了後、トップページへ遷移

# ◎ 💡 工夫した点

- Fortify を用いた認証機能のカスタマイズ
- メール認証導線の改善
- 商品画像の表示調整（object-fit: contain）
- UI の統一感を意識した Blade 構成
- 共通CSSの統合によるスタイル管理の最適化
- 検索フォームの UI 改善
  - 世界的に標準化されている「虫眼鏡アイコン」を採用

# ◎ 📝 補完した仕様

本案件の仕様書には、画面遷移・メッセージ文言・画像処理・メール内容など
UI/UX に関わる重要な仕様が一部欠落していたため、
一般的な Web アプリの慣習と UX の自然さに基づき、補完しています。

# ◎ 📄 ライセンス

このプロジェクトは学習目的で作成されています。