/**
 * Catelabo Base — かんたん更新フォーム
 * 1) 写真：アップロード前にcanvasで長辺1600pxへ縮小＋プレビュー
 * 2) ステータス：一覧からワンタップ変更（fetch）
 * 3) LINE配信文コピー
 * 4) 二重送信防止
 * 依存なし（バニラJS）。設定は window.CATELABO_MANAGE（endpoint / nonce）
 */
(function () {
	'use strict';

	var CFG = window.CATELABO_MANAGE || {};

	/* ── 1) 写真の縮小＆プレビュー ── */

	var MAX_EDGE = 1600;

	function resizeImage(file) {
		return createImageBitmap(file).then(function (bmp) {
			var scale = Math.min(1, MAX_EDGE / Math.max(bmp.width, bmp.height));
			if (scale >= 1) {
				return file; // 十分小さい
			}
			var canvas = document.createElement('canvas');
			canvas.width = Math.round(bmp.width * scale);
			canvas.height = Math.round(bmp.height * scale);
			canvas.getContext('2d').drawImage(bmp, 0, 0, canvas.width, canvas.height);
			return new Promise(function (resolve) {
				canvas.toBlob(function (blob) {
					if (!blob) {
						resolve(file);
						return;
					}
					var name = file.name.replace(/\.\w+$/, '') + '.jpg';
					resolve(new File([blob], name, { type: 'image/jpeg' }));
				}, 'image/jpeg', 0.85);
			});
		}).catch(function () {
			return file; // 変換できない形式は原本のまま（サーバー側の自動縮小が保険）
		});
	}

	document.querySelectorAll('.js-photo').forEach(function (slot) {
		var input = slot.querySelector('.js-photo-input');
		var preview = slot.querySelector('.js-photo-preview');
		var hidden = slot.querySelector('.js-photo-id');
		var removeBtn = slot.querySelector('.js-photo-remove');

		if (!input || !preview) {
			return;
		}

		input.addEventListener('change', function () {
			var file = input.files[0];
			if (!file) {
				return;
			}
			resizeImage(file).then(function (resized) {
				if (resized !== file && window.DataTransfer) {
					try {
						var dt = new DataTransfer();
						dt.items.add(resized);
						input.files = dt.files;
					} catch (e) { /* 差し替え不可なら原本のまま送る */ }
				}
				var url = URL.createObjectURL(input.files[0]);
				preview.innerHTML = '<img src="' + url + '" alt="">';
				slot.classList.add('has-photo');
				if (removeBtn) {
					removeBtn.hidden = false;
				}
			});
		});

		if (removeBtn) {
			removeBtn.addEventListener('click', function () {
				input.value = '';
				if (hidden) {
					hidden.value = '';
				}
				preview.innerHTML = '';
				slot.classList.remove('has-photo');
				removeBtn.hidden = true;
			});
		}
	});

	/* ── 2) ステータスのワンタップ変更 ── */

	document.querySelectorAll('.js-manage-status').forEach(function (sel) {
		sel.dataset.prev = sel.value;

		sel.addEventListener('change', function () {
			var label = sel.options[sel.selectedIndex].text;

			if (!window.confirm('「' + label + '」に変更しますか？')) {
				sel.value = sel.dataset.prev;
				return;
			}

			var body = new URLSearchParams({
				action: 'catelabo_kitten_status',
				nonce: CFG.nonce || '',
				id: sel.dataset.id,
				status: sel.value
			});

			fetch(CFG.endpoint, { method: 'POST', credentials: 'same-origin', body: body })
				.then(function (res) { return res.json(); })
				.then(function (json) {
					if (!json || !json.success) {
						throw new Error('failed');
					}
					sel.dataset.prev = sel.value;
					var item = sel.closest('.p-manage__item');
					if (item) {
						item.dataset.status = sel.value;
					}
				})
				.catch(function () {
					window.alert('変更できませんでした。通信環境をご確認のうえ、もう一度お試しください。');
					sel.value = sel.dataset.prev;
				});
		});
	});

	/* ── 3) LINE配信文コピー ── */

	var copyBtn = document.querySelector('.js-copy-line');
	var lineText = document.querySelector('.js-line-text');

	if (copyBtn && lineText) {
		copyBtn.addEventListener('click', function () {
			var done = function () {
				copyBtn.textContent = 'コピーしました ✓';
				setTimeout(function () {
					copyBtn.textContent = '配信文をコピーする';
				}, 2500);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(lineText.value).then(done);
			} else {
				lineText.select();
				document.execCommand('copy');
				done();
			}
		});
	}

	/* ── 4) 二重送信防止 ── */

	var form = document.querySelector('.js-manage-form');
	if (form) {
		form.addEventListener('submit', function () {
			// 押されたボタンのname/valueが送信に含まれるよう、無効化は次のティックで行う
			setTimeout(function () {
				form.querySelectorAll('.js-submit').forEach(function (btn) {
					btn.disabled = true;
					btn.textContent = '送信中…';
				});
			}, 0);
		});
	}
})();
