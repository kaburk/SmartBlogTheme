<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * レイアウト
 *
 * 呼出箇所：全ページ
 * - 本文の右にサイドバー（ウィジェットエリア）。記事の詳細では、サイドバーの最後に目次（single.php が btToc ブロックに書く）
 * - 配色：<html data-theme> を描画前に決める。配色モード（テーマ設定 → config/setting.php の colorScheme）と、
 *   切り替えボタンの有無（schemeToggle）で決まる
 *   - ボタンあり：閲覧者が選んだ配色（localStorage）→ 配色モード（auto なら OS の設定）
 *   - ボタンなし：配色モード（auto なら OS の設定）。閲覧者の保存値は使わない
 * @var \BaserCore\View\BcFrontAppView $this
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$variant = (string)$this->BlogTheme->setting('variant', 'clean');
$colorScheme = $this->BlogTheme->colorScheme();
$schemeToggle = $this->BlogTheme->schemeToggle();
$fonts = (string)$this->BlogTheme->setting('fonts', '');
?>
<!DOCTYPE html>
<html lang="<?= h($this->BcBaser->getHtmlLang()) ?>" data-variant="<?= h($variant) ?>" data-scheme-mode="<?= h($colorScheme) ?>" data-scheme-toggle="<?= $schemeToggle? '1' : '0' ?>">
<head>
	<?php $this->BcBaser->charset() ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>
	/* 配色を描画前に決めて、ちらつきを防ぐ（ボタンあり：保存値 → 配色モード／ボタンなし：配色モード。auto は OS の設定） */
	(function () {
		var d = document.documentElement, t = null, mode = d.getAttribute('data-scheme-mode');
		if (d.getAttribute('data-scheme-toggle') === '1') {
			try { t = localStorage.getItem('bt-theme'); } catch (e) {}
		}
		if (t !== 'light' && t !== 'dark') t = (mode === 'light' || mode === 'dark') ? mode : null;
		if (!t) t = window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
		d.setAttribute('data-theme', t);
	})();
	</script>
	<?php $this->BcBaser->title() ?>
	<?php $this->BcBaser->metaDescription() ?>
	<?php $this->BcBaser->metaKeywords() ?>
	<?php $this->BcBaser->icon() ?>
	<?php if ($this->BlogTheme->rssUrl()): ?>
		<link rel="alternate" type="application/rss+xml" title="<?= h($this->BlogTheme->siteTitle()) ?>" href="<?= h($this->BcBaser->getUri($this->BlogTheme->rssUrl())) ?>">
	<?php endif ?>
	<?php if ($fonts): ?>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="<?= h($fonts) ?>" rel="stylesheet">
	<?php endif ?>
	<?php $this->BcBaser->css(['base', 'variant']) ?>
	<?php /* BurgerEditor の公開側スクリプト（画像ポップアップなど）とブログのコメントは jQuery が必要 */ ?>
	<?php $this->BcBaser->js('vendor/jquery-3.7.1.min') ?>
	<?php /* bge_style.css とテーマ設定の色（/files/theme_configs/config.css）は scripts() の中で出力される */ ?>
	<?php $this->BcBaser->scripts() ?>
	<?php $this->BcBaser->googleAnalytics() ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>">

<?php $this->BcBaser->func() ?>

<div class="bt-site">
	<a class="bt-skip" href="#main">本文へ移動</a>
	<?php $this->BcBaser->element('icons') ?>

	<?php $this->BcBaser->header() ?>

	<div class="bt-container bt-layout">
		<main class="bt-main" id="main">
			<?php if (!$this->BcBaser->isHome()): ?>
				<?php $this->BcBaser->element('crumbs') ?>
			<?php endif ?>
			<?php $this->BcBaser->flash() ?>
			<?php $this->BcBaser->content() ?>
		</main>

		<aside class="bt-sidebar" aria-label="サイドバー">
			<?php $this->BcBaser->widgetArea() ?>
			<?= $this->fetch('btToc') ?>
		</aside>
	</div>

	<?php $this->BcBaser->footer() ?>
</div>

<?php $this->BcBaser->js('site', true, ['defer' => true]) ?>
</body>
</html>
