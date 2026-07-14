</main>

<footer class="p-footer">
	<div class="p-footer__inner l-container">

		<p class="p-footer__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); // ロゴ設定時はヘッダーと同じロゴ（ホームへのリンク込み）を小さく ?>
			<?php else : ?>
				<a class="p-footer__brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</p>

		<nav class="p-footer__nav" aria-label="フッターメニュー">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'p-footer__menu',
				'depth'          => 1,
				'fallback_cb'    => '__return_empty_string',
			) );
			?>
		</nav>

		<?php if ( has_nav_menu( 'footer_legal' ) ) : ?>
			<nav class="p-footer__legal" aria-label="法的情報">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer_legal',
					'container'      => false,
					'menu_class'     => 'p-footer__legal-menu',
					'depth'          => 1,
					'fallback_cb'    => '__return_empty_string',
				) );
				?>
			</nav>
		<?php endif; ?>

		<?php $license = get_theme_mod( 'catelabo_license' ); ?>
		<?php if ( $license ) : ?>
			<p class="p-footer__license"><?php echo esc_html( $license ); ?></p>
		<?php endif; ?>

		<p class="p-footer__copy">&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
