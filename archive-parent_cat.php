<?php
/**
 * 親猫一覧
 */

get_header();
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
			'posts_per_page' => -1,
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
		<?php else : ?>
			<p class="p-kitten-list__empty">親猫の情報は準備中です。</p>
		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>
