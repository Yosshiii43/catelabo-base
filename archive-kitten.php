<?php
/**
 * 子猫一覧
 * 並び順: 募集中 → 商談中 → ご家族決定（各グループ内は誕生日の新しい順）。「卒業」は除外。
 */

get_header();

$paged   = max( 1, (int) get_query_var( 'paged' ) );
$kittens = new WP_Query( catelabo_kitten_list_query_args( array( 'paged' => $paged ) ) );
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

			<?php
			// ページ送り（catelabo-core側のデフォルト12件/ページ）
			if ( $kittens->max_num_pages > 1 ) :
				$page_links = paginate_links( array(
					'total'     => $kittens->max_num_pages,
					'current'   => $paged,
					'mid_size'  => 1,
					'prev_text' => '前へ',
					'next_text' => '次へ',
					'type'      => 'array',
				) );
				if ( $page_links ) :
					?>
					<nav class="c-pagination" aria-label="ページ送り">
						<ul class="c-pagination__list">
							<?php foreach ( $page_links as $page_link ) : ?>
								<li class="c-pagination__item"><?php echo $page_link; // phpcs:ignore -- paginate_links() の出力はエスケープ済み ?></li>
							<?php endforeach; ?>
						</ul>
					</nav>
					<?php
				endif;
			endif;
			?>
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
