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
5. スキンは `assets/scss/skins/skin-{name}.scss`。トークン再定義を起点に、装飾も**CSSによるレイアウトの再構成も自由**（grid組み替え・writing-mode・疑似要素タイポ等。行数制限なし。品質見本= skin-yuki）。境界は①HTML・契約クラス・テンプレートPHP・JSに触れない ②スマホ表示・:focus-visible・prefers-reduced-motionを壊さない ③色・書体・角丸・影はトークン再定義で表現する
6. 新スキンは `functions.php` の `catelabo_base_skins()` に登録（label＋Google Fonts URL）
7. セクションバリアントの追加＝SCSSに1ブロック＋ `inc/theme-options.php` の登録関数に1項目。ヒーロー=`p-hero--{name}`/catelabo_hero_variants、子猫一覧=`.klist-{name}`/catelabo_kitten_list_variants、見出し=`hstyle-*`/catelabo_heading_styles。他セクションを増やすときも同方式で
8. 見出し系のクラスには `font-family: var(--font-heading)` を自前で宣言（h1〜h4の要素セレクタに依存しない）
9. 関数・mixin（rem / rclamp / mq / cq / bleed / arrow）は `@use "../foundation/functions" as *;` `@use "../foundation/mixins" as *;`（object配下は `../../foundation/…`）
10. 指示されていないファイルを変更しない。大量の一括変更をしない
11. 作業完了時は必ず `git add -A && git commit -m "<内容が分かる一言>"` を実行する。**push は行わない**（pushは人間が確認してから手動で行う）（例: "skin-nostalgia: 初版作成" / "skin-yuki: ホバーアニメーション調整"）
12. JSは const を基本とし、再代入がある変数のみ let。var は使用しない

## 品質基準

- 本文16px以上・行間1.8以上（読者は40〜60代中心）
- 文字と背景のコントラスト比4.5:1以上（スキンの色決定時に必ず確認し、計算結果を報告する）
- `:focus-visible` と `prefers-reduced-motion` を壊さない
- 出現モーションは要素に `c-reveal` を付ける方式に乗せる（独自のスクロール監視JSを追加しない）。強度は body の `motion-*` が制御する
- 画像は width/height 属性必須・4:3は object-fit: cover

## 作業の進め方

- 1セッション＝1スキン（または1バリアント）。tokens再定義 → ビルド → 確認 → 微調整の順で小さく進める
- 完了報告には必ず含める：変更ファイル一覧／ビルド結果／生HEXチェック結果（`grep -rn "#[0-9a-fA-F]\{3\}" assets/scss --include="*.scss" | grep -v _tokens | grep -v skins/` が0件）／人間がカスタマイザーで確認する手順

## スキン制作の必読ノート

docs/テーマ開発指針.md の **§9(実案件で踏んだ落とし穴)を作業前に必ず読む**。特に:
ベース側の同セレクタ全プロパティ確認(min-height等の打ち消し)/Gridは align-self: start /
rem()にはpxを渡す/radial-gradientはcircle明示/重ねる配置はabsoluteでなくフローで/
縦書きは nowrap+手動改行+svh連動フォント。

微調整フェーズでは、人間からスクリーンショットと「要素名+数値(px/rem)+方向」の指示が来る。
その数値をそのまま適用し、勝手に別の値へ「良かれと思って」変えない。