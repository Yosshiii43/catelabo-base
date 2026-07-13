<?php
/**
 * 法定表示ページ（スラッグ: legal）
 * カスタマイザー「法定表示（動物取扱業）」の入力値を表として出力し、
 * その下に編集画面の本文（特商法表記・プライバシーポリシー等）を表示する。
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

			<?php
			$rows = array();
			foreach ( catelabo_legal_fields() as $key => $label ) {
				$value = get_theme_mod( 'catelabo_' . $key );
				if ( $value ) {
					$rows[ $label ] = $value;
				}
			}
			?>
			<?php if ( $rows ) : ?>
				<section class="p-page__legal">
					<h2 class="p-page__legal-title">第一種動物取扱業に関する表示</h2>
					<table class="p-page__legal-table">
						<tbody>
							<?php foreach ( $rows as $label => $value ) : ?>
								<tr>
									<th scope="row"><?php echo esc_html( $label ); ?></th>
									<td><?php echo esc_html( $value ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</section>
			<?php endif; ?>

			<div class="p-page__content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/cta' );

get_footer();
