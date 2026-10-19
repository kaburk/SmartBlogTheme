<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * タグの一覧（サイドバーのウィジェット／トップの「タグから探す」／0件のときの案内）
 * $this->BcBaser->element('BcBlog.tag_list', [...]) で呼び出す
 * @var \BaserCore\View\BcFrontAppView $this
 * @var int $blogContentId ブログコンテンツID
 * @var string $heading 見出し（空なら出さない。strip は「タグから探す」）
 *   ※ 引数の名前を title にすると、ビューの変数（ページのタイトル）とぶつかるので使わない
 * @var bool $viewCount 記事数を出す
 * @var string $wrap widget（サイドバーの箱）／strip（タグから探す）／none（リストだけ）
 * @var int $limit 出す数（0 = すべて。記事数の多い順）
 */
$this->loadHelper('BcBlog.Blog');
$viewCount = !empty($viewCount);
$wrap = $wrap ?? 'none';
$limit = (int)($limit ?? 0);
$heading = $heading ?? '';
if (empty($blogContentId)) {
	return;
}
// 記事数つきのタグ（BlogHelper::getTagList の postCount はほかのプラグインと衝突することがあるので、テーマで数える）
$this->loadHelper('SmartBlogTheme.BlogTheme');
$tags = $this->BlogTheme->tagCounts((int)$blogContentId);
if ($limit) {
	usort($tags, fn($a, $b) => (int)$b->post_count <=> (int)$a->post_count);
	$tags = array_slice($tags, 0, $limit);
}
if (!$tags) {
	return;
}
$base = $this->BcBaser->getBlogContentsUrl((int)$blogContentId, false) . 'archives/tag/';
ob_start();
?>
<ul class="bt-tag-list">
	<?php foreach ($tags as $tag): ?>
		<li><a class="bt-tag" href="<?= h($this->BcBaser->getUrl($base . rawurlencode($tag->name))) ?>"><?= h($tag->name) ?><?php if ($viewCount): ?><span class="bt-count"><?= h($tag->post_count) ?></span><?php endif ?></a></li>
	<?php endforeach ?>
</ul>
<?php
$list = ob_get_clean();
?>
<?php if ($wrap === 'widget'): ?>
	<section class="bt-widget bt-widget-blog-tags">
		<?php if ($heading): ?><h2 class="bt-widget-title"><?= h($heading) ?></h2><?php endif ?>
		<div class="bt-widget-body"><?= $list ?></div>
	</section>
<?php elseif ($wrap === 'strip'): ?>
	<section class="bt-tag-strip" aria-labelledby="bt-tag-strip-title">
		<h2 class="bt-tag-strip-title" id="bt-tag-strip-title"><?= h($heading ?: 'タグから探す') ?></h2>
		<?= $list ?>
	</section>
<?php else: ?>
	<?= $list ?>
<?php endif ?>
