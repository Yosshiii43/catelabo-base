/* catelabo-base 文節折返し（BudouX）
 * 再ビルド手順（npmでbudouxとesbuildを導入して）:
 *   esbuild assets/js/src/phrase-wrap-entry.js --bundle --minify --format=iife
 *     --target=es2017 --outfile=assets/js/phrase-wrap.min.js
 *     --banner:js="/*! BudouX (c) Google LLC, Apache-2.0 ... *\/"
 * word-break: auto-phrase 非対応ブラウザ（Safari等）向けのポリフィル的適用。
 * 対応ブラウザ（Chromium）ではネイティブ実装に任せて何もしない。 */
import { loadDefaultJapaneseParser } from 'budoux';

(function () {
	'use strict';

	// ネイティブの文節折返しがあるならJSは不要
	if (window.CSS && CSS.supports && CSS.supports('word-break', 'auto-phrase')) {
		return;
	}

	// 適用対象（増やすときはここに追記）
	const SELECTORS = [
		'.p-cta__title',
		'.p-cta__text',
	];

	const apply = function () {
		let parser;
		try {
			parser = loadDefaultJapaneseParser();
		} catch (e) {
			return; // モデル読込失敗時は通常折返しのまま
		}
		document.querySelectorAll(SELECTORS.join(',')).forEach(function (el) {
			parser.applyToElement(el);
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', apply);
	} else {
		apply();
	}
})();
