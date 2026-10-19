<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ウィジェット：ブログのカテゴリ一覧（記事数つき）
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */
if (empty($limit)) $limit = '0';
if (empty($depth)) $depth = 1;
$id = isset($blogContent)? $blogContent->id : $blog_content_id;
$data = $this->Blog->getViewVarsForBlogCategoryArchivesWdget($id, $limit, true, $depth, null);
$categories = $data['categories'];
$base = $this->BcBaser->getBlogContentsUrl($id, false) . 'archives/category/';
$here = rawurldecode($this->getRequest()->getPath());
?>
<?php if ($categories): ?>
	<section class="bt-widget bt-widget-blog-categories">
		<?php if ($name && $use_title): ?>
			<h2 class="bt-widget-title"><?= h($name) ?></h2>
		<?php endif ?>
		<div class="bt-widget-body">
			<ul class="bt-widget-list">
				<?php foreach ($categories as $category): ?>
					<?php $url = $base . rawurlencode($category->name) ?>
					<li>
						<a href="<?= h($this->BcBaser->getUrl($url)) ?>"<?= rawurldecode($url) === $here? ' aria-current="page"' : '' ?>>
							<span class="bt-w-title"><?= h($category->title) ?></span>
							<?php if (isset($category->count)): ?><span class="bt-count"><?= h($category->count) ?></span><?php endif ?>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</section>
<?php endif ?>
