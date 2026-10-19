<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * 記事のカード（一覧・関連記事で共通）
 * $this->BcBaser->element('BcBlog.post_card', ['post' => $post, 'small' => false]) で呼び出す
 * - アイキャッチがない記事は「メインを薄めた背景＋カテゴリ名」の枠になる
 * - small：関連記事の小さいカード（抜粋・タグなし）
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $post
 * @var bool $small
 */
$this->loadHelper('BcBlog.Blog');
$this->loadHelper('SmartBlogTheme.BlogTheme');
$small = !empty($small);
$url = $this->Blog->getPostLinkUrl($post, false);
$categoryUrl = $this->BlogTheme->categoryUrl($post);
$tags = (!$small && !empty($post->blog_content->tag_use) && !empty($post->blog_tags))? array_slice($post->blog_tags, 0, 3) : [];
?>
<article class="bt-card">
	<div class="bt-card-thumb"><?= $this->BlogTheme->thumb($post, $small? 'thumb' : 'default') ?></div>
	<div class="bt-card-body">
		<?php if ($categoryUrl): ?>
			<a class="bt-card-cat" href="<?= h($this->BcBaser->getUrl($categoryUrl)) ?>"><?= h($post->blog_category->title) ?></a>
		<?php endif ?>
		<h3 class="bt-card-title"><a href="<?= h($this->BcBaser->getUrl($url)) ?>"><?= h($post->title) ?></a></h3>
		<?php if (!$small): ?>
			<p class="bt-card-excerpt"><?= h($this->BlogTheme->excerpt($post)) ?></p>
		<?php endif ?>
		<div class="bt-card-foot">
			<?php if ($tags): ?>
				<ul class="bt-tag-list" aria-label="タグ">
					<?php foreach ($tags as $tag): ?>
						<li><a class="bt-tag" href="<?= h($this->BcBaser->getUrl($this->BlogTheme->tagUrl($post, $tag))) ?>"><?= h($tag->name) ?></a></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
			<p class="bt-card-meta">
				<span><?= $this->BlogTheme->icon('calendar') ?><time datetime="<?= h($post->posted?->format('Y-m-d')) ?>"><?= h($post->posted?->format('Y.m.d')) ?></time></span>
				<span><?= $this->BlogTheme->icon('clock') ?>約<?= $this->BlogTheme->readingMinutes($post) ?>分</span>
			</p>
		</div>
	</div>
</article>
