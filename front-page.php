<?php
/**
 * TOPページ
 * ヒーロー（バリアント切替）→ 子猫のご案内 → CTA帯
 */

get_header();

$hero_variant = get_theme_mod( 'catelabo_hero_variant', 'split' );
$hero_eyebrow = get_theme_mod( 'catelabo_hero_eyebrow', '' );
$hero_image   = get_theme_mod( 'catelabo_hero_image' );
$hero_title   = get_theme_mod( 'catelabo_hero_title', '大切に育てた子猫たちを、あなたの家族に。' );
$hero_text    = get_theme_mod( 'catelabo_hero_text', '' );
$cta_url      = catelabo_cta_url();
$cta_label    = get_theme_mod( 'catelabo_cta_label', '見学のご相談' );
?>

<!-- ヒーロー -->
<section class="p-hero p-hero--<?php echo esc_attr( $hero_variant ); ?>"
	<?php if ( $hero_image && 'photo' === $hero_variant ) : ?>
		style="background-image:url('<?php echo esc_url( $hero_image ); ?>');"
	<?php endif; ?>>
	<div class="l-container p-hero__inner">
		<div class="p-hero__body c-reveal">
			<?php if ( $hero_eyebrow ) : ?>
				<p class="p-hero__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<?php endif; ?>
			<p class="p-hero__title"><?php echo nl2br( esc_html( $hero_title ) ); ?></p>
			<span class="p-hero__divider" aria-hidden="true"></span>
			<?php if ( $hero_text ) : ?>
				<p class="p-hero__text"><?php echo nl2br( esc_html( $hero_text ) ); ?></p>
			<?php endif; ?>
			<div class="p-hero__cta">
				<?php if ( $cta_url ) : ?>
					<a class="c-btn c-btn--ghost" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a>
				<?php endif; ?>
				<a class="p-hero__sub" href="<?php echo esc_url( get_post_type_archive_link( 'kitten' ) ); ?>">子猫たちを見る <span class="p-hero__sub-arrow" aria-hidden="true">→</span></a>
			</div>
		</div>
		<?php if ( $hero_image && 'split' === $hero_variant ) : ?>
			<div class="p-hero__media c-reveal">
				<img src="<?php echo esc_url( $hero_image ); ?>" alt="" width="900" height="1200">
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
// ごあいさつ（カスタマイザーで本文を入力すると表示される）
$greet_title = get_theme_mod( 'catelabo_greeting_title', 'ごあいさつ' );
$greet_text  = get_theme_mod( 'catelabo_greeting_text', '' );
$greet_image = get_theme_mod( 'catelabo_greeting_image', '' );
?>
<?php if ( $greet_text ) : ?>
<section class="p-greeting l-section">
	<div class="l-container p-greeting__inner<?php echo $greet_image ? '' : ' p-greeting__inner--noimage'; ?>">
		<?php if ( $greet_image ) : ?>
			<div class="p-greeting__media c-reveal">
				<img src="<?php echo esc_url( $greet_image ); ?>" alt="" width="800" height="600">
			</div>
		<?php endif; ?>
		<div class="p-greeting__body c-reveal">
			<header class="c-heading c-heading--left c-reveal">
				<p class="c-heading__label">Greeting</p>
				<h2 class="c-heading__title"><?php echo esc_html( $greet_title ); ?></h2>
			</header>
			<p class="p-greeting__text"><?php echo nl2br( esc_html( $greet_text ) ); ?></p>
			<?php $about_page = get_page_by_path( 'about' ); ?>
			<?php if ( $about_page ) : ?>
				<p class="p-greeting__more"><a href="<?php echo esc_url( get_permalink( $about_page ) ); ?>">私たちについて <span aria-hidden="true">→</span></a></p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- 子猫のご案内（最新6匹） -->
<section class="l-section">
	<div class="l-container">
		<header class="c-heading c-reveal">
			<p class="c-heading__label">Kittens</p>
			<h2 class="c-heading__title">子猫のご案内</h2>
		</header>

		<?php $kittens = new WP_Query( catelabo_kitten_list_query_args( array( 'posts_per_page' => 6 ) ) ); ?>
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
			<p class="p-more">
				<a class="c-btn c-btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'kitten' ) ); ?>">子猫の一覧を見る</a>
			</p>
		<?php else : ?>
			<p class="p-kitten-list__empty">ただいまご案内できる子猫はいません。<br>子猫が生まれましたら、こちらでお知らせします。</p>
		<?php endif; ?>
	</div>
</section>

<?php get_template_part( 'template-parts/cta' ); ?>

<?php get_footer(); ?>
