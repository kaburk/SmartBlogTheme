<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ウィジェット：ブログの最近の投稿（タイトル＋日付）
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var int $blog_content_id ブログコンテンツID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 */
if (!isset($count)) $count = 5;
$id = isset($blogContent)? $blogContent->id : $blog_content_id;
$data = $this->Blog->getViewVarsRecentEntriesWidget($id, (int)$count);
$recentEntries = $data['recentEntries'];
$here = $this->getRequest()->getPath();
?>
<?php if ($recentEntries && count($recentEntries)): ?>
	<section class="bt-widget bt-widget-blog-recent">
		<?php if ($name && $use_title): ?>
			<h2 class="bt-widget-title"><?= h($name) ?></h2>
		<?php endif ?>
		<div class="bt-widget-body">
			<ul class="bt-widget-list bt-recent-list">
				<?php foreach ($recentEntries as $entry): ?>
					<?php $url = $this->Blog->getPostLinkUrl($entry, false) ?>
					<li>
						<a href="<?= h($this->BcBaser->getUrl($url)) ?>"<?= $here === $url? ' aria-current="page"' : '' ?>>
							<span class="bt-w-title"><?= h($entry->title) ?></span>
							<time datetime="<?= h($entry->posted?->format('Y-m-d')) ?>"><?= h($entry->posted?->format('Y.m.d')) ?></time>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</section>
<?php endif ?>
