<?php
/**
 * かんたん更新（スラッグ: manage）
 * ビュー: 一覧（デフォルト） / ?action=new・?action=edit&id=N（フォーム） / ?done=N（公開完了）
 * 処理はプラグイン側 includes/manage-form.php（admin-post.php）が受ける。
 */

// ── 認証 ──
if ( ! is_user_logged_in() ) {
	auth_redirect();
	exit;
}
if ( ! current_user_can( 'edit_kittens' ) ) {
	wp_die( 'このページを利用する権限がありません。サイト管理者にお問い合わせください。' );
}

// 検索エンジンに載せない
add_filter( 'wp_robots', 'wp_robots_no_robots' );

// 専用スクリプト
wp_enqueue_script( 'catelabo-manage', get_template_directory_uri() . '/assets/js/manage.js', array(), CATELABO_BASE_VER, true );
wp_add_inline_script(
	'catelabo-manage',
	'window.CATELABO_MANAGE = ' . wp_json_encode( array(
		'endpoint' => admin_url( 'admin-post.php' ),
		'nonce'    => wp_create_nonce( 'catelabo_manage' ),
	) ) . ';',
	'before'
);

// ── ルーティング ──
$action  = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : '';
$view    = 'list';
$edit_id = 0;

if ( 'new' === $action ) {
	$view = 'form';
} elseif ( 'edit' === $action && ! empty( $_GET['id'] ) ) {
	$edit_id = absint( $_GET['id'] );
	$p       = get_post( $edit_id );
	if ( $p && 'kitten' === $p->post_type && current_user_can( 'edit_post', $edit_id ) ) {
		$view = 'form';
	} else {
		$edit_id = 0;
	}
}
if ( ! empty( $_GET['done'] ) ) {
	$done_id = absint( $_GET['done'] );
	if ( 'kitten' === get_post_type( $done_id ) ) {
		$view = 'done';
	}
}

get_header();
?>

