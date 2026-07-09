<?php
/**
 * 親猫詳細
 * 基本情報 ＋ この親猫から生まれた子猫の一覧
 */

get_header();

while ( have_posts() ) :
	the_post();

	$cat_id   = get_the_ID();
	$sex      = catelabo_get_field( 'cat_sex', $cat_id );
	$role     = ( 'father' === $sex ) ? '父猫' : ( ( 'mother' === $sex ) ? '母猫' : '' );
	$color    = catelabo_get_field( 'cat_color', $cat_id );
	$birthday = catelabo_get_field( 'cat_birthday', $cat_id );
	$dna      = catelabo_get_field( 'cat_dna', $cat_id );
	$intro    = catelabo_get_field( 'cat_intro', $cat_id );
	$breeds   = get_the_terms( $cat_id, 'breed' );

	// 写真: アイキャッチ + サブ1〜2
	$photo_ids = array();
	if ( has_post_thumbnail() ) {
		$photo_ids[] = get_post_thumbnail_id( $cat_id );
	}
	foreach ( array( 'cat_photo_1', 'cat_photo_2' ) as $key ) {
		$pid = (int) catelabo_get_field( $key, $cat_id );
		if ( $pid ) {
			$photo_ids[] = $pid;
		}
	}
	?>

	<article class="p-kitten-detail l-section">
		<div class="l-container">

			<p class="p-kitten-detail__back">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'parent_cat' ) ); ?>">← 親猫のご紹介にもどる</a>
			</p>

			<div class="p-kitten-detail__grid">

				<div class="p-kitten-detail__gallery js-gallery">
					<div class="p-kitten-detail__stage">
						<?php if ( ! empty( $photo_ids ) ) : ?>
							<?php echo wp_get_attachment_image( $photo_ids[0], 'large', false, array( 'class' => 'p-kitten-detail__stage-img js-gallery-main' ) ); ?>
						<?php endif; ?>
					</div>
					<?php if ( count( $photo_ids ) > 1 ) : ?>
						<ul class="p-kitten-detail__thumbs">
							<?php foreach ( $photo_ids as $i => $pid ) : ?>
								<li>
									<button type="button"
										class="p-kitten-detail__thumb js-gallery-thumb<?php echo 0 === $i ? ' is-current' : ''; ?>"
										data-full="<?php echo esc_url( wp_get_attachment_image_url( $pid, 'large' ) ); ?>">
										<?php echo wp_get_attachment_image( $pid, 'thumbnail' ); ?>
									</button>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="p-kitten-detail__info">
					<?php if ( $role || ( $breeds && ! is_wp_error( $breeds ) ) ) : ?>
						<p class="p-kitten-detail__breed">
							<?php echo esc_html( trim( $role . '　' . ( ( $breeds && ! is_wp_error( $breeds ) ) ? $breeds[0]->name : '' ) ) ); ?>
						</p>
					<?php endif; ?>

					<h1 class="p-kitten-detail__name"><?php the_title(); ?></h1>

					<dl class="p-kitten-detail__spec">
						<?php if ( $color ) : ?>
							<dt>毛色</dt>
							<dd><?php echo esc_html( $color ); ?></dd>
						<?php endif; ?>
						<?php if ( $birthday ) : ?>
							<dt>誕生日</dt>
							<dd><?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $birthday ) ) ); ?></dd>
						<?php endif; ?>
						<?php if ( $dna ) : ?>
							<dt>健康</dt>
							<dd><?php echo esc_html( $dna ); ?></dd>
						<?php endif; ?>
					</dl>

					<?php if ( $intro ) : ?>
						<div class="p-kitten-detail__health">
							<p class="p-kitten-detail__health-note"><?php echo nl2br( esc_html( $intro ) ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php
			// この親猫から生まれた子猫（卒業は除外の標準並び順）
			$meta_key = ( 'father' === $sex ) ? 'kitten_father' : 'kitten_mother';
			$children = new WP_Query( catelabo_kitten_list_query_args( array(
				'posts_per_page' => 6,
				'meta_query'     => array_merge(
					catelabo_kitten_list_query_args()['meta_query'],
					array( array( 'key' => $meta_key, 'value' => $cat_id ) )
				),
			) ) );
			?>
			<?php if ( $children->have_posts() ) : ?>
				<section class="p-kitten-detail__parents">
					<h2 class="p-kitten-detail__section-title"><?php the_title(); ?>の子猫たち</h2>
					<div class="p-kitten-list">
						<?php
						while ( $children->have_posts() ) :
							$children->the_post();
							get_template_part( 'template-parts/kitten-card' );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</section>
			<?php endif; ?>

		</div>
	</article>

	<?php
endwhile;

get_template_part( 'template-parts/cta' );

get_footer();
