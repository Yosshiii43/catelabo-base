<?php
/**
 * フォールバックテンプレート。
 * Phase 2-b以降で archive-kitten.php / single-kitten.php / front-page.php が追加される。
 */
get_header();
?>

<div class="l-section">
	<div class="l-container">

		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p>ページが見つかりませんでした。</p>
		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>
