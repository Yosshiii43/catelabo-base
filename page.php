<?php
/**
 * 固定ページ共通テンプレート
 * welcome / about / contact もこの構造を継承（スラッグ別テンプレートで拡張）
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="p-page l-section">
		<div class="l-container">
			<header class="c-heading">
				<h1 class="c-heading__title"><?php the_title(); ?></h1>
			</header>
			<div class="p-page__content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