<div class="p-manage l-section">
	<div class="l-container">

		<?php // ── 通知 ── ?>
		<?php if ( isset( $_GET['error'] ) && 'required' === $_GET['error'] ) : ?>
			<p class="p-manage__notice p-manage__notice--error">必須項目が入力されていませんでした。お手数ですが、もう一度入力してください。</p>
		<?php endif; ?>
		<?php if ( ! empty( $_GET['saved'] ) ) : ?>
			<p class="p-manage__notice">保存しました（下書き）。「公開する」を押すまでサイトには表示されません。</p>
		<?php endif; ?>

		<?php if ( 'done' === $view ) : ?>
			<?php
			// ══ 公開完了 ══
			$is_new    = ! empty( $_GET['is_new'] );
			$permalink = get_permalink( $done_id );
			?>
			<div class="p-manage__done">
				<p class="p-manage__done-icon" aria-hidden="true">🎉</p>
				<h1 class="p-manage__done-title"><?php echo $is_new ? '公開しました！' : '更新しました！'; ?></h1>
				<p class="p-manage__done-name"><?php echo esc_html( get_the_title( $done_id ) ); ?></p>

				<div class="p-manage__done-actions">
					<a class="c-btn c-btn--primary" href="<?php echo esc_url( $permalink ); ?>">公開ページを見る</a>
					<a class="c-btn c-btn--ghost" href="<?php echo esc_url( catelabo_manage_url() ); ?>">一覧にもどる</a>
				</div>

				<section class="p-manage__line">
					<h2 class="p-manage__line-title">LINEでお知らせしましょう</h2>
					<p class="p-manage__line-text">下の文章をコピーして、LINE公式アカウントの「メッセージ配信」にテキストとして貼り付けます。URLの部分には、この子のページの写真つきカードが自動で表示されます。さらに目立たせたいときは、吹き出しを追加して「画像」からメイン写真をアップロードするのがおすすめです（吹き出し3つまで1通分。追加コストはかかりません）。</p>
					<textarea class="p-manage__line-box js-line-text" rows="7" readonly><?php echo esc_textarea( catelabo_line_message( $done_id ) ); ?></textarea>
					<button type="button" class="c-btn c-btn--primary js-copy-line">配信文をコピーする</button>
				</section>
			</div>

		<?php elseif ( 'form' === $view ) : ?>
			<?php
			// ══ 登録・編集フォーム ══
			$editing      = (bool) $edit_id;
			$post_status  = $editing ? get_post_status( $edit_id ) : '';
			$is_published = ( 'publish' === $post_status );
			$thumb_id     = $editing ? (int) get_post_thumbnail_id( $edit_id ) : 0;
			?>
			<header class="p-manage__head">
				<h1 class="p-manage__title"><?php echo $editing ? '子猫の情報を編集' : '新しい子猫を登録'; ?></h1>
				<a class="p-manage__back" href="<?php echo esc_url( catelabo_manage_url() ); ?>">← 一覧にもどる</a>
			</header>

			<form class="p-manage__form js-manage-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="catelabo_kitten_save">
				<input type="hidden" name="kitten_id" value="<?php echo esc_attr( $edit_id ); ?>">
				<?php wp_nonce_field( 'catelabo_manage', 'catelabo_manage_nonce' ); ?>

				<?php // ── 写真 ── ?>
				<fieldset class="p-manage__photos">
					<legend class="p-manage__label">写真 <span class="p-manage__req">メインは必須</span></legend>

					<div class="p-manage__photo p-manage__photo--main js-photo<?php echo $thumb_id ? ' has-photo' : ''; ?>">
						<input type="hidden" name="thumb_id" value="<?php echo esc_attr( $thumb_id ); ?>" class="js-photo-id">
						<div class="p-manage__photo-preview js-photo-preview"><?php echo $thumb_id ? wp_get_attachment_image( $thumb_id, 'medium' ) : ''; ?></div>
						<label class="p-manage__photo-btn">メイン写真を選ぶ<input type="file" name="photo_main" accept="image/*" class="js-photo-input" hidden<?php echo $thumb_id ? '' : ' required'; ?>></label>
					</div>

					<div class="p-manage__photo-grid">
						<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
							<?php $pid = $editing ? (int) catelabo_get_field( 'kitten_photo_' . $i, $edit_id ) : 0; ?>
							<div class="p-manage__photo js-photo<?php echo $pid ? ' has-photo' : ''; ?>">
								<input type="hidden" name="kitten_photo_<?php echo $i; ?>" value="<?php echo esc_attr( $pid ); ?>" class="js-photo-id">
								<div class="p-manage__photo-preview js-photo-preview"><?php echo $pid ? wp_get_attachment_image( $pid, 'thumbnail' ) : ''; ?></div>
								<label class="p-manage__photo-btn">サブ<?php echo $i; ?><input type="file" name="photo_sub_<?php echo $i; ?>" accept="image/*" class="js-photo-input" hidden></label>
								<button type="button" class="p-manage__photo-remove js-photo-remove"<?php echo $pid ? '' : ' hidden'; ?>>外す</button>
							</div>
						<?php endfor; ?>
					</div>
					<p class="p-manage__help">写真は自動で軽く圧縮されるので、スマホで撮ったままの写真で大丈夫です。</p>
				</fieldset>

				<?php // ── フィールド（定義配列から自動生成） ── ?>
				<?php
				foreach ( catelabo_kitten_fields() as $key => $def ) {
					$value = $editing ? catelabo_get_field( $key, $edit_id ) : '';
					catelabo_manage_render_field( $key, $def, $value );
				}
				?>

				<div class="p-manage__submit">
					<button type="submit" name="publish" value="1" class="c-btn c-btn--primary js-submit">
						<?php echo $is_published ? '更新する' : '公開する'; ?>
					</button>
					<?php if ( ! $is_published ) : ?>
						<button type="submit" name="save_draft" value="1" formnovalidate class="c-btn c-btn--ghost js-submit">下書き保存</button>
					<?php endif; ?>
				</div>
				<?php if ( ! $is_published ) : ?>
					<p class="p-manage__help">「下書き保存」は入力の途中でも保存できます（サイトには表示されません）。続きは一覧の「編集」から。</p>
				<?php endif; ?>
			</form>

		<?php else : ?>
			<?php
			// ══ 一覧 ══
			$tab = ( isset( $_GET['tab'] ) && 'parents' === $_GET['tab'] ) ? 'parents' : 'kittens';
			?>
			<header class="p-manage__head">
				<h1 class="p-manage__title"><?php echo 'parents' === $tab ? '親猫の管理' : '子猫の管理'; ?></h1>
				<?php if ( 'parents' === $tab ) : ?>
					<a class="c-btn c-btn--primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=parent_cat' ) ); ?>">＋ 新しい親猫を登録</a>
				<?php else : ?>
					<a class="c-btn c-btn--primary" href="<?php echo esc_url( catelabo_manage_url( array( 'action' => 'new' ) ) ); ?>">＋ 新しい子猫を登録</a>
				<?php endif; ?>
			</header>

			<nav class="p-manage__tabs" aria-label="管理タブ">
				<a class="p-manage__tab<?php echo 'kittens' === $tab ? ' is-current' : ''; ?>" href="<?php echo esc_url( catelabo_manage_url() ); ?>">子猫</a>
				<a class="p-manage__tab<?php echo 'parents' === $tab ? ' is-current' : ''; ?>" href="<?php echo esc_url( catelabo_manage_url( array( 'tab' => 'parents' ) ) ); ?>">親猫</a>
			</nav>

			<?php if ( 'parents' === $tab ) : ?>
				<?php
				$parents = new WP_Query( array(
					'post_type'      => 'parent_cat',
					'post_status'    => array( 'publish', 'draft' ),
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
				) );
				?>
				<?php if ( $parents->have_posts() ) : ?>
					<div class="p-manage__list">
						<?php while ( $parents->have_posts() ) : $parents->the_post(); ?>
							<article class="p-manage__item">
								<div class="p-manage__item-thumb"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
								<div class="p-manage__item-body">
									<p class="p-manage__item-title"><?php the_title(); ?></p>
									<p class="p-manage__item-meta"><?php echo esc_html( catelabo_get_field( 'cat_color' ) ); ?></p>
								</div>
								<div class="p-manage__item-actions">
									<a class="p-manage__item-link" href="<?php echo esc_url( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit' ) ); ?>">編集</a>
								</div>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<p class="p-manage__empty">親猫はまだ登録されていません。</p>
				<?php endif; ?>
				<p class="p-manage__help">親猫の登録・編集は、WordPressの管理画面（従来の入力画面）が開きます。終わったら、画面上部の「🐾 かんたん更新」からこのページに戻れます。</p>

			<?php else : ?>
				<?php
				$kittens = new WP_Query( array(
					'post_type'      => 'kitten',
					'post_status'    => array( 'publish', 'draft' ),
					'posts_per_page' => -1,
					'orderby'        => 'date',
					'order'          => 'DESC',
				) );
				?>
				<?php if ( $kittens->have_posts() ) : ?>
					<div class="p-manage__list">
						<?php while ( $kittens->have_posts() ) : $kittens->the_post(); ?>
							<?php $s = catelabo_kitten_status( get_the_ID() ); ?>
							<article class="p-manage__item" data-status="<?php echo esc_attr( $s['slug'] ); ?>">
								<div class="p-manage__item-thumb"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
								<div class="p-manage__item-body">
									<p class="p-manage__item-title">
										<?php the_title(); ?>
										<?php if ( 'draft' === get_post_status() ) : ?><span class="p-manage__draft">下書き</span><?php endif; ?>
									</p>
									<?php $bd = catelabo_get_field( 'kitten_birthday' ); ?>
									<?php if ( $bd ) : ?>
										<p class="p-manage__item-meta"><?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $bd ) ) ); ?>生まれ<?php $age = catelabo_age_label( $bd ); echo $age ? '（' . esc_html( $age ) . '）' : ''; ?></p>
									<?php endif; ?>
									<label class="p-manage__status">
										<span class="u-visually-hidden">ステータス</span>
										<select class="js-manage-status" data-id="<?php the_ID(); ?>">
											<?php foreach ( catelabo_status_labels() as $k => $label ) : ?>
												<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $s['slug'], $k ); ?>><?php echo esc_html( $label ); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
								</div>
								<div class="p-manage__item-actions">
									<a class="p-manage__item-link" href="<?php echo esc_url( catelabo_manage_url( array( 'action' => 'edit', 'id' => get_the_ID() ) ) ); ?>">編集</a>
									<?php if ( 'publish' === get_post_status() ) : ?>
										<a class="p-manage__item-link" href="<?php the_permalink(); ?>" target="_blank" rel="noopener">ページを見る</a>
									<?php endif; ?>
								</div>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<p class="p-manage__empty">まだ子猫が登録されていません。「＋ 新しい子猫を登録」から始めましょう。</p>
				<?php endif; ?>
			<?php endif; ?>

		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>
