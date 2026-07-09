<?php
/**
 * CTA帯（共通テンプレートパーツ）
 *
 * 呼び出し方: get_template_part( 'template-parts/cta' )
 *
 * 表示しないケース:
 *   1. カスタマイザーで CTA リンク先が未設定かつ contact ページが存在しない
 *   2. お問い合わせページ（スラッグ: contact）自身
 *
 * テキストはカスタマイザー「CTA帯の見出し / 本文」で変更可能。
 */

$cta_url   = catelabo_cta_url();
$cta_label = get_theme_mod( 'catelabo_cta_label', '見学のご相談' );

if ( ! $cta_url ) {
	return;
}

// お問い合わせページ自身では表示しない
if ( is_page( 'contact' ) ) {
	return;
}

$title = get_theme_mod( 'catelabo_cta_section_title', '見学のご相談・お問い合わせ' );
$text  = get_theme_mod( 'catelabo_cta_section_text',  '気になる子がいましたら、お気軽にご連絡ください。' );
?>
<section class="p-cta">
	<div class="l-container p-cta__inner c-reveal">
		<?php if ( $title ) : ?>
			<h2 class="p-cta__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $text ) : ?>
			<p class="p-cta__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<a class="c-btn c-btn--inverse" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a>
	</div>
</section>
