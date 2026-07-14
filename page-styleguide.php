<?php
/**
 * Template Name: コンポーネント見本（開発用）
 *
 * ベーステーマ変更時の全スキン確認を1ページに集約する開発用テンプレート。
 *
 * 使い方:
 *   1. 固定ページを新規作成（タイトル例:「コンポーネント見本」）し、
 *      テンプレートに「コンポーネント見本（開発用）」を選んで公開する
 *   2. ベースを変更したら、カスタマイザーでスキン×モーション×見出し装飾を
 *      切り替えながらこのページを流し見る（1スキン30秒が目安）
 *   3. 顧客サイトにはこのページを作らない（テンプレートファイル自体は無害）
 *
 * 実データ不要: カードはプレースホルダSVG内蔵の静的マークアップ（外部リクエストなし）。
 * 各セクションの「確認 ▸」に見るべきポイントを書いてある。
 */

// 検索エンジンに拾わせない（開発用ページ）
add_filter( 'wp_robots', 'wp_robots_no_robots' );

get_header();

get_template_part( 'template-parts/breadcrumb' );

// プレースホルダ画像（データURI・外部リクエストなし）
$ph_43 = 'data:image/svg+xml;charset=utf-8,' . rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600"><rect width="800" height="600" fill="#D8D4C8"/><circle cx="400" cy="250" r="95" fill="#C6C0B0"/><path d="M330 180l-24-58 62 30zM470 180l24-58-62 30z" fill="#C6C0B0"/><path d="M295 480c0-80 47-125 105-125s105 45 105 125z" fill="#C6C0B0"/></svg>' );
$ph_11 = 'data:image/svg+xml;charset=utf-8,' . rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300"><rect width="300" height="300" fill="#D8D4C8"/><circle cx="150" cy="130" r="55" fill="#C6C0B0"/><path d="M110 90l-14-34 36 17zM190 90l14-34-36 17z" fill="#C6C0B0"/></svg>' );
?>

