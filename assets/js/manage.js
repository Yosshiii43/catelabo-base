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

	const CFG = window.CATELABO_MANAGE || {};

	/* ── 1) 写真の縮小＆プレビュー ── */

	const MAX_EDGE = 1600;

	function resizeImage(file) {
		return createImageBitmap(file).then(function (bmp) {
			const scale = Math.min(1, MAX_EDGE / Math.max(bmp.width, bmp.height));
			if (scale >= 1) {
				return file; // 十分小さい
			}
			const canvas = document.createElement('canvas');
			canvas.width = Math.round(bmp.width * scale);
			canvas.height = Math.round(bmp.height * scale);
			canvas.getContext('2d').drawImage(bmp, 0, 0, canvas.width, canvas.height);
			return new Promise(function (resolve) {
				canvas.toBlob(function (blob) {
					if (!blob) {
						resolve(file);
						return;
					}
					const name = file.name.replace(/\.\w+$/, '') + '.jpg';
					resolve(new File([blob], name, { type: 'image/jpeg' }));
				}, 'image/jpeg', 0.85);
			});
		}).catch(function () {
			return file; // 変換できない形式は原本のまま（サーバー側の自動縮小が保険）
		});
	}

	document.querySelectorAll('.js-photo').forEach(function (slot) {
		const input = slot.querySelector('.js-photo-input');
		const preview = slot.querySelector('.js-photo-preview');
		const hidden = slot.querySelector('.js-photo-id');
		const removeBtn = slot.querySelector('.js-photo-remove');

		if (!input || !preview) {
			return;
		}

		input.addEventListener('change', function () {
			const file = input.files[0];
			if (!file) {
				return;
			}
			resizeImage(file).then(function (resized) {
				if (resized !== file && window.DataTransfer) {
					try {
						const dt = new DataTransfer();
						dt.items.add(resized);
						input.files = dt.files;
					} catch (e) { /* 差し替え不可なら原本のまま送る */ }
				}
				const url = URL.createObjectURL(input.files[0]);
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
			const label = sel.options[sel.selectedIndex].text;

			if (!window.confirm('「' + label + '」に変更しますか？')) {
				sel.value = sel.dataset.prev;
				return;
			}

			const body = new URLSearchParams({
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
					const item = sel.closest('.p-manage__item');
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

	const copyBtn = document.querySelector('.js-copy-line');
	const lineText = document.querySelector('.js-line-text');

	if (copyBtn && lineText) {
		copyBtn.addEventListener('click', function () {
			const done = function () {
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

  /* ── 4) 二重送信防止 ＋ メイン写真の必須チェック
   * メイン写真の <input type="file"> は hidden のためnative required検証に
   * 使えない（focusできずエラーで検証全体が沈黙する）ので自前チェックする。
   * ブラウザ標準の検証（誕生日・性別など）は submit イベントより前、
   * publishボタンのクリック直後（デフォルト動作の一部）に走るため、
   * 写真チェックは submit ではなく click ハンドラの中で先に行い、
   * 不足していればそこで preventDefault してブラウザ標準検証に進ませない。 */

  const form = document.querySelector('.js-manage-form');
  if (form) {

    /* 写真エラーのインライン表示。
     * alert() は scrollIntoView(smooth) を中断させ、閉じた後に画面へ痕跡が
     * 残らないため使わない。メッセージ要素（role=alert）を写真枠の直後に
     * 出し、そこへスクロールする。 */
    const photoSlot = form.querySelector('.p-manage__photo--main');

    function showPhotoError() {
      if (!photoSlot) {
        return;
      }
      let msg = form.querySelector('.js-photo-error');
      if (!msg) {
        msg = document.createElement('p');
        msg.className = 'p-manage__field-error js-photo-error';
        msg.setAttribute('role', 'alert');
        msg.textContent = 'メイン写真が選ばれていません。「メイン写真を選ぶ」から写真を選んでください。';
        photoSlot.insertAdjacentElement('afterend', msg);
      }
      photoSlot.classList.add('is-error');
      photoSlot.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearPhotoError() {
      const msg = form.querySelector('.js-photo-error');
      if (msg) {
        msg.remove();
      }
      if (photoSlot) {
        photoSlot.classList.remove('is-error');
      }
    }

    if (photoSlot) {
      const mainInput = photoSlot.querySelector('.js-photo-input');
      if (mainInput) {
        mainInput.addEventListener('change', clearPhotoError);
      }
    }

    form.querySelectorAll('.js-submit').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        if (btn.name !== 'publish') {
          return;
        }
        const thumbInput = form.querySelector('input[name="thumb_id"]');
        const fileInput  = form.querySelector('input[name="photo_main"]');
        const hasPhoto   = (thumbInput && thumbInput.value && thumbInput.value !== '0') ||
                          (fileInput && fileInput.files && fileInput.files.length > 0);

        if (!hasPhoto) {
          e.preventDefault();
          showPhotoError();
        }
      });
    });

    // 二重送信防止（ここに到達＝写真チェック通過後、ブラウザ標準検証も通過した本当のsubmit）
    // 注意: submitハンドラ内で同期的に disabled にすると、送信データの
    // 構築（イベント処理の後に行われる）から押されたボタンの name=value
    // （publish=1 / save_draft=1）が除外され、公開が下書きになる。
    // setTimeout(0) で送信データ構築後に無効化する。
    form.addEventListener('submit', function () {
      setTimeout(function () {
        form.querySelectorAll('.js-submit').forEach(function (btn) {
          btn.disabled = true;
        });
      }, 0);
    });

    // ブラウザの標準検証で無効なフィールドにフォーカスが移っても、
    // スクロールが伴わないことがあるため、明示的にスクロールさせる。
    // 'invalid' イベントはバブリングしないため capture(true) で拾う。
    // 無効フィールドが複数あると1つずつ発火して scrollIntoView が競合し
    // 最後（一番下）の欄へ飛んでしまうため、1回の検証につき最初の1つ
    // だけにスクロールする。
    let invalidScrolled = false;
    form.addEventListener('invalid', function (e) {
      if (invalidScrolled) {
        return;
      }
      invalidScrolled = true;
      setTimeout(function () {
        invalidScrolled = false;
      }, 0);
      e.target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, true);
  }
})();