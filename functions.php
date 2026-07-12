<?php
/**
 * Catelabo Base — テーマ機能
 * ・FLOCSS順のCSS読み込み（スキンを必ず最後に）
 * ・スキン切替機構（カスタマイザー）＋スキン連動のGoogle Fonts
 * ・ヘッダーCTA／動物取扱業番号のカスタマイザー設定
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CATELABO_BASE_VER', '0.6.0' );

/* ─────────────────────────────
 * スキン定義
 * 新しいスキンを追加するとき：
 * 1) assets/scss/skins/skin-{キー}.scss を作成（トークン再定義が主体・上書き100行以内）
 * 2) `sass assets/scss:assets/css --no-source-map` でビルド
 * 3) この配列にキー・表示名・Google Fonts URLを追加
 * ──────────────────────────── */
function catelabo_base_skins() {
	$skins = array(
		'default' => array(
			'label'        => 'デフォルト（クリーム×モスグリーン）',
			'google_fonts' => 'https://fonts.googleapis.com/css2?family=Shippori+Mincho+B1:wght@500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap',
		),
    'yuki' => array(
      'label'        => '雪（白×ブルーグレー）',
      'google_fonts' => 'https://fonts.googleapis.com/css2?family=Shippori+Mincho+B1:wght@400;500;600&family=Noto+Sans+JP:wght@400;500;700&display=swap',
    ),
    'nostalgia' => array(
      'label'        => 'ノスタルジー（ラベンダー×淡ピンク）',
      'google_fonts' => 'https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@400;500&family=EB+Garamond:ital,wght@0,400;0,500;1,400&family=Dancing+Script:wght@500&family=Noto+Sans+JP:wght@400;500&display=swap',
    ),
    'standard-01' => array(
      'label'        => 'スタンダード01（上質な便箋）',
      // フォールバック（pair_fonts未一致時）＝classic
      'google_fonts' => 'https://fonts.googleapis.com/css2?family=Shippori+Mincho+B1:wght@500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap',
      // フォントペア連動（カスタマイザー「フォントペア」で切替。読み込むのは選択中の1ペア分のみ）
      'pair_fonts'   => array(
        'classic' => 'https://fonts.googleapis.com/css2?family=Shippori+Mincho+B1:wght@500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap',
        'modern'  => 'https://fonts.googleapis.com/css2?family=M+PLUS+1p:wght@500;700&family=Noto+Sans+JP:wght@400;500;700&display=swap',
        'soft'    => 'https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;500;700&family=Noto+Sans+JP:wght@400;500;700&display=swap',
      ),
    ),
	);
	return apply_filters( 'catelabo_base_skins', $skins );
}

/** 現在のスキンキー（未定義キーならdefaultへフォールバック） */
function catelabo_base_current_skin() {
	$skin  = get_theme_mod( 'catelabo_skin', 'default' );
	$skins = catelabo_base_skins();
	return isset( $skins[ $skin ] ) ? $skin : 'default';
}

/** 現在のフォントペアキー（未定義キーならclassicへフォールバック。一覧は inc/theme-options.php） */
function catelabo_current_font_pair() {
	$pair  = get_theme_mod( 'catelabo_font_pair', 'classic' );
	$pairs = catelabo_font_pairs();
	return isset( $pairs[ $pair ] ) ? $pair : 'classic';
}

/* ─────────────────────────────
 * 管理バー：管理者以外にはフロント側で表示しない
 * 理由①: 顧客（編集者権限）の画面を「かんたん更新」だけの簡素な体験に保つ
 * 理由②: 管理バーはモバイル幅でビューポートより横に長くなることがあり、
 *         固定配置のため html { overflow-x: clip } の外側で iOS の
 *         横パン（画面の横揺れ）を誘発する（2026-07 実機で確認）。
 * ※ wp-admin 内の管理バーには影響しない（親猫編集からの戻り導線は生きる）
 * ──────────────────────────── */
add_filter( 'show_admin_bar', function ( $show ) {
	return current_user_can( 'manage_options' ) ? $show : false;
} );

/* ─────────────────────────────
 * テーマサポート
 * ──────────────────────────── */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-width'  => true,
		'flex-height' => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	register_nav_menus( array(
		'primary' => 'メインメニュー',
		'footer'  => 'フッターメニュー',
	) );
} );

/* ─────────────────────────────
 * アセット読み込み（FLOCSS順・スキン最後）
 * ──────────────────────────── */
