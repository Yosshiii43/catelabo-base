<?php
/**
 * 子猫一覧
 * 並び順: 募集中 → 商談中 → ご家族決定（各グループ内は誕生日の新しい順）。「卒業」は除外。
 */

get_header();

$kittens = new WP_Query( catelabo_kitten_list_query_args() );
?>

<div class="l-section">
	<div class="l-container">

		<header class="c-heading c-reveal">
			<p class="c-heading__label">Kittens</p>
			<h1 class="c-heading__title">子猫のご案内</h1>
		</header>

		<?php if ( $kittens->have_posts() ) : ?>
			<div class="p-kitten-list">
				<?php
				while ( $kittens->have_posts() ) :
					$kittens->the_post();
					get_template_part( 'template-parts/kitten-card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<p class="p-kitten-list__empty">
				ただいまご案内できる子猫はいません。<br>
				子猫が生まれましたら、こちらのページとお知らせでご案内します。
			</p>
		<?php endif; ?>

	</div>
</div>

<?php get_template_part( 'template-parts/cta' ); ?>

<?php get_footer(); ?>
