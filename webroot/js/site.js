/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * テーマの動き（jQuery 不要）
 * - ダークモードの切り替え（localStorage に保存。配色モードが auto で保存がなければ OS の変更に追従）
 * - スマホのメニュー
 * - 目次：本文（[data-bt-post-body]）の h2・h3 から作る。見出しが2つ以上あるときだけ出す。いま読んでいる見出しを強調
 * - 素の <pre>（通常のエディタの本文・Markdown ブロック）にコピーボタンを付ける
 * - メールフォームの確認画面で、値のない行（送信しない項目など）を隠す
 *   （BgeAddonSyntaxHighlight のコードブロックは、プラグインのボタンを使う）
 */
(function () {
	'use strict';
	var root = document.documentElement;
	var KEY = 'bt-theme';

	/* ---------- ダークモード ---------- */
	var toggle = document.querySelector('.bt-theme-toggle');
	function applyTheme(t) {
		root.setAttribute('data-theme', t);
		if (!toggle) return;
		var dark = t === 'dark';
		toggle.setAttribute('aria-pressed', String(dark));
		toggle.setAttribute('aria-label', dark ? 'ライトモードに切り替え' : 'ダークモードに切り替え');
	}
	applyTheme(root.getAttribute('data-theme') || 'light');
	if (toggle) {
		toggle.addEventListener('click', function () {
			var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
			applyTheme(next);
			try { localStorage.setItem(KEY, next); } catch (e) {}
		});
	}
	/* 配色モードが auto のとき、OS の設定の変更に追従する（閲覧者が切り替えボタンで選んでいるときを除く） */
	if (window.matchMedia && root.getAttribute('data-scheme-mode') === 'auto') {
		var mq = matchMedia('(prefers-color-scheme: dark)');
		var onChange = function (e) {
			var saved = null;
			if (toggle) {
				try { saved = localStorage.getItem(KEY); } catch (err) {}
			}
			if (!saved) applyTheme(e.matches ? 'dark' : 'light');
		};
		if (mq.addEventListener) mq.addEventListener('change', onChange);
	}

	/* ---------- スマホのメニュー ---------- */
	var header = document.getElementById('bt-header');
	var menuBtn = header ? header.querySelector('.bt-menu-btn') : null;
	function setMenu(open) {
		header.classList.toggle('is-open', open);
		menuBtn.setAttribute('aria-expanded', String(open));
		menuBtn.setAttribute('aria-label', open ? 'メニューを閉じる' : 'メニューを開く');
	}
	if (menuBtn) {
		menuBtn.addEventListener('click', function () { setMenu(!header.classList.contains('is-open')); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && header.classList.contains('is-open')) { setMenu(false); menuBtn.focus(); }
		});
	}

	/* ---------- 素の <pre> のコピーボタン ---------- */
	function copyText(text, done) {
		function fallback() {
			var ta = document.createElement('textarea');
			ta.value = text; ta.setAttribute('readonly', ''); ta.style.position = 'fixed'; ta.style.opacity = '0';
			document.body.appendChild(ta); ta.select();
			try { document.execCommand('copy'); } catch (err) {}
			document.body.removeChild(ta); done();
		}
		if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(text).then(done, fallback);
		else fallback();
	}
	function addCopyButtons(body) {
		body.querySelectorAll('pre').forEach(function (pre) {
			if (pre.closest('.bge-sh-wrapper') || pre.parentNode.classList.contains('bt-pre-wrap')) return;
			var wrap = document.createElement('div');
			wrap.className = 'bt-pre-wrap';
			pre.parentNode.insertBefore(wrap, pre);
			wrap.appendChild(pre);
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'bt-copy-btn';
			btn.textContent = 'Copy';
			btn.setAttribute('aria-live', 'polite');
			btn.addEventListener('click', function () {
				copyText(pre.innerText, function () {
					btn.textContent = 'Copied!';
					clearTimeout(btn._t);
					btn._t = setTimeout(function () { btn.textContent = 'Copy'; }, 1600);
				});
			});
			wrap.appendChild(btn);
		});
	}

	/* ---------- 目次 ---------- */
	function buildToc(body) {
		var heads = Array.prototype.slice.call(body.querySelectorAll('h2, h3')).filter(function (h) {
			return h.textContent.trim() !== '';
		});
		if (heads.length < 2) return;
		heads.forEach(function (h, i) { if (!h.id) h.id = 'bt-sec-' + (i + 1); });
		var links = [];
		document.querySelectorAll('[data-bt-toc]').forEach(function (ol) {
			ol.textContent = '';
			heads.forEach(function (h) {
				var li = document.createElement('li');
				li.className = h.tagName === 'H3' ? 'bt-toc-h3' : 'bt-toc-h2';
				var a = document.createElement('a');
				a.href = '#' + h.id;
				a.textContent = h.textContent.trim();
				a.addEventListener('click', function (e) {
					e.preventDefault();
					h.scrollIntoView({ block: 'start' });
					if (history.replaceState) history.replaceState(null, '', '#' + h.id);
					var d = ol.closest('details'); if (d) d.open = false;
				});
				li.appendChild(a); ol.appendChild(li); links.push(a);
			});
		});
		document.querySelectorAll('[data-bt-toc-box]').forEach(function (box) { box.hidden = false; });

		var ticking = false;
		function spy() {
			ticking = false;
			var current = heads[0];
			for (var i = 0; i < heads.length; i++) {
				if (heads[i].getBoundingClientRect().top < 120) current = heads[i]; else break;
			}
			links.forEach(function (a) {
				var on = a.getAttribute('href') === '#' + current.id;
				a.classList.toggle('is-active', on);
				if (on) a.setAttribute('aria-current', 'location'); else a.removeAttribute('aria-current');
			});
		}
		window.addEventListener('scroll', function () {
			if (!ticking) { ticking = true; requestAnimationFrame(spy); }
		}, { passive: true });
		spy();
	}

	/* ---------- メールフォームの確認画面：値のない行を隠す ----------
	 * 「送信しない」（no_send）の項目（個人情報の取り扱いへの同意など）は、確認画面では hidden の値だけになり、
	 * 見出しだけの空の行が出る。値の文字がない行は表示だけ隠す（hidden の値は送信される）。空欄で送った任意の項目も隠れる */
	document.querySelectorAll('.bt-form.is-confirm .bs-mail-form-body tr').forEach(function (tr) {
		var td = tr.querySelector('td.col-input');
		if (td && td.textContent.replace(/\u00a0/g, '').trim() === '') tr.hidden = true;
	});

	function init() {
		var body = document.querySelector('[data-bt-post-body]');
		if (!body) return;
		/* Markdown ブロックは DOMContentLoaded で描画されるので、その後に作る */
		requestAnimationFrame(function () {
			addCopyButtons(body);
			buildToc(body);
		});
	}
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
	else init();
})();
