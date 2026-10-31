<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ブログの記事の詳細
 * - 記事の上：カテゴリ・タイトル・投稿日・更新日（投稿日と違う日のとき）・読了時間・タグ・書いた人、アイキャッチ
 * - 本文の上下：シェア（上はスタイルの設定 shareTop が true のとき）、ウィジェットエリア「広告：記事の上」「広告：記事の下」
 * - 目次：本文の h2・h3 から site.js が作る（PC はサイドバーの最後、スマホは本文の上で開閉）。スタイルの設定 toc が false なら出さない
 * - 本文の下：ウィジェットエリア「記事の下：CTA」（資料請求の箱など）、「記事の下：著者」、前後の記事、関連記事（同じタグの記事 3件）、コメント
 * - 本文の上の目次は、スタイルの設定 tocOpen が true なら開いた状態で出す
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $post ブログ記事データ
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$this->BcBaser->setDescription($this->Blog->getPostContent($post, false, false, 100));
$this->BcBaser->setTableToUpload('BcBlog.BlogPosts');
$this->BcBaser->setTitle($post->title);

$categoryUrl = $this->BlogTheme->categoryUrl($post);
$tags = (!empty($post->blog_content->tag_use) && !empty($post->blog_tags))? $post->blog_tags : [];
$author = $this->BlogTheme->authorName($post);
$eyeCatch = $this->Blog->getEyeCatch($post, ['link' => false, 'imgsize' => 'default', 'class' => 'bt-eyecatch-img', 'alt' => '']);
$share = $this->BlogTheme->shareLinks($this->BcBaser->getUri($this->Blog->getPostLinkUrl($post, false)), $post->title);
$prev = $this->Blog->getPrevPost($post);
$next = $this->Blog->getNextPost($post);
$related = $this->Blog->getRelatedPosts($post, ['limit' => 3]);
if (is_object($related)) {
	/* 関連記事のクエリはカテゴリを含まないので足す（カードのカテゴリ名とアイキャッチなしの枠に使う） */
	$related = $related->contain(['BlogCategories', 'BlogContents' => ['Contents']])->all()->toArray();
}

ob_start();
?>
<div class="bt-share">
	<span class="bt-share-label">シェア</span>
	<ul>
		<?php foreach ($share as $link): ?>
			<li><a href="<?= h($link['key'] === 'rss'? $this->BcBaser->getUrl($link['url']) : $link['url']) ?>"<?= $link['key'] === 'rss'? '' : ' target="_blank" rel="noopener nofollow"' ?>><?= $this->BlogTheme->icon($link['key']) ?><?= h($link['label']) ?></a></li>
		<?php endforeach ?>
	</ul>
</div>
<?php
$shareHtml = ob_get_clean();
$toc = (bool)$this->BlogTheme->setting('toc', true);

/* サイドバーの目次（layout の btToc ブロック） */
if ($toc):
$this->start('btToc');
?>
<section class="bt-widget bt-toc-widget" data-bt-toc-box hidden>
	<h2 class="bt-widget-title">目次</h2>
	<div class="bt-widget-body">
		<nav class="bt-toc" aria-label="目次"><ol data-bt-toc></ol></nav>
	</div>
</section>
<?php
$this->end();
endif;
?>

