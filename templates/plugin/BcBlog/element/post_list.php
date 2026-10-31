<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * 記事のカードの一覧。スタイルの設定 listAdAfter 件目（初期値 4）のあとに、ウィジェットエリア「広告：一覧の途中」があれば出す
 * $this->BcBaser->element('BcBlog.post_list', ['posts' => $posts]) で呼び出す
 * @var \BaserCore\View\BcFrontAppView $this
 * @var iterable $posts
 * @var bool $ad 広告を出す（初期値 true）
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$ad = $ad ?? true;
$adHtml = $ad? $this->BlogTheme->namedWidgetArea('広告：一覧の途中') : '';
$posts = is_array($posts)? $posts : iterator_to_array($posts);
$adAfter = max(1, (int)$this->BlogTheme->setting('listAdAfter', 4));
?>
<ul class="bt-post-grid">
	<?php foreach (array_values($posts) as $i => $post): ?>
		<li><?php $this->BcBaser->element('BcBlog.post_card', ['post' => $post]) ?></li>
		<?php if ($i === $adAfter - 1 && $adHtml && count($posts) > $adAfter): ?>
			<li class="bt-ad-item"><?= $adHtml ?></li>
		<?php endif ?>
	<?php endforeach ?>
</ul>
