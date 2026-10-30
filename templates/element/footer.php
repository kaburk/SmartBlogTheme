<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * フッター（サイト名・説明・ヘッダーと同じナビ・RSS・コピーライト）
 * - ウィジェットエリア「フッター：プロフィール」に中身があれば、左にそれを出し、サイト名はナビの上に出す（エッセイ向け）
 * @var \BaserCore\View\BcFrontAppView $this
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');

$desc = $this->BlogTheme->tagline();
$rss = $this->BlogTheme->rssUrl();
$siteName = $this->BlogTheme->siteTitle();
$profile = $this->BlogTheme->namedWidgetArea('フッター：プロフィール', 'bt-footer-profile', null);
?>
<footer class="bt-footer">
	<div class="bt-container">
		<div class="bt-footer-inner">
			<?php if ($profile): ?>
				<?= $profile ?>
			<?php else: ?>
				<div>
					<p class="bt-footer-name"><a href="<?= h($this->BcBaser->getUrl('/')) ?>"><?= h($siteName) ?></a></p>
					<?php if ($desc): ?>
						<p class="bt-footer-desc"><?= h($desc) ?></p>
					<?php endif ?>
				</div>
			<?php endif ?>
			<nav class="bt-footer-nav" aria-label="フッターメニュー">
				<?php if ($profile): ?>
					<p class="bt-footer-name"><a href="<?= h($this->BcBaser->getUrl('/')) ?>"><?= h($siteName) ?></a></p>
				<?php endif ?>
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
