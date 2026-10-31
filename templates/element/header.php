<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ヘッダー（サイト名またはロゴ・説明1行・ナビ・検索・ダークモード切り替え（テーマ設定で非表示にできる）・RSS）
 * - ロゴ：テーマ設定でロゴ画像を登録すると、サイト名の代わりに出る
 * - 説明1行：最初のブログの「ブログ説明文」（ブログ設定）の最初の1文
 * - 目立つボタン（資料請求など）：テーマ設定の「ヘッダーのボタン」→ スタイルの設定 headerCta。どちらも空なら出さない（BlogThemeHelper::headerCta()）
 * - ナビ：テーマ設定の「ヘッダーのナビ」で選ぶ（コンテンツ管理のルート直下／最初のブログのカテゴリ＋固定ページ）。BlogThemeHelper::navItems()
 * @var \BaserCore\View\BcFrontAppView $this
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
ob_start();
$this->BcBaser->logo(['link' => '']);
$logo = trim(ob_get_clean());

$desc = $this->BlogTheme->tagline();
$navItems = $this->BlogTheme->navItems();
$rss = $this->BlogTheme->rssUrl();
$siteName = $this->BlogTheme->siteTitle();
$headerCta = $this->BlogTheme->headerCta();
$tag = $this->BcBaser->isHome()? 'h1' : 'p';
?>
<header class="bt-header" id="bt-header">
	<div class="bt-container bt-header-top">
		<div class="bt-brand">
			<<?= $tag ?> class="bt-brand-name"><a href="<?= h($this->BcBaser->getUrl('/')) ?>"><?= $logo ?: h($siteName) ?></a></<?= $tag ?>>
			<?php if ($desc): ?>
				<p class="bt-brand-desc"><?= h($desc) ?></p>
			<?php endif ?>
		</div>
		<div class="bt-header-tools">
			<?php if ($rss): ?>
				<a class="bt-icon-btn bt-rss-link" href="<?= h($this->BcBaser->getUrl($rss)) ?>" aria-label="RSS フィード"><?= $this->BlogTheme->icon('rss') ?></a>
			<?php endif ?>
			<?php if ($this->BlogTheme->schemeToggle()): ?>
				<button class="bt-icon-btn bt-theme-toggle" type="button" aria-label="ダークモードに切り替え" aria-pressed="false">
					<?= $this->BlogTheme->icon('moon', 'bt-ico bt-i-moon') ?>
					<?= $this->BlogTheme->icon('sun', 'bt-ico bt-i-sun') ?>
				</button>
			<?php endif ?>
			<?php if ($headerCta): ?>
				<a class="bt-btn bt-header-cta" href="<?= h($this->BcBaser->getUrl($headerCta['url'])) ?>"><?= h($headerCta['label']) ?></a>
			<?php endif ?>
			<button class="bt-icon-btn bt-menu-btn" type="button" aria-expanded="false" aria-controls="bt-gnav" aria-label="メニューを開く">
				<?= $this->BlogTheme->icon('menu', 'bt-ico bt-i-menu') ?>
				<?= $this->BlogTheme->icon('close', 'bt-ico bt-i-close') ?>
			</button>
		</div>
	</div>
	<nav class="bt-gnav" id="bt-gnav" aria-label="メインメニュー">
		<div class="bt-container bt-gnav-inner">
			<ul class="bt-gnav-list">
				<?php foreach ($navItems as $item): ?>
					<li><a href="<?= h($this->BcBaser->getUrl($item['url'])) ?>"<?= $item['current']? ' aria-current="page"' : '' ?><?= $item['blank']? ' target="_blank" rel="noopener"' : '' ?>><?= h($item['title']) ?></a></li>
				<?php endforeach ?>
				<?php if ($rss): ?>
					<li class="bt-gnav-rss"><a href="<?= h($this->BcBaser->getUrl($rss)) ?>"><?= $this->BlogTheme->icon('rss') ?>RSS</a></li>
				<?php endif ?>
			</ul>
			<?php if (\Cake\Core\Plugin::isLoaded('BcSearchIndex')): ?>
				<form class="bt-search" role="search" action="<?= h($this->BcBaser->getUrl('/bc-search-index/search_indexes/search')) ?>" method="get">
					<label class="bt-visually-hidden" for="bt-q">サイト内を検索</label>
					<?= $this->BlogTheme->icon('search') ?>
					<input id="bt-q" name="q" type="search" placeholder="記事を検索" autocomplete="off" value="<?= h((string)$this->getRequest()->getQuery('q', '')) ?>">
				</form>
			<?php endif ?>
		</div>
	</nav>
</header>
