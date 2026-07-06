<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<script>if ('IntersectionObserver' in window) { document.body.classList.add('js'); }</script>

<header class="p-header">
	<div class="p-header__inner l-container">

		<?php $brand_tag = is_front_page() ? 'h1' : 'p'; // TOPのh1はサイト名 ?>
		<<?php echo $brand_tag; ?> class="p-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="p-header__sitename" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</<?php echo $brand_tag; ?>>

		<button class="p-header__toggle js-nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
			<span class="p-header__toggle-bar" aria-hidden="true"></span>
			<span class="u-visually-hidden">メニュー</span>
		</button>

		<nav id="site-nav" class="p-header__nav js-nav" aria-label="メインメニュー">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'p-header__menu',
				'depth'          => 1,
				'fallback_cb'    => '__return_empty_string',
			) );
			$cta_url = catelabo_cta_url();
			if ( $cta_url ) :
				?>
				<a class="c-btn c-btn--primary p-header__cta" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( get_theme_mod( 'catelabo_cta_label', '見学のご相談' ) ); ?>
				</a>
			<?php endif; ?>
		</nav>

	</div>
</header>

<main class="l-main">
