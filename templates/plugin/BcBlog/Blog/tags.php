<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * すべてのブログを横断したタグの記事の一覧（/<ブログ>/tags/<タグ名>）
 * - 見出しと記事のカードは、ブログの一覧（Blog/default/index.php）と同じ見た目
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var \Cake\ORM\ResultSet $posts
 * @var string $tag
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$this->BcBaser->setTitle($tag);
$this->BcBaser->setDescription(sprintf('%s の記事の一覧です。', $tag));
$this->BcBaser->setTableToUpload('BcBlog.BlogPosts');
$posts = $posts->toArray();
try {
	$count = (int)$this->Paginator->counter('{{count}}');
} catch (\Cake\Core\Exception\CakeException) {
	$count = count($posts);
}
?>

<header class="bt-list-head">
	<p class="bt-list-kicker">タグ</p>
	<h1 class="bt-list-title">
		<span><span class="bt-hash">#</span><?= h($tag) ?> の記事</span>
		<span class="bt-list-count"><?= h($count) ?>件</span>
	</h1>
</header>

<?php if ($posts): ?>
	<?php $this->BcBaser->element('BcBlog.post_list', ['posts' => $posts]) ?>
	<?php $this->BcBaser->pagination('simple') ?>
<?php else: ?>
	<div class="bt-empty">
		<p class="bt-empty-title">記事がありません</p>
	</div>
<?php endif ?>
