<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * フッター（サイト名・説明・ヘッダーと同じナビ・RSS・コピーライト）
 * @var \BaserCore\View\BcFrontAppView $this
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');

$desc = $this->BlogTheme->tagline();
$rss = $this->BlogTheme->rssUrl();
$siteName = $this->BlogTheme->siteTitle();
?>
<footer class="bt-footer">
	<div class="bt-container">
		<div class="bt-footer-inner">
			<div>
				<p class="bt-footer-name"><a href="<?= h($this->BcBaser->getUrl('/')) ?>"><?= h($siteName) ?></a></p>
				<?php if ($desc): ?>
					<p class="bt-footer-desc"><?= h($desc) ?></p>
				<?php endif ?>
			</div>
			<nav class="bt-footer-nav" aria-label="フッターメニュー">
				<ul>
					<?php foreach ($this->BlogTheme->navItems() as $item): ?>
						<li><a href="<?= h($this->BcBaser->getUrl($item['url'])) ?>"<?= $item['blank']? ' target="_blank" rel="noopener"' : '' ?>><?= h($item['title']) ?></a></li>
					<?php endforeach ?>
					<?php if ($rss): ?>
						<li><a href="<?= h($this->BcBaser->getUrl($rss)) ?>"><?= $this->BlogTheme->icon('rss') ?>RSS</a></li>
					<?php endif ?>
				</ul>
			</nav>
		</div>
		<p class="bt-copyright">© <?php $this->BcBaser->copyYear((int)date('Y')) ?> <?= h($siteName) ?></p>
	</div>
</footer>
