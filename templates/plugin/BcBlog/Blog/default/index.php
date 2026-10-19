<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ブログの一覧（ブログのトップ・カテゴリ・タグ・作成者・年・月・日。archives.php からも読み込む）
 * - ブログのトップ：ブログ説明文（ブログ設定）を紹介文として出し、「タグから探す」（スタイルの設定 tagStrip）と新着記事
 * - アーカイブ：見出し（種類・名前・件数）と記事
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var \Cake\ORM\ResultSet $posts ブログ記事リスト
 * @var string $blogArchiveType
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$type = $blogArchiveType ?? null;
$kicker = '';
$title = '';
$hash = false;
if ($type === 'category') {
	$kicker = 'カテゴリ';
	$title = $blogCategory->title;
} elseif ($type === 'tag') {
	$kicker = 'タグ';
	$title = rawurldecode($blogTag->name);
	$hash = true;
} elseif ($type === 'author') {
	$kicker = '書いた人';
	$title = $author->getDisplayName();
} elseif ($type === 'daily') {
	$kicker = '日別アーカイブ';
	$title = sprintf('%s年%s月%s日', $year, (int)$month, (int)$day);
} elseif ($type === 'monthly') {
	$kicker = '月別アーカイブ';
	$title = sprintf('%s年%s月', $year, (int)$month);
} elseif ($type === 'yearly') {
	$kicker = '年別アーカイブ';
	$title = sprintf('%s年', $year);
}
if ($title !== '') {
	$this->BcBaser->setTitle($title);
}
if ($this->Blog->isHome()) {
	$this->BcBaser->setDescription($this->Blog->getDescription());
} else {
	$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->BcBaser->getContentsTitle() . 'の記事の一覧です。');
}
$this->BcBaser->setTableToUpload('BcBlog.BlogPosts');
$posts = $posts->toArray();
try {
	$count = (int)$this->Paginator->counter('{{count}}');
} catch (\Cake\Core\Exception\CakeException) {
	$count = count($posts);
}
$blogContentId = (int)$this->Blog->getCurrentBlogId();
?>

<?php if ($type === null): ?>
	<?php if ($this->Blog->descriptionExists()): ?>
		<div class="bt-intro"><?php $this->Blog->description() ?></div>
	<?php endif ?>
	<?php if ($this->BlogTheme->setting('tagStrip')): ?>
		<?php $this->BcBaser->element('BcBlog.tag_list', ['blogContentId' => $blogContentId, 'viewCount' => true, 'wrap' => 'strip', 'limit' => 12]) ?>
	<?php endif ?>
	<h1 class="bt-section-title"><?= h($this->Blog->getTitle()) ?></h1>
<?php else: ?>
	<header class="bt-list-head">
		<p class="bt-list-kicker"><?= h($kicker) ?></p>
		<h1 class="bt-list-title">
			<span><?php if ($hash): ?><span class="bt-hash">#</span><?php endif ?><?= h($title) ?><?= $hash? ' の記事' : '' ?></span>
			<span class="bt-list-count"><?= h($count) ?>件</span>
		</h1>
		<?php if ($type === 'category' && !empty($blogCategory->description)): ?>
			<p class="bt-list-desc"><?= h(strip_tags((string)$blogCategory->description)) ?></p>
		<?php endif ?>
	</header>
<?php endif ?>

<?php if ($posts): ?>
	<?php $this->BcBaser->element('BcBlog.post_list', ['posts' => $posts]) ?>
	<?php $this->BcBaser->pagination('simple') ?>
<?php else: ?>
	<div class="bt-empty">
		<p class="bt-empty-title">記事がありません</p>
		<p>タグから探してみてください。</p>
		<?php $this->BcBaser->element('BcBlog.tag_list', ['blogContentId' => $blogContentId, 'limit' => 8]) ?>
	</div>
<?php endif ?>
