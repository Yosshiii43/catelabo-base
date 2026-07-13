<?php
/**
 * 固定ページ共通テンプレート
 * welcome / about / contact もこの構造を継承（スラッグ別テンプレートで拡張）
 */

get_header();

get_template_part( 'template-parts/breadcrumb' );

while ( have_posts() ) :
	the_post();
	?>
	<article class="p-page l-section">
		<div class="l-container">
			<header class="c-heading">
				<p class="c-heading__label"><?php echo esc_html( get_post_field( 'post_name', get_the_ID() ) ); ?></p>
				<h1 class="c-heading__title"><?php the_title(); ?></h1>
			</header>
			<div class="p-page__content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/cta' );

get_footer();
