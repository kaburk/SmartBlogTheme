<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * 固定ページ
 * - トップページ：本文を紹介文として出し、その下に最初のブログの新着記事（ブログ設定の「一覧表示件数」）と「記事の一覧へ」
 *   スタイルの設定 tagStrip が true なら、紹介文の下に「タグから探す」
 * - それ以外：記事と同じ見た目の本文（目次・シェア・日付は出さない）
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BaserCore\Model\Entity\Page $page
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$this->BcBaser->setTableToUpload('BaserCore.Pages');
$contents = (string)$page->contents;
$hasBody = trim(strip_tags($contents, '<img><iframe>')) !== '';
?>
<?php if ($this->BcBaser->isHome()): ?>
	<?php
	$blog = $this->BlogTheme->mainBlog();
	$blogContent = $blog? \Cake\ORM\TableRegistry::getTableLocator()->get('BcBlog.BlogContents')->find()->where(['BlogContents.id' => $blog->entity_id])->first() : null;
	?>
	<?php if ($hasBody): ?>
		<div class="bt-intro"><?= $contents ?></div>
	<?php endif ?>
	<?php if ($blog): ?>
		<?php if ($this->BlogTheme->setting('tagStrip')): ?>
			<?php $this->BcBaser->element('BcBlog.tag_list', ['blogContentId' => (int)$blog->entity_id, 'viewCount' => true, 'wrap' => 'strip', 'limit' => 12]) ?>
		<?php endif ?>
		<h2 class="bt-section-title">新着記事</h2>
		<?php $this->BcBaser->blogPosts($blog->name, (int)($blogContent->list_count ?? 10)) ?>
		<p class="bt-more"><a class="bt-btn" href="<?= h($this->BcBaser->getUrl($this->BcBaser->getContentsUrl($blog->url, false, null, false))) ?>">記事の一覧へ</a></p>
	<?php endif ?>
<?php else: ?>
	<article class="bt-article">
		<div class="bt-article-inner">
			<header class="bt-article-head">
				<h1 class="bt-article-title"><?php $this->BcBaser->contentsTitle() ?></h1>
			</header>
			<div class="bt-post-body">
				<?= $contents ?>
			</div>
		</div>
	</article>
<?php endif ?>
