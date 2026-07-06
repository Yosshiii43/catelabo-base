# Catelabo Base — Claude Code 作業規約

このファイルはClaude Codeが毎セッション自動で読み込む。詳細は docs/テーマ開発指針.md（矛盾する場合は指針が正）。

## このリポジトリについて

キャッテリー専門WordPressテーマ。**1つのテーマをスキンCSSの切り替えだけで「まったく別のサイト」に見せる商品**。
データ層（子猫・親猫のCPT/フィールド）は別プラグイン catelabo-core が担当しており、このリポジトリでは扱わない。

## ビルド

```
sass assets/scss:assets/css --no-source-map
```

SCSSを変更したら必ず実行し、成功を確認してから作業完了とすること。

## 絶対規則（違反はレビューで差し戻し）

1. 色・書体・角丸・影・余白は必ず `var(--token)` 経由。**生HEX・font-family直書きは禁止**（例外は `foundation/_tokens.scss` と `skins/` のみ）
2. **トークンをSCSS変数に置き換えない**。CSSカスタムプロパティであることが実行時スキン切替の生命線
3. `@media` 直書き禁止。必ず `mq(sp / tab / pc)` を使う（sp=〜767 / tab=768〜 / pc=1024〜）
4. クラス契約（指針§5）の改名・削除禁止。テンプレートPHPのHTML構造は原則変更しない
5. スキンは `assets/scss/skins/skin-{name}.scss`。トークン再定義を起点に、**装飾（疑似要素・テクスチャ・区切り形状等）は自由に書いてよい（行数制限なし）**。ただし①HTML・契約クラス・テンプレートPHPに触れない ②レイアウト骨格（display/grid等）の変更はスキンでやらずバリアント（規則7）として実装 ③色・書体・角丸・影はトークン再定義で表現し、セレクタ上書きは「トークンで表現できない装飾」に限る
6. 新スキンは `functions.php` の `catelabo_base_skins()` に登録（label＋Google Fonts URL）
7. セクションバリアントの追加＝SCSSに1ブロック＋ `inc/theme-options.php` の登録関数に1項目。ヒーロー=`p-hero--{name}`/catelabo_hero_variants、子猫一覧=`.klist-{name}`/catelabo_kitten_list_variants、見出し=`hstyle-*`/catelabo_heading_styles。他セクションを増やすときも同方式で
8. 見出し系のクラスには `font-family: var(--font-heading)` を自前で宣言（h1〜h4の要素セレクタに依存しない）
9. 関数・mixin（rem / rclamp / mq / cq / bleed / arrow）は `@use "../foundation/functions" as *;` `@use "../foundation/mixins" as *;`（object配下は `../../foundation/…`）
10. 指示されていないファイルを変更しない。大量の一括変更をしない

## 品質基準

- 本文16px以上・行間1.8以上（読者は40〜60代中心）
- 文字と背景のコントラスト比4.5:1以上（スキンの色決定時に必ず確認し、計算結果を報告する）
- `:focus-visible` と `prefers-reduced-motion` を壊さない
- 出現モーションは要素に `c-reveal` を付ける方式に乗せる（独自のスクロール監視JSを追加しない）。強度は body の `motion-*` が制御する
- 画像は width/height 属性必須・4:3は object-fit: cover

## 作業の進め方

- 1セッション＝1スキン（または1バリアント）。tokens再定義 → ビルド → 確認 → 微調整の順で小さく進める
- 完了報告には必ず含める：変更ファイル一覧／ビルド結果／生HEXチェック結果（`grep -rn "#[0-9a-fA-F]\{3\}" assets/scss --include="*.scss" | grep -v _tokens | grep -v skins/` が0件）／人間がカスタマイザーで確認する手順
