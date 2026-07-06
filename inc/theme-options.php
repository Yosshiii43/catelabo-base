<?php
/**
 * カスタマイザー追加設定
 * ・TOPヒーロー（バリアント・画像・キャッチコピー）
 * ・動物取扱業の法定表示フィールド（page-legal.php が表として出力）
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 法定表示フィールドの定義（key => ラベル） */
function catelabo_legal_fields() {
	return array(
		'legal_owner'   => '氏名または法人名',
		'legal_shop'    => '事業所の名称',
		'legal_address' => '事業所の所在地',
		'legal_type'    => '動物取扱業の種別',
		'legal_number'  => '登録番号',
		'legal_date'    => '登録年月日',
		'legal_expiry'  => '有効期間の末日',
		'legal_manager' => '動物取扱責任者',
	);
}

/** 見出し装飾スタイル一覧（bodyクラス hstyle-* に出力される） */
function catelabo_heading_styles() {
	return apply_filters( 'catelabo_heading_styles', array(
		'line'   => '左の縦線（line）',
		'bottom' => '下線＋アクセント（bottom）',
		'dot'    => '先頭に丸（dot）',
		'plain'  => '装飾なし（plain）',
	) );
}

/** モーション強度一覧（bodyクラス motion-* に出力される） */
function catelabo_motion_levels() {
	return apply_filters( 'catelabo_motion_levels', array(
		'soft' => 'ソフト（標準）',
		'rich' => 'リッチ',
		'none' => 'なし',
	) );
}

/** 子猫一覧のレイアウトバリアント一覧（bodyクラス klist-* に出力される） */
function catelabo_kitten_list_variants() {
	return apply_filters( 'catelabo_kitten_list_variants', array(
		'grid'   => 'グリッド（標準）',
		'rows'   => '横長リスト（rows）',
		'mosaic' => 'モザイク（mosaic）',
	) );
}

/** ヒーローのレイアウトバリアント一覧（パターンライブラリはここに追加していく） */
function catelabo_hero_variants() {
	return apply_filters( 'catelabo_hero_variants', array(
		'split'   => '左右分割（split）',
		'photo'   => '写真全面（photo）',
		'minimal' => '文字主体（minimal）',
	) );
}

add_action( 'customize_register', function ( $wp_customize ) {

	/* ── TOPヒーロー ── */
	$wp_customize->add_section( 'catelabo_hero', array(
		'title'    => 'TOPヒーロー',
		'priority' => 31,
	) );

	$wp_customize->add_setting( 'catelabo_hero_variant', array(
		'default'           => 'split',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'catelabo_hero_variant', array(
		'label'   => 'レイアウトバリアント',
		'section' => 'catelabo_hero',
		'type'    => 'select',
		'choices' => catelabo_hero_variants(),
	) );

	$wp_customize->add_setting( 'catelabo_hero_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'catelabo_hero_image', array(
		'label'       => 'ヒーロー画像',
		'description' => 'photo=背景全面／split=右側に表示。横長の写真を推奨。',
		'section'     => 'catelabo_hero',
	) ) );

	$wp_customize->add_setting( 'catelabo_hero_eyebrow', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'catelabo_hero_eyebrow', array(
		'label'       => '小見出し（英字タグライン）',
		'description' => '例：Scottish Fold & Ragdoll Cattery',
		'section'     => 'catelabo_hero',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'catelabo_hero_title', array(
		'default'           => '大切に育てた子猫たちを、あなたの家族に。',
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'catelabo_hero_title', array(
		'label'       => 'キャッチコピー',
		'description' => '改行がそのまま表示に反映されます。読ませたい位置で改行してください。',
		'section'     => 'catelabo_hero',
		'type'        => 'textarea',
	) );

	$wp_customize->add_setting( 'catelabo_hero_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'catelabo_hero_text', array(
		'label'   => 'サブテキスト',
		'section' => 'catelabo_hero',
		'type'    => 'textarea',
	) );

	// 見出しの装飾スタイル（キャテラボ設定セクションに追加）
	$wp_customize->add_setting( 'catelabo_heading_style', array(
		'default'           => 'line',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'catelabo_heading_style', array(
		'label'   => '見出しの装飾スタイル',
		'section' => 'catelabo_settings',
		'type'    => 'select',
		'choices' => catelabo_heading_styles(),
	) );

	// モーション（出現・ホバー演出の強度）
	$wp_customize->add_setting( 'catelabo_motion', array(
		'default'           => 'soft',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'catelabo_motion', array(
		'label'   => 'モーション（動きの強さ）',
		'section' => 'catelabo_settings',
		'type'    => 'select',
		'choices' => catelabo_motion_levels(),
	) );

	// 子猫一覧のレイアウト
	$wp_customize->add_setting( 'catelabo_kitten_list_variant', array(
		'default'           => 'grid',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'catelabo_kitten_list_variant', array(
		'label'   => '子猫一覧のレイアウト',
		'section' => 'catelabo_settings',
		'type'    => 'select',
		'choices' => catelabo_kitten_list_variants(),
	) );

	/* ── TOPごあいさつ ── */
	$wp_customize->add_section( 'catelabo_greeting', array(
		'title'       => 'TOPごあいさつ',
		'description' => '本文を入力すると、TOPページのヒーロー直下に表示されます。',
		'priority'    => 32,
	) );
	$wp_customize->add_setting( 'catelabo_greeting_title', array(
		'default'           => 'ごあいさつ',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'catelabo_greeting_title', array(
		'label'   => '見出し',
		'section' => 'catelabo_greeting',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'catelabo_greeting_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'catelabo_greeting_text', array(
		'label'   => '本文',
		'section' => 'catelabo_greeting',
		'type'    => 'textarea',
	) );
	$wp_customize->add_setting( 'catelabo_greeting_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'catelabo_greeting_image', array(
		'label'   => '写真（任意）',
		'section' => 'catelabo_greeting',
	) ) );

	/* ── 法定表示（動物取扱業） ── */
	$wp_customize->add_section( 'catelabo_legal', array(
		'title'       => '法定表示（動物取扱業）',
		'description' => '入力した項目が「法定表示」ページ（スラッグ: legal）に表として表示されます。最新の法令・自治体要件は制作時に確認してください。',
		'priority'    => 32,
	) );

	foreach ( catelabo_legal_fields() as $key => $label ) {
		$wp_customize->add_setting( 'catelabo_' . $key, array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'catelabo_' . $key, array(
			'label'   => $label,
			'section' => 'catelabo_legal',
			'type'    => 'text',
		) );
	}
} );
