<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * トップのまとまり（スタイルの設定 topLayout が sections のとき、固定ページ index から呼ぶ）
 * $this->BcBaser->element('BcBlog.top_sections') で呼び出す
 * - ピックアップ：最初のブログの新着3件（カード3列）
 * - カテゴリごとのまとまり：第1階層のカテゴリごとに、新着4件（小さいカード4列）と「もっと見る」。記事のないカテゴリは出さない
 * - 2つ目のまとまりのあとに、ウィジェットエリア「トップ：CTA」（資料請求の帯など。空なら何も出さない）
 * @var \BaserCore\View\BcFrontAppView $this
 */
$this->loadHelper('BcBlog.Blog');
$this->loadHelper('SmartBlogTheme.BlogTheme');
$blog = $this->BlogTheme->mainBlog();
if (!$blog) {
	return;
}
$base = $this->BcBaser->getContentsUrl($blog->url, false, null, false);
$pickup = $this->Blog->getPosts($blog->name, 3)->all()->toArray();
$sections = [];
foreach ($this->Blog->getCategories(['blogContentId' => $blog->entity_id, 'depth' => 1]) as $category) {
	$posts = $this->Blog->getPosts($blog->name, 4, ['category' => $category->name])->all()->toArray();
	if ($posts) {
		$sections[] = ['category' => $category, 'posts' => $posts];
	}
}
$cta = $this->BlogTheme->namedWidgetArea('トップ：CTA', 'bt-cta-area', null);
?>
<?php if ($pickup): ?>
	<section class="bt-top-sec bt-top-pickup" aria-labelledby="bt-top-pickup">
		<div class="bt-sec-head">
			<h2 class="bt-section-title" id="bt-top-pickup">ピックアップ</h2>
		</div>
		<ul class="bt-post-grid bt-post-grid--3">
			<?php foreach ($pickup as $post): ?>
				<li><?php $this->BcBaser->element('BcBlog.post_card', ['post' => $post]) ?></li>
			<?php endforeach ?>
		</ul>
	</section>
<?php endif ?>
<?php foreach ($sections as $i => $section): ?>
	<section class="bt-top-sec bt-top-cat" aria-labelledby="bt-top-cat-<?= $i ?>">
		<div class="bt-sec-head">
			<h2 class="bt-section-title" id="bt-top-cat-<?= $i ?>"><?= h($section['category']->title) ?></h2>
			<a class="bt-sec-more" href="<?= h($this->BcBaser->getUrl($base . 'archives/category/' . rawurlencode($section['category']->name))) ?>">もっと見る<?= $this->BlogTheme->icon('right') ?></a>
		</div>
		<ul class="bt-post-grid bt-post-grid--4">
			<?php foreach ($section['posts'] as $post): ?>
				<li><?php $this->BcBaser->element('BcBlog.post_card', ['post' => $post, 'small' => true]) ?></li>
			<?php endforeach ?>
		</ul>
	</section>
	<?php if ($i === 1 && $cta): ?>
		<?= $cta ?>
	<?php endif ?>
<?php endforeach ?>
<?php if (count($sections) < 2 && $cta): ?>
	<?= $cta ?>
<?php endif ?>
