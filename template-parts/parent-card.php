<?php
/**
 * 親猫カード（子猫詳細ページの「両親」欄で使用）
 * 呼び出し: get_template_part( 'template-parts/parent-card', null, array( 'role' => '父猫', 'data' => $parent ) );
 * $parent は catelabo_kitten_parent() の戻り値（type: post|text）
 */

$role = isset( $args['role'] ) ? $args['role'] : '';
$data = isset( $args['data'] ) ? $args['data'] : null;

if ( ! $data ) {
	return;
}

$is_post = ( 'post' === $data['type'] );
$tag     = $is_post ? 'a' : 'div';
$href    = $is_post ? ' href="' . esc_url( get_permalink( $data['id'] ) ) . '"' : '';
?>
<<?php echo $tag . $href; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="p-parent-card c-reveal">

	<div class="p-parent-card__photo">
		<?php if ( $is_post && has_post_thumbnail( $data['id'] ) ) : ?>
			<?php echo get_the_post_thumbnail( $data['id'], 'thumbnail' ); ?>
		<?php endif; ?>
	</div>

	<div class="p-parent-card__body">
		<p class="p-parent-card__role"><?php echo esc_html( $role ); ?></p>
		<p class="p-parent-card__name"><?php echo esc_html( $data['name'] ); ?></p>

		<?php if ( $is_post ) : ?>
			<?php
			$color = catelabo_get_field( 'cat_color', $data['id'] );
			$dna   = catelabo_get_field( 'cat_dna', $data['id'] );
			?>
			<?php if ( $color || $dna ) : ?>
				<p class="p-parent-card__meta">
					<?php echo esc_html( implode( ' ／ ', array_filter( array( $color, $dna ) ) ) ); ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
	</div>

</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
