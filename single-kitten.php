<?php
/**
 * 子猫詳細
 * クラス契約: p-kitten-detail __gallery __spec __health __intro __parents __cta
 */

get_header();

get_template_part( 'template-parts/breadcrumb' );

while ( have_posts() ) :
	the_post();

	$kitten_id = get_the_ID();
	$status    = catelabo_kitten_status( $kitten_id );
	$fields    = catelabo_kitten_fields();

	$birthday   = catelabo_get_field( 'kitten_birthday', $kitten_id );
	$sex        = catelabo_get_field( 'kitten_sex', $kitten_id );
	$color      = catelabo_get_field( 'kitten_color', $kitten_id );
	$price      = catelabo_get_field( 'kitten_price', $kitten_id );
	$price_show = catelabo_get_field( 'kitten_price_show', $kitten_id );
	$vaccine    = catelabo_get_field( 'kitten_vaccine', $kitten_id );
	$dna        = catelabo_get_field( 'kitten_dna', $kitten_id );
	$health     = catelabo_get_field( 'kitten_health_note', $kitten_id );
	$video      = catelabo_get_field( 'kitten_video', $kitten_id );
	$intro      = catelabo_get_field( 'kitten_intro', $kitten_id );

	$sexes         = catelabo_sex_labels();
	$sex_label     = isset( $sexes[ $sex ] ) ? $sexes[ $sex ] : '';
	$vaccine_label = ( $vaccine && isset( $fields['kitten_vaccine']['options'][ $vaccine ] ) ) ? $fields['kitten_vaccine']['options'][ $vaccine ] : '';
	$dna_label     = ( $dna && isset( $fields['kitten_dna']['options'][ $dna ] ) ) ? $fields['kitten_dna']['options'][ $dna ] : '';

	$breeds = get_the_terms( $kitten_id, 'breed' );
	$father = catelabo_kitten_parent( $kitten_id, 'father' );
	$mother = catelabo_kitten_parent( $kitten_id, 'mother' );

	// 写真: アイキャッチ + サブ1〜4
	$photo_ids = array();
	if ( has_post_thumbnail() ) {
		$photo_ids[] = get_post_thumbnail_id( $kitten_id );
	}
	foreach ( array( 'kitten_photo_1', 'kitten_photo_2', 'kitten_photo_3', 'kitten_photo_4' ) as $key ) {
		$pid = (int) catelabo_get_field( $key, $kitten_id );
		if ( $pid ) {
			$photo_ids[] = $pid;
		}
	}

	// CTA: 募集中・商談中のみ予約ボタンを出す
	$is_open = in_array( $status['slug'], array( 'boshu', 'shodan' ), true );
	$cta_url = catelabo_cta_url();

	// CTAが内部のお問い合わせページなら「ご希望の子猫」を事前入力するパラメータを付与
	// （CF7側のタグに default:get を付けると自動で反映される）
	$cta_link = $cta_url;
	if ( $cta_url && 0 === strpos( $cta_url, home_url() ) ) {
		$prefill = get_the_title();
		if ( false === strpos( $prefill, 'No.' ) ) {
			$prefill .= '（No.' . $kitten_id . '）';
		}
		$cta_link = add_query_arg( 'your-kitten', rawurlencode( $prefill ), $cta_url );
	}
	?>

	<article class="p-kitten-detail l-section">
		<div class="l-container">

			<p class="p-kitten-detail__back">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'kitten' ) ); ?>">← 子猫のご案内にもどる</a>
			</p>

			<div class="p-kitten-detail__grid">

				<!-- ギャラリー -->
				<div class="p-kitten-detail__gallery js-gallery c-reveal">
					<div class="p-kitten-detail__stage">
						<?php if ( ! empty( $photo_ids ) ) : ?>
							<?php echo wp_get_attachment_image( $photo_ids[0], 'large', false, array( 'class' => 'p-kitten-detail__stage-img js-gallery-main' ) ); ?>
						<?php endif; ?>
						<span class="p-kitten-detail__status c-badge c-badge--<?php echo esc_attr( $status['slug'] ); ?>">
							<?php echo esc_html( $status['label'] ); ?>
						</span>
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

				<!-- 基本情報 -->
				<div class="p-kitten-detail__info c-reveal">

					<?php if ( $breeds && ! is_wp_error( $breeds ) ) : ?>
						<p class="p-kitten-detail__breed"><?php echo esc_html( $breeds[0]->name ); ?></p>
					<?php endif; ?>

					<h1 class="p-kitten-detail__name"><?php the_title(); ?></h1>

					<dl class="p-kitten-detail__spec">
						<?php if ( $birthday ) : ?>
							<dt>誕生日</dt>
							<dd>
								<?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $birthday ) ) ); ?>
								<?php $age = catelabo_age_label( $birthday ); ?>
								<?php if ( $age ) : ?>
									<span class="p-kitten-detail__age">（<?php echo esc_html( $age ); ?>）</span>
								<?php endif; ?>
							</dd>
						<?php endif; ?>

						<?php if ( $sex_label ) : ?>
							<dt>性別</dt>
							<dd><?php echo esc_html( $sex_label ); ?></dd>
						<?php endif; ?>

						<?php if ( $color ) : ?>
							<dt>毛色</dt>
							<dd><?php echo esc_html( $color ); ?></dd>
						<?php endif; ?>

						<?php if ( 'kettei' !== $status['slug'] ) : // ご家族決定後は価格を非表示（業界慣行） ?>
							<dt>価格</dt>
							<dd>
								<?php if ( $price && $price_show ) : ?>
									<?php echo esc_html( '¥' . number_format( (int) $price ) . '（税込）' ); ?>
								<?php else : ?>
									お問い合わせください
								<?php endif; ?>
							</dd>
						<?php endif; ?>
					</dl>

					<?php if ( $vaccine_label || $dna_label || $health ) : ?>
						<div class="p-kitten-detail__health">
							<h2 class="p-kitten-detail__health-title">健康について</h2>
							<dl class="p-kitten-detail__spec">
								<?php if ( $vaccine_label ) : ?>
									<dt>ワクチン</dt>
									<dd><?php echo esc_html( $vaccine_label ); ?></dd>
								<?php endif; ?>
								<?php if ( $dna_label ) : ?>
									<dt>遺伝子検査</dt>
									<dd><?php echo esc_html( $dna_label ); ?></dd>
								<?php endif; ?>
							</dl>
							<?php if ( $health ) : ?>
								<p class="p-kitten-detail__health-note"><?php echo nl2br( esc_html( $health ) ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="p-kitten-detail__cta">
						<?php if ( $is_open && $cta_url ) : ?>
							<a class="c-btn c-btn--primary" href="<?php echo esc_url( $cta_link ); ?>">この子の見学を予約する</a>
						<?php elseif ( ! $is_open ) : ?>
							<p class="p-kitten-detail__decided">この子はご家族が決まりました。<br><a href="<?php echo esc_url( get_post_type_archive_link( 'kitten' ) ); ?>">ほかの子猫を見る →</a></p>
						<?php endif; ?>
					</div>

				</div>
			</div>

			<?php if ( $intro ) : ?>
				<section class="p-kitten-detail__intro c-reveal">
					<h2 class="p-kitten-detail__section-title">この子について</h2>
					<p><?php echo nl2br( esc_html( $intro ) ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( $video ) : ?>
				<?php $embed = wp_oembed_get( $video ); ?>
				<section class="p-kitten-detail__video c-reveal">
					<h2 class="p-kitten-detail__section-title">動画</h2>
					<?php if ( $embed ) : ?>
						<div class="p-kitten-detail__video-embed"><?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<?php else : ?>
						<p><a href="<?php echo esc_url( $video ); ?>" target="_blank" rel="noopener">動画を見る（外部サイト）→</a></p>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( $father || $mother ) : ?>
				<section class="p-kitten-detail__parents c-reveal">
					<h2 class="p-kitten-detail__section-title">この子の両親</h2>
					<div class="p-kitten-detail__parents-grid">
						<?php get_template_part( 'template-parts/parent-card', null, array( 'role' => '父猫', 'data' => $father ) ); ?>
						<?php get_template_part( 'template-parts/parent-card', null, array( 'role' => '母猫', 'data' => $mother ) ); ?>
					</div>
				</section>
			<?php endif; ?>

		</div>
	</article>

	<?php if ( $is_open && $cta_url ) : ?>
		<!-- スマホ追従CTA -->
		<div class="p-kitten-detail__ctabar">
			<a class="c-btn c-btn--primary" href="<?php echo esc_url( $cta_link ); ?>">この子の見学を予約する</a>
		</div>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