<section class="l-section p-styleguide">
	<div class="l-container">
		<header class="c-heading c-heading--left">
			<p class="c-heading__label">Styleguide</p>
			<h1 class="c-heading__title">コンポーネント見本</h1>
		</header>
		<p class="p-styleguide__intro">
			ベース変更後の全スキン確認用ページです。カスタマイザーで「スキン／モーション／見出しの装飾／子猫一覧」を切り替えながら流し見てください。パンくずリストの実物はこのページの最上部（ヘッダー直下）に出ています。
		</p>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">セクション見出し（c-heading）</h2>
			<p class="p-styleguide__check">中央揃え／左揃えの両方でラベルと見出しの書体・字間が揃っているか</p>
		</div>

		<header class="c-heading">
			<p class="c-heading__label">Kittens</p>
			<h2 class="c-heading__title">子猫のご案内</h2>
		</header>

		<header class="c-heading c-heading--left">
			<p class="c-heading__label">Greeting</p>
			<h2 class="c-heading__title">ごあいさつ</h2>
		</header>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">本文と見出し装飾（p-page__content・hstyle対象）</h2>
			<p class="p-styleguide__check">カスタマイザー「見出しの装飾スタイル」を切り替えて h2 の装飾（線／下線／丸）が変わるか。本文の行間・リンク色</p>
		</div>

		<div class="p-page__content">
			<h2>見学のご案内について</h2>
			<p>この段落は本文スタイルの見本です。読者は40〜60代が中心のため、本文16px以上・行間1.8以上を品質基準としています。文中の<a href="#">テキストリンクの見え方</a>もここで確認します。</p>
			<ul>
				<li>箇条書きの1行目</li>
				<li>箇条書きの2行目。少し長めの文章にして折り返し時の字下がりを確認します</li>
			</ul>
		</div>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">ボタン（c-btn）とバッジ（c-badge）</h2>
			<p class="p-styleguide__check">hover（浮き・色）／Tabフォーカスでリングが全周見えるか。inverseはページ下部のCTA帯で確認</p>
		</div>

		<div class="p-styleguide__row">
			<a class="c-btn c-btn--primary" href="#">お問い合わせ</a>
			<a class="c-btn c-btn--ghost" href="#">くわしく見る</a>
			<a class="c-btn c-btn--primary c-btn--small" href="#">小さいボタン</a>
		</div>

		<div class="p-styleguide__row">
			<span class="c-badge c-badge--boshu">ご家族募集中</span>
			<span class="c-badge c-badge--shodan">ご商談中</span>
			<span class="c-badge c-badge--kettei">ご家族決定</span>
			<span class="c-badge c-badge--sotsugyo">卒業</span>
		</div>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">子猫カード（p-kitten-card）</h2>
			<p class="p-styleguide__check">hover応答（default=浮き＋影／yuki=写真ズーム／nostalgia=浮き＋影＋彩度／standard-01=紙の持ち上げ）。出現の時間差（motion soft/rich）。カスタマイザー「子猫一覧」の切替</p>
		</div>

		<div class="p-kitten-list">
			<?php
			$sg_kittens = array(
				array( 'boshu', 'ご家族募集中', 'ゆき' ),
				array( 'shodan', 'ご商談中', 'こむぎ' ),
				array( 'kettei', 'ご家族決定', 'あずき' ),
			);
			foreach ( $sg_kittens as $sg ) :
				?>
				<article class="p-kitten-card c-reveal" data-status="<?php echo esc_attr( $sg[0] ); ?>">
					<a class="p-kitten-card__link" href="#">
						<div class="p-kitten-card__photo">
							<img class="p-kitten-card__img" src="<?php echo esc_attr( $ph_43 ); ?>" alt="" width="800" height="600">
							<span class="p-kitten-card__status c-badge c-badge--<?php echo esc_attr( $sg[0] ); ?>"><?php echo esc_html( $sg[1] ); ?></span>
						</div>
						<div class="p-kitten-card__body">
							<p class="p-kitten-card__breed">ラグドール</p>
							<h2 class="p-kitten-card__name"><?php echo esc_html( $sg[2] ); ?></h2>
							<p class="p-kitten-card__meta">
								<span>2026年5月10日生まれ</span>
								<span class="p-kitten-card__age">生後2ヶ月</span>
							</p>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">親猫カード（p-parent-card）</h2>
			<p class="p-styleguide__check">リンク版のみhover応答が出るか（テキスト版は動かないのが正）。写真と枠の関係</p>
		</div>

		<div class="p-parent-list">
			<a class="p-parent-card c-reveal" href="#">
				<div class="p-parent-card__photo">
					<img src="<?php echo esc_attr( $ph_11 ); ?>" alt="" width="300" height="300">
				</div>
				<div class="p-parent-card__body">
					<p class="p-parent-card__role">父猫</p>
					<p class="p-parent-card__name">バート</p>
					<p class="p-parent-card__meta">シールポイント ／ PKD N/N</p>
				</div>
			</a>

			<div class="p-parent-card c-reveal">
				<div class="p-parent-card__photo"></div>
				<div class="p-parent-card__body">
					<p class="p-parent-card__role">母猫</p>
					<p class="p-parent-card__name">外部血統（テキスト登録）</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">ページネーション（c-pagination）</h2>
			<p class="p-styleguide__check">現在ページの反転色／hoverの罫色変化／タップ領域44px。※paginate_links()出力の再現（静的）</p>
		</div>

		<nav class="c-pagination" aria-label="ページ送り（見本）">
			<ul class="c-pagination__list">
				<li class="c-pagination__item"><a class="prev page-numbers" href="#">前へ</a></li>
				<li class="c-pagination__item"><a class="page-numbers" href="#">1</a></li>
				<li class="c-pagination__item"><span class="page-numbers current" aria-current="page">2</span></li>
				<li class="c-pagination__item"><a class="page-numbers" href="#">3</a></li>
				<li class="c-pagination__item"><span class="page-numbers dots">…</span></li>
				<li class="c-pagination__item"><a class="page-numbers" href="#">8</a></li>
				<li class="c-pagination__item"><a class="next page-numbers" href="#">次へ</a></li>
			</ul>
		</nav>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">よくある質問（c-faq）</h2>
			<p class="p-styleguide__check">開閉と矢印の回転／Q・Aマーカー／Tabフォーカスで全周リング（Enterで開閉しても切れ端が出ない）</p>
		</div>

		<div class="c-faq">
			<details class="c-faq__item">
				<summary class="c-faq__q">見学だけでも大丈夫ですか？</summary>
				<div class="c-faq__a">
					<p>もちろんです。実際に会ってみて決めていただくのがいちばんです。</p>
				</div>
			</details>
			<details class="c-faq__item">
				<summary class="c-faq__q">お迎えまでの流れを教えてください。</summary>
				<div class="c-faq__a">
					<p>お問い合わせ → ご見学 → ご契約 → お迎え、の順にご案内しています。</p>
					<p>2段落目の見本です。段落が縦に並び、Aマーカーとの位置関係が保たれているかを見ます。</p>
				</div>
			</details>
		</div>
	</div>
</section>

<section class="l-section p-styleguide">
	<div class="l-container">
		<div class="p-styleguide__head">
			<h2 class="p-styleguide__title">フォーム部品（p-page__content form）</h2>
			<p class="p-styleguide__check">入力枠のfocusリング／送信ボタンがそのスキンの他ボタンと同じhover挙動か（c-btn付与で追従）。※見本のため送信しても何も起きません</p>
		</div>

		<div class="p-page__content">
			<form action="#" method="get">
				<label>お名前
					<input type="text" name="sg-name" autocomplete="off">
				</label>
				<label>メールアドレス
					<input type="email" name="sg-email" autocomplete="off">
				</label>
				<label>お問い合わせ内容
					<textarea name="sg-message" rows="4"></textarea>
				</label>
				<?php // 送信ボタンはc-btnを付けてスキンのボタン挙動に追従させる（CF7: [submit class:c-btn class:c-btn--primary "送信する"]） ?>
				<button type="submit" class="c-btn c-btn--primary">送信する（見本）</button>
			</form>
		</div>
	</div>
</section>

<?php // CTA帯（静的見本。実物はカスタマイザーのCTA設定に依存するため、ここでは常に表示させる） ?>
<section class="p-cta p-styleguide__cta">
	<div class="l-container p-cta__inner">
		<h2 class="p-cta__title">見学のご相談・お問い合わせ</h2>
		<p class="p-cta__text">CTA帯の見本です。ボタンにTabフォーカスして、リングが帯上の文字色で全周見えるかを確認してください。</p>
		<a class="c-btn c-btn--inverse" href="#">見学のご相談</a>
	</div>
</section>

<?php get_footer(); ?>
