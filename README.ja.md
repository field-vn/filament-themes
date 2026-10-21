# Filament Themes

[English](README.md) | [Tiếng Việt](README.vi.md) | **日本語**

[Filament 5](https://filamentphp.com) のパネルを、たった 1 行で **Tabler**、**shadcn/ui**、**Ant Design** の見た目に
変更できます。ビルド不要、Vite の設定変更不要、カスタムテーマも不要です。

開発・メンテナンス: [field.vn](https://field.vn/)

```php
->plugin(FilamentThemes::make()->preset(Preset::Shadcn))
```

## 目次

- [特長](#特長)
- [プリセット](#プリセット)
- [動作要件](#動作要件)
- [インストール](#インストール)
- [使い方](#使い方)
- [カラー](#カラー)
- [アイコン](#アイコン)
- [ロゴ](#ロゴ)
- [CSS 変数によるカスタマイズ](#css-変数によるカスタマイズ)
- [コンポーネント一覧](#コンポーネント一覧)
- [仕組み](#仕組み)
- [Filament のアップグレード](#filament-のアップグレード)
- [制限事項](#制限事項)
- [開発](#開発)
- [ライセンス](#ライセンス)

## 特長

- **3 つのデザインシステム:** Tabler、shadcn/ui、Ant Design。それぞれ独自のカラー、アイコン、フォント、レイアウトを持ちます。
- **導入が簡単:** パッケージをインストールしてプラグインを登録するだけ。ビルドやコンパイルは不要です。
- **軽量:** Bootstrap、Radix、antd を同梱しません。CSS は Filament の `fi-*` クラスを上書きするだけなので、
  JavaScript（Alpine/Livewire）には触れず、プロジェクト独自の `->viteTheme()` とも併用できます。
- **幅広い対応:** ライト/ダークモード、モバイル、縦型（サイドバー）と横型（トップナビゲーション）レイアウト。
- **設定を尊重:** パネル側で設定したカラー、フォント、レイアウトが常に優先されます。
- **プレビューページ:** よく使うコンポーネントを一覧できるページ（任意）で、プリセットをすぐに確認できます。

## プリセット

| プリセット | スタイル | デフォルトのレイアウト | アイコン | フォント |
|---|---|---|---|---|
| `Preset::Tabler`（デフォルト） | [Tabler](https://tabler.io): 細い枠線のフラットなカード、小さな大文字ラベル、色付きアラート、青 `#066fd1`、slate 背景 | 全高の白いサイドバー、トップバーなし | Tabler Icons | Inter |
| `Preset::Shadcn` | [shadcn/ui](https://ui.shadcn.com): ニュートラルな配色、黒いプライマリボタン（ダークモードで反転）、ピル型タブ、枠線付きバッジ | 薄いグレーのサイドバー、コンテンツは「inset」カード内 | Lucide | Geist |
| `Preset::Antd` | [Ant Design Pro](https://pro.ant.design): 青 `#1677ff`、高さ 32px のコンパクトなコントロール、タグ風バッジ | ダークサイドバー `#001529` + 白いヘッダー | Ant Design Icons | システムフォント |

テーブル、ボタン、モーダル、通知などのコンポーネントも、それぞれのデザインシステムに合わせてスタイリングされます。
詳細は [コンポーネント一覧](#コンポーネント一覧) を参照してください。

## 動作要件

- PHP 8.3 以上
- Laravel 12 または 13
- Filament 5

## インストール

**1. パッケージをインストール**

```bash
composer require field-vn/filament-themes:^0.1
```

**2.（任意）プリセットのアイコンセットをインストール**

`->icons()` を有効にする場合のみ必要です。3 つのアイコンセットは合計で約 40 MB あるため、自動ではインストールされません。
使用するプリセットのものだけをインストールしてください。

```bash
composer require secondnetwork/blade-tabler-icons   # Preset::Tabler
composer require mallardduck/blade-lucide-icons     # Preset::Shadcn
composer require codeat3/blade-ant-design-icons     # Preset::Antd
```

**3. CSS を公開**

```bash
php artisan filament:assets
```

スタイルシートが `public/css/field-vn/filament-themes/` にコピーされます。標準的な Filament プロジェクトでは
`post-autoload-dump` スクリプトで `filament:upgrade` が実行されるため、この手順は自動で行われます。そうでない場合は、
パッケージを更新するたびに手動で実行し、Docker イメージのビルドにも追加してください。

<details>
<summary>プライベートリポジトリからのインストール</summary>

パッケージが Packagist に未公開の場合（またはリポジトリがプライベートの場合）は、`composer require` の前に
プロジェクトの `composer.json` にリポジトリを追加します。

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/field-vn/filament-themes" }
]
```

プライベートリポジトリの場合、ビルド環境（Docker、CI）には GitHub トークンが必要です。例えば `COMPOSER_AUTH`
環境変数で渡します。

</details>

## 使い方

パネルプロバイダーでプラグインを登録します。

```php
use FieldVn\FilamentThemes\FilamentThemes;
use FieldVn\FilamentThemes\Preset;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->brandLogo(asset('images/logo.svg'))
        ->darkModeBrandLogo(asset('images/logo-white.svg')) // ダークモードとダークサイドバーで使用
        ->plugin(
            FilamentThemes::make()
                ->preset(Preset::Shadcn)
                ->icons()
                ->previewPage(! app()->isProduction()),
        );
}
```

### オプション

| メソッド | デフォルト | 説明 |
|---|---|---|
| `->preset(Preset::Tabler)` | `Tabler` | デザインシステム: `Preset::Tabler`、`Preset::Shadcn`、`Preset::Antd`。文字列（`'shadcn'`）も使えます。 |
| `->icons()` | オフ | Filament の Heroicons をプリセットのアイコンセットに置き換えます。アイコンパッケージが必要です（[アイコン](#アイコン) を参照）。 |
| `->horizontal()` | オフ | サイドバーの代わりに 2 段の白いヘッダー（トップナビゲーション）を使用します。 |
| `->darkSidebar()` | Antd ではオン | ダークサイドバー（縦型レイアウトのみ）。ブランドカラーを渡すこともできます: `->darkSidebar('#1D334F')`。`false` で無効化。 |
| `->alignModals(false)` | オン | デザインシステムではなく Filament 標準のモーダル配置を維持します。 |
| `->previewPage()` | オフ | 「Theme preview」ページを追加します。本番環境では無効にしてください。 |

### レイアウト

各プリセットは、プラグイン登録時に独自のレイアウトを設定します。

| | Tabler | shadcn | Ant Design |
|---|---|---|---|
| `topbar()` | オフ | オフ | オン |
| `sidebarWidth()` | Filament のデフォルト | 16rem | 16rem |

変更したい場合は、`->plugin()` の **後に** パネルで `->topbar()` または `->sidebarWidth()` を呼び出してください。

### プレビューページ

`->previewPage()` は `/{panel}/theme-preview` にページを追加し、よく使うコンポーネントをサンプルデータで表示します。

- 全カラー・全サイズのボタン、リンク、アイコンボタン、ツールチップ
- 統計カード、コールアウト、空の状態、タブ、インフォリスト
- ファイルアップロードを含む全種類のフィールドを持つフォーム
- 検索・並べ替え・フィルター・一括操作・ページネーション付きのテーブル（存在しない文字列で検索すると空の状態を確認できます）
- フォームモーダル、確認ダイアログ、ウィザード、スライドオーバー、ドロップダウン、通知

プリセットの比較や、Filament アップグレード後の確認に使えます。

> [!NOTE]
> コンポーネントをキャッシュしている場合（`php artisan filament:optimize` または `filament:cache-components`）、
> そのコマンドを再実行するまでこのページのルートは存在しません。

### モーダル

デフォルトでは、モーダルは使用中のデザインシステムに従います（`Preset::modalAlignment()`）。

- フッターのボタンは右寄せで、プライマリボタンが一番外側に配置されます。
- 中央寄せのモーダル（確認ダイアログと Tabler の幅の狭いモーダル）では、ボタンが横幅を均等に分け合います。
- shadcn と Ant Design では、モーダルのタイトルが左寄せになります。

独自に `->modalAlignment()` や `->modalFooterActionsAlignment()` を設定したアクションはその設定が維持されます。
完全に無効にするには `->alignModals(false)` を使います。

すべてのアクションに独自のデフォルトを設定したい場合は、`Action::configureUsing(..., isImportant: true)` を使ってください。
`isImportant` がないと、プロジェクトの設定はプラグインより先に実行されるため上書きされます。

## カラー

`->colors()` を呼ばない場合、プラグインはプリセットのパレットを使用します。プラグインはパネルで **未設定** の
カラーだけを補うため、`->plugin()` の前後どちらで呼んでも、独自の `->colors()` が常に優先されます。

| プリセット | パレット |
|---|---|
| Tabler | primary `#066fd1`、danger `#d63939`、warning `#f59f00`、success `#2fb344`、info `#4299e1`、gray = slate |
| shadcn | primary = neutral スケール、シェード 600 がほぼ黒の `oklch(0.205 0 0)`。gray = neutral。ダークモードではプライマリボタンが明るいグレー＋黒文字に反転します。 |
| Ant Design | `@ant-design/colors` 公式の 10 段階パレット（blue、red、gold、green）。トークンカラーはシェード 600、ホバーカラーは 500。gray = antd の neutral。primary と danger ボタンは白文字のままです。 |

> [!WARNING]
> Ant Design の `#1677ff` は白背景でコントラスト比 3.97:1 となり、Filament が適用する 4.5:1 を下回ります。
> WCAG AA が必要な場合は `'primary' => Palette::anchored('#0958d9', 600)` を使用してください。

### ブランドカラーを正確に使う

Filament の `Color::hex()` は色相（hue）しか保持しないため、ブランドカラーが指定どおりに表示されることはありません。
代わりに `Palette` を使います。

```php
use FieldVn\FilamentThemes\Support\Palette;

->colors([
    // この色をそのままシェード 600（ボタンで使用）に置いたスケールを生成
    'primary' => Palette::anchored('#0054a6', 600),

    // または 11 段階（50〜950）のスケールをそのまま指定
    'info' => Palette::fromShades([50 => '#e6f4ff', /* … */ 950 => '#001d66']),
])
```

## アイコン

`->icons()` は Filament のすべてのアイコンエイリアスを、プリセットのアイコンセットに置き換えます
（対応表は `src/Presets/*/Icons.php`）。

| プリセット | アイコンセット | パッケージ | 独自アイコンのプレフィックス |
|---|---|---|---|
| Tabler | Tabler Icons | `secondnetwork/blade-tabler-icons` | `tabler-…` |
| shadcn | Lucide | `mallardduck/blade-lucide-icons` | `lucide-…` |
| Ant Design | Ant Design Icons | `codeat3/blade-ant-design-icons` | `antdesign-…-o` |

- アイコンパッケージは推奨（`suggest`）であり、必須ではありません。必要なパッケージなしで `->icons()` を有効にすると、
  実行すべき `composer require` コマンドを含むエラーが表示されます。
- 独自に設定したアイコン（リソースのナビゲーションアイコンなど）はそのまま維持されます。プリセットに合わせて上記の
  プレフィックスを使用してください。
- Ant Design には太陽/月のアイコンがないため、テーマ切り替えボタンは Filament の Heroicons のままです。

本番環境では `php artisan icons:cache`（または `filament:optimize`）を実行し、リクエストごとにアイコンセットが
スキャンされないようにしてください。

## ロゴ

- 明るいサイドバーと横型ヘッダーでは `->brandLogo()` を使用します。
- ダークサイドバー（`->darkSidebar()`、または Ant Design のデフォルト）では `->darkModeBrandLogo()` を使用します。
  ロゴが暗い文字の画像の場合は、`->darkModeBrandLogo()` で明るいバージョンを追加してください。
- テキストロゴ（ブランド名）は自動で白になります。

## CSS 変数によるカスタマイズ

すべての値は、プリセットが設定する `--ft-*` CSS 変数（ライトモード、`.dark`、`html[data-ft-sidebar="dark"]`）から
取得されます。独自の Vite テーマ、またはプラグインの **後に** 登録した `STYLES_AFTER` レンダーフックで上書きできます。

```css
:root {
    --ft-sidebar-accent: #d4af37; /* 選択中の項目のマーカー（Tabler のダークサイドバー） */
    --ft-table-cell-py: 0.5rem;   /* テーブルの行にゆとりを持たせる */
}
```

| グループ | 変数 |
|---|---|
| 背景・枠線・影 | `--ft-body-bg`、`--ft-surface`、`--ft-surface-secondary`、`--ft-border`、`--ft-border-input`、`--ft-shadow-card/input/overlay` |
| フォーカス | `--ft-focus-border`、`--ft-focus-ring`、`--ft-focus-width` |
| タイポグラフィ | `--ft-font-family`、`--ft-heading-size/weight/…`、`--ft-label-size/weight/transform/…`（小さなラベル: メニューグループ、パンくず、統計） |
| 余白 | `--ft-topbar-height`、`--ft-page-gap`、`--ft-field-gap`、`--ft-card-padding`、`--ft-card-header-padding`、`--ft-table-cell-py` |
| テーブル | `--ft-table-head-bg/color/size/weight/transform`、`--ft-table-row-hover` |
| コントロール | `--ft-btn-padding/weight/line-height`、`--ft-switch-width/height/knob/shift/off-bg`、`--ft-upload-bg` |
| オーバーレイ | `--ft-overlay`（モーダルの背景）、`--ft-tooltip-bg/text/size/padding/radius` |
| サイドバー | `--ft-sidebar-bg/text/text-muted/text-active/hover-bg/active-bg/divider/accent`、`--ft-nav-item-padding/radius` |
| 横型ヘッダー | `--ft-navbar-height`。`--ft-container` は `->maxContentWidth()` に従います |
| 角丸 | `--radius-sm/md/lg/xl`（Tabler 4/4/6/8px、shadcn は `--radius: 0.625rem` から算出、Antd 4/4/6/8px） |

## コンポーネント一覧

プリセットごとの各コンポーネントのスタイルです。

### テーブル

| Tabler | shadcn（`examples/tasks` 準拠） | Ant Design（ProTable 準拠） |
|---|---|---|
| フラットなカード内に配置。列見出しは薄いグレー背景に 12px/500 の大文字。セルの文字は濃いグレー。行アクションはアイコンなしのテキストリンク。 | カードなし、角丸 8px の枠線のみ。左上に高さ 32px の検索欄、点線枠のフィルターボタン。列見出しは背景なし、行は低め、ページネーションは下部。 | 枠線なしの白いカード。列見出しは `#fafafa` で区切り線あり、セルのパディング 16px。選択バーは青い Alert。行アクションは区切り線で分けたリンク（"View \| Edit"）。 |

### その他のコンポーネント

| コンポーネント | Tabler | shadcn | Ant Design |
|---|---|---|---|
| ボタン・入力欄 | 高さ 40px（sm 32、lg 48、xl 56）。色付きボタンは暗い枠線と上部ハイライト | 高さ 36px、px-4（sm 32、lg 40） | 高さ 32px（sm 24、lg 40）、文字の太さ 400。白いボタンはホバーで青に |
| モーダル | 下線付き 56px ヘッダー、上線付きの薄いグレーのフッター、キャンセルは左端 | 角丸 10px、パディング 24px、タイトル 18px、ボタン間隔 8px | パディング 20px 24px、タイトル 16px、背景 45% |
| 確認ダイアログ | 上部に 2px のカラーバー、大きなアイコン、全幅のボタン | AlertDialog: タイトル横の淡色角丸スクエア内にアイコン、ボタンは右 | Modal.confirm: タイトル前に 22px のアイコン、本文はタイトルに揃えて字下げ、ボタンは右 |
| 通知 | 角丸 6px のトースト、柔らかい影 | Sonner: 文字 13px、アイコン 16px、全種類で同じ白いカード | Notification: タイトル 16px、パディング 20px 24px、枠線なし |
| ツールチップ | 90% の暗い背景、文字 12px | 黒背景、文字 12px、角丸 8px | 85% の黒背景、文字 14px |
| スイッチ | 32×20 | 32×18 | 44×22 |
| チェックボックス | 20px、角丸 6px | 16px | 16px |
| ドロップダウン | 全幅の項目、角丸なし | 項目 px-2 py-1.5、アイコン 16px | 項目 5px 12px。削除項目はホバーで赤に |
| 空の状態 | 大きなアイコン、タイトル 20px | 点線の枠、グレーのスクエア内にアイコン | 薄いグレーのアイコン、補助色の「No data」 |
| ToggleButtons | 選択時: 淡い色の背景に色付きの枠線と文字 | 選択時: グレーの `accent` 背景 | 選択時: 白背景に青い枠線と文字（Radio.Button） |
| ファイルアップロード | 点線の枠 | 点線の枠 | `#fafafa` 背景の点線の枠、角丸 8px（Upload.Dragger） |
| ウィザード | Filament のデフォルト | Filament のデフォルト | Steps: 32px の円、現在のステップは青、完了したステップは薄い青 |

## 仕組み

- **共通構造 + プリセットごとのトークン。** `resources/dist/core.css` には上書き対象の `fi-*` セレクターがあり、
  `--ft-*` 変数のみを参照します。各プリセットのファイル（`tabler.css`、`shadcn.css`、`antd.css`）はそれらの変数を定義し、
  独自のルールをいくつか追加します（shadcn の inset カード、antd の列区切り線など）。ページで読み込まれるのは
  `core.css` と使用中のプリセットのファイルだけです。
- **パネルテーマの後に読み込み。** CSS は `@layer components` に配置され、`STYLES_AFTER` レンダーフックで挿入されます。
  セレクターは Filament 本体の CSS からそのまま写しているため、Filament のスタイルを上書きしつつ、ユーティリティクラスは
  通常どおり優先されます。Filament が `fi-bg-color-*` ユーティリティで設定するボタンの色は `@layer utilities` で上書きします。
- **キャッシュに強いファイル名。** 公開ファイルは `?v=` クエリ文字列ではなく、ファイル名にコンテンツハッシュを含みます
  （`core-<hash>.css`）。クエリ文字列を無視する CDN（Cloudflare の「Ignore query string」など）では古い CSS が配信され続けるためです。
- **プロジェクトの設定を邪魔しない。** カラー、アイコン、フォントは、パネルが自身の設定を登録した後のプラグインの `boot()` で、
  パネルが未設定の項目にのみ適用されます。

## Filament のアップグレード

Filament がクラスやマークアップを変更した場合、影響を受けた部分は Filament 標準のスタイルに戻るだけで、ページが崩れることは
ありません。Filament をアップグレードするたびに:

1. このリポジトリで `composer test` を実行します（CI も push のたびに最新の Filament で実行します）。テストでは次を確認します。
   - 3 つのアイコンセットがすべての Filament エイリアスをカバーし、すべてのアイコンが存在すること
   - `core.css` で使われるすべての変数が 3 つのプリセットすべてで定義されていること
   - CSS がパネルテーマの後に読み込まれること
   - Tabler のプライマリボタンの色が正しいこと
   - プレビューページとそのモーダルが開けること
2. 2 つのバージョン間で `vendor/filament/*/resources/css` に `git diff` をかけ、`core.css` とプリセットのファイルの
   該当するセレクターを修正します。
3. 各プリセットで、ライト/ダークモード、縦型/横型レイアウト、モバイル表示のプレビューページを確認します。

## 制限事項

- Filament の HTML は変更しない（ビューを公開しない）ため、異なるマークアップが必要なデザインは実現できません。
  例: ログインページでカードの外にあるロゴ（Tabler）、inset カード内のページヘッダー（shadcn）。
- 折りたたみ式サイドバー（`sidebarCollapsibleOnDesktop`）に対応していますが、折りたたむとロゴがトップバーの白い背景上に
  表示されます（Ant Design、またはトップバーを有効にしている場合）。
- shadcn は Geist フォントを Bunny Fonts（`fonts.bunny.net`）から読み込みます。セルフホストする場合はパネルで `->font()` を設定してください。
- ツールチップは `.tippy-box[data-theme]` でスタイリングされるため、ページ内のすべての tippy.js ポップアップ
  （他のプラグインのもの、例えば tiptap エディターのメニューを含む）もダークスタイルになります。

## 開発

```bash
composer install   # `testbench package:discover` も実行されます
composer ci        # pint --test, phpstan (level 7), pest
composer format    # pint
```

テストは [Orchestra Testbench](https://packages.tools/testbench) 上で実行されます。`tests/Fixtures/AdminPanelProvider.php` は
`filament:install --panels` で生成されるものと同様の `/admin` パネルに、プラグイン（`->icons()->previewPage()`）を追加したものです。
すべてのプリセットをテストできるよう、3 つのアイコンセットはすべて `require-dev` に含まれています。CI は PHP 8.3/8.4/8.5 ×
Laravel 12/13 で最新の Filament を使って実行し、さらに `composer.json` が許容する最小の依存バージョンでも 1 回実行します。

リリース手順: `CHANGELOG.md` を更新し、`main` で `vX.Y.Z` タグを付けます。

## ライセンス

MIT。開発: [field.vn](https://field.vn/)

デザイン言語は Tabler、shadcn/ui、Ant Design（いずれも MIT）に基づいています。アイコンは Tabler Icons（MIT）、
Lucide（ISC）、Ant Design Icons（MIT）を使用しています。詳細は [`NOTICE`](NOTICE) を参照してください。
