<?php
/**
 * パンくずリスト
 * 設置: 子猫詳細・親猫詳細・下層固定ページのヘッダー直下（TOPには置かない）
 * 経路データは inc/seo.php の catelabo_breadcrumb_trail() が単一情報源。
 * JSON-LD(BreadcrumbList)も同関数から wp_head で出力される。
 */

$trail = function_exists( 'catelabo_breadcrumb_trail' ) ? catelabo_breadcrumb_trail() : array();
if ( count( $trail ) < 2 ) {
	return;
}
$last_index = count( $trail ) - 1;
?>
<nav class="c-breadcrumb" aria-label="現在位置">
	<div class="l-container">
		<ol class="c-breadcrumb__list">
			<?php foreach ( $trail as $i => $crumb ) : ?>
				<li class="c-breadcrumb__item">
					<?php if ( $i < $last_index && ! empty( $crumb['url'] ) ) : ?>
						<a class="c-breadcrumb__link" href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['label'] ); ?></a>
					<?php else : ?>
						<span class="c-breadcrumb__current" aria-current="page"><?php echo esc_html( $crumb['label'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</nav>
