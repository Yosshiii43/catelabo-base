<?php
/**
 * 子猫カード（一覧・TOP共通コンポーネント）
 * クラス契約: p-kitten-card __photo __status __name __meta __age / data-status
 */

$kitten_id = get_the_ID();
$status    = catelabo_kitten_status( $kitten_id );
$birthday  = catelabo_get_field( 'kitten_birthday', $kitten_id );
$breeds    = get_the_terms( $kitten_id, 'breed' );
?>
<article class="p-kitten-card c-reveal" data-status="<?php echo esc_attr( $status['slug'] ); ?>">
	<a class="p-kitten-card__link" href="<?php the_permalink(); ?>">

		<div class="p-kitten-card__photo">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'p-kitten-card__img' ) ); ?>
			<?php endif; ?>
			<span class="p-kitten-card__status c-badge c-badge--<?php echo esc_attr( $status['slug'] ); ?>">
				<?php echo esc_html( $status['label'] ); ?>
			</span>
		</div>

		<div class="p-kitten-card__body">
			<?php if ( $breeds && ! is_wp_error( $breeds ) ) : ?>
				<p class="p-kitten-card__breed"><?php echo esc_html( $breeds[0]->name ); ?></p>
			<?php endif; ?>

			<h2 class="p-kitten-card__name"><?php the_title(); ?></h2>

			<?php if ( $birthday ) : ?>
				<p class="p-kitten-card__meta">
					<span><?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $birthday ) ) ); ?>生まれ</span>
					<?php $age = catelabo_age_label( $birthday ); ?>
					<?php if ( $age ) : ?>
						<span class="p-kitten-card__age"><?php echo esc_html( $age ); ?></span>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>

	</a>
</article>