<article class="bt-article">
	<div class="bt-article-inner">
		<header class="bt-article-head">
			<?php if ($categoryUrl): ?>
				<a class="bt-article-cat" href="<?= h($this->BcBaser->getUrl($categoryUrl)) ?>"><?= h($post->blog_category->title) ?></a>
			<?php endif ?>
			<h1 class="bt-article-title"><?= h($post->title) ?></h1>
			<p class="bt-article-meta">
				<span><?= $this->BlogTheme->icon('calendar') ?>投稿 <time datetime="<?= h($post->posted?->format('Y-m-d')) ?>"><?= h($this->BlogTheme->dateText($post->posted)) ?></time></span>
				<?php if ($this->BlogTheme->isUpdated($post)): ?>
					<span><?= $this->BlogTheme->icon('refresh') ?>更新 <time datetime="<?= h($post->modified->format('Y-m-d')) ?>"><?= h($this->BlogTheme->dateText($post->modified)) ?></time></span>
				<?php endif ?>
				<span><?= $this->BlogTheme->icon('clock') ?>約<?= $this->BlogTheme->readingMinutes($post) ?>分で読めます</span>
			</p>
			<?php if ($tags): ?>
				<ul class="bt-tag-list bt-article-tags" aria-label="タグ">
					<?php foreach ($tags as $tag): ?>
						<li><a class="bt-tag" href="<?= h($this->BcBaser->getUrl($this->BlogTheme->tagUrl($post, $tag))) ?>"><?= h($tag->name) ?></a></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
			<?php if ($author): ?>
				<div class="bt-byline">
					<span class="bt-avatar" aria-hidden="true"><?= h(mb_substr($author, 0, 1)) ?></span>
					<span>書いた人 <strong><?= h($author) ?></strong></span>
				</div>
			<?php endif ?>
		</header>

		<?php if ($eyeCatch): ?>
			<figure class="bt-eyecatch"><?= $eyeCatch ?></figure>
		<?php endif ?>

		<?php if ($this->BlogTheme->setting('shareTop', true)): ?>
			<?= $shareHtml ?>
		<?php endif ?>
		<?= $this->BlogTheme->namedWidgetArea('広告：記事の上') ?>

		<?php if ($toc): ?>
			<details class="bt-toc-sp"<?= $this->BlogTheme->setting('tocOpen')? ' open' : '' ?> data-bt-toc-box hidden>
				<summary>目次<?= $this->BlogTheme->icon('chevron') ?></summary>
				<nav class="bt-toc" aria-label="目次"><ol data-bt-toc></ol></nav>
			</details>
		<?php endif ?>

		<div class="bt-post-body" data-bt-post-body>
			<?php $this->BcBaser->blogPostContent($post) ?>
		</div>

		<?= $this->BlogTheme->namedWidgetArea('記事の下：CTA', 'bt-cta-area', null) ?>
		<?= $this->BlogTheme->namedWidgetArea('広告：記事の下') ?>
		<?= $shareHtml ?>

		<?= $this->BlogTheme->namedWidgetArea('記事の下：著者', 'bt-author-area', null) ?>

		<?php if ($prev || $next): ?>
			<nav class="bt-post-nav" aria-label="前後の記事">
				<?php if ($prev): ?>
					<a class="bt-prev" href="<?= h($this->BcBaser->getUrl($this->Blog->getPostLinkUrl($prev, false))) ?>"><span class="bt-label"><?= $this->BlogTheme->icon('left') ?>前の記事</span><span class="bt-t"><?= h($prev->title) ?></span></a>
				<?php endif ?>
				<?php if ($next): ?>
					<a class="bt-next" href="<?= h($this->BcBaser->getUrl($this->Blog->getPostLinkUrl($next, false))) ?>"><span class="bt-label">次の記事<?= $this->BlogTheme->icon('right') ?></span><span class="bt-t"><?= h($next->title) ?></span></a>
				<?php endif ?>
			</nav>
		<?php endif ?>
	</div>
</article>

<?php if ($related): ?>
	<section class="bt-block bt-related" aria-labelledby="bt-related-heading">
		<h2 class="bt-section-title" id="bt-related-heading">関連記事</h2>
		<ul class="bt-post-grid bt-post-grid--small">
			<?php foreach ($related as $relatedPost): ?>
				<li><?php $this->BcBaser->element('BcBlog.post_card', ['post' => $relatedPost, 'small' => true]) ?></li>
			<?php endforeach ?>
		</ul>
	</section>
<?php endif ?>

<?php $this->BcBaser->element('blog_comments') ?>