add_action( 'wp_enqueue_scripts', function () {
	$ver = CATELABO_BASE_VER;
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	// スキン連動のGoogle Fonts（pair_fontsを持つスキンはフォントペア連動）
	$skin  = catelabo_base_current_skin();
	$skins = catelabo_base_skins();
	$fonts = ! empty( $skins[ $skin ]['google_fonts'] ) ? $skins[ $skin ]['google_fonts'] : '';
	if ( ! empty( $skins[ $skin ]['pair_fonts'] ) ) {
		$pair = catelabo_current_font_pair();
		if ( ! empty( $skins[ $skin ]['pair_fonts'][ $pair ] ) ) {
			$fonts = $skins[ $skin ]['pair_fonts'][ $pair ];
		}
	}
	if ( $fonts ) {
		wp_enqueue_style( 'catelabo-fonts', $fonts, array(), null );
	}

	// コンパイル済みCSS（SCSSソースは assets/scss/。ビルド：sass assets/scss:assets/css --no-source-map）
	wp_enqueue_style( 'catelabo-main', $uri . '/assets/css/main.css', array(), $ver );

	// スキン（必ず最後に読み込み、トークンを実行時に上書きする）
	$skin_rel = 'assets/css/skins/skin-' . $skin . '.css';
	if ( file_exists( $dir . '/' . $skin_rel ) ) {
		wp_enqueue_style( 'catelabo-skin', $uri . '/' . $skin_rel, array( 'catelabo-main' ), $ver );
	}

	wp_enqueue_script( 'catelabo-main', $uri . '/assets/js/main.js', array(), $ver, true );
} );

/* bodyにスキンクラスを付与（レイアウトバリアントの切替にも使う） */
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'skin-' . catelabo_base_current_skin();
	$classes[] = 'hstyle-' . get_theme_mod( 'catelabo_heading_style', 'line' );
	$classes[] = 'motion-' . get_theme_mod( 'catelabo_motion', 'soft' );
	$classes[] = 'klist-' . get_theme_mod( 'catelabo_kitten_list_variant', 'grid' );
	$classes[] = 'pair-' . catelabo_current_font_pair();
	return $classes;
} );

/* ─────────────────────────────
 * カスタマイザー
 * ──────────────────────────── */
add_action( 'customize_register', function ( $wp_customize ) {

	$wp_customize->add_section( 'catelabo_settings', array(
		'title'    => 'キャテラボ設定',
		'priority' => 30,
	) );

	// スキン切替
	$choices = array();
	foreach ( catelabo_base_skins() as $key => $skin ) {
		$choices[ $key ] = $skin['label'];
	}
	$wp_customize->add_setting( 'catelabo_skin', array(
		'default'           => 'default',
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'catelabo_skin', array(
		'label'   => 'スキン（デザインの着せ替え）',
		'section' => 'catelabo_settings',
		'type'    => 'select',
		'choices' => $choices,
	) );

	// ヘッダーCTA
	$wp_customize->add_setting( 'catelabo_cta_label', array(
		'default'           => '見学のご相談',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'catelabo_cta_label', array(
		'label'   => 'ヘッダーCTAの文言',
		'section' => 'catelabo_settings',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'catelabo_cta_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'catelabo_cta_url', array(
		'label'       => 'ヘッダーCTAのリンク先',
		'description' => '空欄の場合はスラッグ contact のページへ自動リンクします。LINEに飛ばす場合はURLを入れ、文言に「LINE」を含めてください（例：LINEで相談）。',
		'section'     => 'catelabo_settings',
		'type'        => 'url',
	) );

	// CTA帯 見出し・本文
	$wp_customize->add_setting( 'catelabo_cta_section_title', array(
		'default'           => '見学のご相談・お問い合わせ',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'catelabo_cta_section_title', array(
		'label'   => 'CTA帯の見出し',
		'section' => 'catelabo_settings',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'catelabo_cta_section_text', array(
		'default'           => '気になる子がいましたら、お気軽にご連絡ください。',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'catelabo_cta_section_text', array(
		'label'   => 'CTA帯の本文',
		'section' => 'catelabo_settings',
		'type'    => 'text',
	) );

	// 動物取扱業 登録番号（フッター常時表示）
	$wp_customize->add_setting( 'catelabo_license', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'catelabo_license', array(
		'label'       => '動物取扱業 登録番号',
		'description' => '例：第一種動物取扱業（販売）○○県 第12345号。フッターに常時表示されます。',
		'section'     => 'catelabo_settings',
		'type'        => 'text',
	) );
} );

/* ─────────────────────────────
 * 追加モジュール
 * ──────────────────────────── */
require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/theme-options.php';

/**
 * CTAの遷移先URL。
 * カスタマイザー設定 → なければスラッグ contact のページ → なければ空。
 */
function catelabo_cta_url() {
	$url = get_theme_mod( 'catelabo_cta_url' );
	if ( $url ) {
		return $url;
	}
	$page = get_page_by_path( 'contact' );
	return $page ? get_permalink( $page ) : '';
}
