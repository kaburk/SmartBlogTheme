<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ウィジェット：ブログの月別アーカイブ（「記事数を表示」で件数つき）
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */
if (!isset($view_count)) $view_count = false;
if (!isset($limit)) $limit = 12;
$id = isset($blogContent)? $blogContent->id : $blog_content_id;
$data = $this->Blog->getViewVarsBlogMonthlyArchivesWidget($id, $limit, (bool)$view_count);
$postedDates = $data['postedDates'];
$base = $this->BcBaser->getBlogContentsUrl($id, false) . 'archives/date/';
$here = $this->getRequest()->getPath();
?>
<?php if (!empty($postedDates)): ?>
	<section class="bt-widget bt-widget-blog-monthly">
		<?php if ($name && $use_title): ?>
			<h2 class="bt-widget-title"><?= h($name) ?></h2>
		<?php endif ?>
		<div class="bt-widget-body">
			<ul class="bt-widget-list">
				<?php foreach ($postedDates as $postedDate): ?>
					<?php $url = $base . $postedDate['year'] . '/' . $postedDate['month'] ?>
					<li>
						<a href="<?= h($this->BcBaser->getUrl($url)) ?>"<?= $here === $url? ' aria-current="page"' : '' ?>>
							<span class="bt-w-title"><?= h((int)$postedDate['year'] . '年' . (int)$postedDate['month'] . '月') ?></span>
							<?php if ($view_count): ?><span class="bt-count"><?= h($postedDate['count']) ?></span><?php endif ?>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</section>
<?php endif ?>
