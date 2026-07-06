<?php
/**
 * 404ページ
 */

get_header();
?>

<div class="p-page l-section">
	<div class="l-container" style="text-align:center">

		<header class="c-heading">
			<p class="c-heading__label">404 Not Found</p>
			<h1 class="c-heading__title">ページが見つかりませんでした</h1>
		</header>

		<p style="max-width:32em;margin-inline:auto">
			お探しのページは、移動または削除された可能性があります。<br>
			ご家族が決まった子猫のページは、公開を終了している場合があります。
		</p>

		<p style="margin-top:2.5em;display:flex;gap:1.5em;justify-content:center;flex-wrap:wrap">
			<a class="c-btn c-btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'kitten' ) ); ?>">子猫のご案内を見る</a>
			<a class="c-btn c-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">トップページへ</a>
		</p>

	</div>
</div>

<?php get_footer(); ?>
