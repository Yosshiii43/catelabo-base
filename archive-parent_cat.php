<?php
/**
 * 親猫一覧
 */

get_header();

$paged = max( 1, (int) get_query_var( 'paged' ) );
?>

<div class="l-section">
	<div class="l-container">

		<header class="c-heading c-reveal">
			<p class="c-heading__label">Parents</p>
			<h1 class="c-heading__title">親猫のご紹介</h1>
		</header>

		<?php
		$parents = new WP_Query( array(
			'post_type'      => 'parent_cat',
			'post_status'    => 'publish',
			'posts_per_page' => 12, // 子猫一覧と同じ12件/ページ
			'paged'          => $paged,
			'meta_key'       => 'cat_sex',
			'orderby'        => array( 'meta_value' => 'ASC', 'title' => 'ASC' ), // 父猫→母猫
		) );
		?>

		<?php if ( $parents->have_posts() ) : ?>
			<div class="p-parent-list">
				<?php
				while ( $parents->have_posts() ) :
					$parents->the_post();
					$pid  = get_the_ID();
					$sex  = catelabo_get_field( 'cat_sex', $pid );
					$role = ( 'father' === $sex ) ? '父猫' : ( ( 'mother' === $sex ) ? '母猫' : '' );
					get_template_part( 'template-parts/parent-card', null, array(
						'role' => $role,
						'data' => array( 'type' => 'post', 'id' => $pid, 'name' => get_the_title() ),
					) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php
			// ページ送り（archive-kitten.php と同方式）
			if ( $parents->max_num_pages > 1 ) :
				$page_links = paginate_links( array(
					'total'     => $parents->max_num_pages,
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
			<p class="p-kitten-list__empty">親猫の情報は準備中です。</p>
		<?php endif; ?>

	</div>
</div>

<?php get_template_part( 'template-parts/cta' ); ?>

<?php get_footer(); ?>
