<?php
/**
 * OGP・JSON-LD の自動出力
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * パンくずの経路データ（単一情報源）。
 * 表示は template-parts/breadcrumb.php、JSON-LD(BreadcrumbList)は下のwp_headが
 * 同じこの関数から出力する。対象は子猫詳細・親猫詳細・下層固定ページのみ（TOP不要）。
 *
 * @return array{label:string,url:string}[] 経路。対象外のページでは空配列。
 */
function catelabo_breadcrumb_trail() {
	$trail = array();

	if ( is_front_page() || ! ( is_singular( 'kitten' ) || is_singular( 'parent_cat' ) || is_page() ) ) {
		return $trail;
	}

	$qid = get_queried_object_id();

	$trail[] = array(
		'label' => 'ホーム',
		'url'   => home_url( '/' ),
	);

	if ( is_singular( 'kitten' ) ) {
		$archive = get_post_type_archive_link( 'kitten' );
		if ( $archive ) {
			$trail[] = array(
				'label' => '子猫のご案内',
				'url'   => $archive,
			);
		}
	} elseif ( is_singular( 'parent_cat' ) ) {
		$archive = get_post_type_archive_link( 'parent_cat' );
		if ( $archive ) {
			$trail[] = array(
				'label' => '親猫のご紹介',
				'url'   => $archive,
			);
		}
	} elseif ( is_page() ) {
		// 親ページがあれば経路に挟む（上の階層から順に）
		foreach ( array_reverse( get_post_ancestors( $qid ) ) as $ancestor_id ) {
			$trail[] = array(
				'label' => get_the_title( $ancestor_id ),
				'url'   => get_permalink( $ancestor_id ),
			);
		}
	}

	$trail[] = array(
		'label' => get_the_title( $qid ),
		'url'   => '', // 現在地はリンクしない
	);

	return $trail;
}

add_action( 'wp_head', function () {

	$title = wp_get_document_title();
	$url   = home_url( add_query_arg( array() ) );
	$desc  = get_bloginfo( 'description' );
	$image = '';
	$type  = 'website';

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$url     = get_permalink( $post_id );
		$type    = 'article';

		if ( has_post_thumbnail( $post_id ) ) {
			$image = wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'large' );
		}
		if ( 'kitten' === get_post_type( $post_id ) ) {
			$intro = catelabo_get_field( 'kitten_intro', $post_id );
			if ( $intro ) {
				$desc = wp_trim_words( $intro, 60, '…' );
			} else {
				// 紹介文が空でも、フィールドから説明文を自動生成する
				// （og:descriptionが無いとLINE等のクローラーがページ本文を拾ってしまうため）
				$breeds    = get_the_terms( $post_id, 'breed' );
				$breed     = ( $breeds && ! is_wp_error( $breeds ) ) ? $breeds[0]->name : '';
				$color     = catelabo_get_field( 'kitten_color', $post_id );
				$sexes     = catelabo_sex_labels();
				$sex       = catelabo_get_field( 'kitten_sex', $post_id );
				$sex_label = isset( $sexes[ $sex ] ) ? $sexes[ $sex ] : '子猫';
				$birthday  = catelabo_get_field( 'kitten_birthday', $post_id );
				$status    = catelabo_kitten_status( $post_id );

				$auto  = $breed ? $breed . '、' : '';
				$auto .= $color ? $color . 'の' . $sex_label : $sex_label;
				$auto .= '。';
				if ( $birthday ) {
					$auto .= date_i18n( 'Y年n月j日', strtotime( $birthday ) ) . '生まれ。';
				}
				$auto .= $status['label'] . 'です。';
				$desc  = $auto;
			}
		}
	}

	// 説明文が空ならサイト名で埋める（クローラーの本文スクレイピング防止）
	if ( ! $desc ) {
		$desc = get_bloginfo( 'name' );
	}

	echo "\n<!-- Catelabo OGP -->\n";
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );

	// 子猫詳細: JSON-LD（Product相当）
	if ( is_singular( 'kitten' ) ) {
		$post_id = get_queried_object_id();
		$status  = catelabo_kitten_status( $post_id );

		$data = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Product',
			'name'        => get_the_title( $post_id ),
			'url'         => get_permalink( $post_id ),
			'description' => $desc,
			'brand'       => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
			),
		);
		if ( $image ) {
			$data['image'] = $image;
		}

		$price      = catelabo_get_field( 'kitten_price', $post_id );
		$price_show = catelabo_get_field( 'kitten_price_show', $post_id );
		if ( $price && $price_show ) {
			$data['offers'] = array(
				'@type'         => 'Offer',
				'price'         => (int) $price,
				'priceCurrency' => 'JPY',
				'availability'  => ( 'boshu' === $status['slug'] ) ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
			);
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}

	// パンくず: JSON-LD（BreadcrumbList）。表示側は template-parts/breadcrumb.php
	$trail = catelabo_breadcrumb_trail();
	if ( count( $trail ) >= 2 ) {
		$bc_items = array();
		foreach ( $trail as $i => $crumb ) {
			$bc_item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $crumb['label'],
			);
			if ( ! empty( $crumb['url'] ) ) {
				$bc_item['item'] = $crumb['url'];
			}
			$bc_items[] = $bc_item;
		}
		$bc_data = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $bc_items,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $bc_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
	echo "<!-- /Catelabo OGP -->\n";
}, 5 );
