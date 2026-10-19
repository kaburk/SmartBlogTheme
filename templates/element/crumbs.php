<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * パンくず（構造化データつき）
 * @var \BaserCore\View\BcFrontAppView $this
 */
$crumbs = $this->BcBaser->getCrumbs();
if (!$crumbs) {
	return;
}
$position = 1;
?>
<nav class="bt-breadcrumb" aria-label="パンくずリスト">
	<ol itemscope itemtype="https://schema.org/BreadcrumbList">
		<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
			<a itemprop="item" href="<?= h($this->BcBaser->getUrl('/')) ?>"><span itemprop="name">ホーム</span></a>
			<meta itemprop="position" content="<?= $position++ ?>">
		</li>
		<?php foreach ($crumbs as $i => $crumb): ?>
			<?php if (empty($crumb['name'])) continue ?>
			<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<?php if ($i === array_key_last($crumbs) || empty($crumb['url'])): ?>
					<span itemprop="name" aria-current="page"><?= h($crumb['name']) ?></span>
				<?php else: ?>
					<a itemprop="item" href="<?= h($this->BcBaser->getUrl($crumb['url'])) ?>"><span itemprop="name"><?= h($crumb['name']) ?></span></a>
				<?php endif ?>
				<meta itemprop="position" content="<?= $position++ ?>">
			</li>
		<?php endforeach ?>
	</ol>
</nav>
