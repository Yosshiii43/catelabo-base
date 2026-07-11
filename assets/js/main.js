/**
 * Catelabo Base — フロントスクリプト
 * モバイルナビの開閉のみ（バニラJS・依存なし）
 */
(function () {
	'use strict';

	const toggle = document.querySelector('.js-nav-toggle');
	const nav = document.querySelector('.js-nav');

	if (!toggle || !nav) {
		return;
	}

	toggle.addEventListener('click', function () {
		const isOpen = nav.classList.toggle('is-open');
		toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
	});

	// パネル外をタップで閉じる
	document.addEventListener('click', function (e) {
		if (!nav.classList.contains('is-open')) {
			return;
		}
		if (!nav.contains(e.target) && !toggle.contains(e.target)) {
			nav.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
		}
	});
})();


/**
 * 子猫詳細：ギャラリーのサムネイル切替
 */
(function () {
	'use strict';

	document.querySelectorAll('.js-gallery').forEach(function (gallery) {
		const main = gallery.querySelector('.js-gallery-main');
		if (!main) {
			return;
		}

		gallery.querySelectorAll('.js-gallery-thumb').forEach(function (btn) {
			btn.addEventListener('click', function () {
				const src = btn.getAttribute('data-full');
				if (!src) {
					return;
				}
				main.src = src;
				main.removeAttribute('srcset');
				main.removeAttribute('sizes');

				gallery.querySelectorAll('.js-gallery-thumb.is-current').forEach(function (b) {
					b.classList.remove('is-current');
				});
				btn.classList.add('is-current');
			});
		});
	});
})();

/**
 * お問い合わせフォーム：URLパラメータからの事前入力
 * ?your-kitten=… を name="your-kitten" の入力欄へ反映する。
 * CF7の default:get に依存しないフォールバック（フォームプラグインを替えても動く）
 */
(function () {
	'use strict';

	const params = new URLSearchParams(window.location.search);
	const val = params.get('your-kitten');
	if (!val) {
		return;
	}

	const field = document.querySelector('input[name="your-kitten"]');
	if (field && !field.value) {
		field.value = val;
	}
})();

/**
 * ヒーロー写真の上にヘッダーが重なる間だけ .is-over-photo を付与
 * skin-nostalgia が利用（ink ↔ on-photo カラー切替）
 */
(function () {
	'use strict';

	const hero   = document.querySelector('.p-hero--photo');
	const header = document.querySelector('.p-header');
	if (!hero || !header) { return; }

	/* 初回ロード時の一瞬を防ぐため先に付与し、IO が訂正する */
	header.classList.add('is-over-photo');

	const io = new IntersectionObserver(function (entries) {
		header.classList.toggle('is-over-photo', entries[0].isIntersecting);
	}, { threshold: 0 });

	io.observe(hero);
})();

/**
 * スクロール出現モーション（c-reveal）
 * bodyの motion-soft / motion-rich のときだけ動く（クラス付与はheader.phpのインラインJS）
 */
(function () {
	'use strict';

	if (!document.body.classList.contains('js')) {
		return;
	}
	const els = document.querySelectorAll('.c-reveal');
	if (!els.length) {
		return;
	}

	const io = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (entry.isIntersecting) {
				entry.target.classList.add('is-inview');
				io.unobserve(entry.target);
			}
		});
	}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

	els.forEach(function (el) {
		io.observe(el);
	});
})();