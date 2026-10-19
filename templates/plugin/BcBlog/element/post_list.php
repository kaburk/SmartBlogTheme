<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * 記事のカードの一覧。4件目のあとに、ウィジェットエリア「広告：一覧の途中」があれば出す
 * $this->BcBaser->element('BcBlog.post_list', ['posts' => $posts]) で呼び出す
 * @var \BaserCore\View\BcFrontAppView $this
 * @var iterable $posts
 * @var bool $ad 広告を出す（初期値 true）
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$ad = $ad ?? true;
$adHtml = $ad? $this->BlogTheme->namedWidgetArea('広告：一覧の途中') : '';
$posts = is_array($posts)? $posts : iterator_to_array($posts);
?>
<ul class="bt-post-grid">
	<?php foreach (array_values($posts) as $i => $post): ?>
		<li><?php $this->BcBaser->element('BcBlog.post_card', ['post' => $post]) ?></li>
		<?php if ($i === 3 && $adHtml && count($posts) > 4): ?>
			<li class="bt-ad-item"><?= $adHtml ?></li>
		<?php endif ?>
	<?php endforeach ?>
</ul>
