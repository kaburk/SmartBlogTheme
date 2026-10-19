<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * パーツ用の記事一覧（BcBaserHelper::blogPosts() で呼び出す。トップページなどで使う）
 * @var \BaserCore\View\BcFrontAppView $this
 * @var mixed $posts ブログ記事リスト
 */
$posts = is_array($posts)? $posts : $posts->toArray();
?>
<?php if ($posts): ?>
	<?php $this->BcBaser->element('BcBlog.post_list', ['posts' => $posts]) ?>
<?php else: ?>
	<p class="bt-empty">記事がありません。</p>
<?php endif ?>
