<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ブログのコメント1件（一覧と、送信後に Ajax で返す HTML の両方で使う）
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogComment $blogComment コメントデータ
 */
if (empty($blogComment) || !$blogComment->status) return;
?>
<div class="bt-comment" id="Comment<?= h($blogComment->no) ?>">
	<p class="bt-comment-head">
		<span class="bt-comment-name">
			<?php if ($blogComment->url): ?>
				<a href="<?= h($blogComment->url) ?>" target="_blank" rel="nofollow ugc noopener"><?= h($blogComment->name) ?></a>
			<?php else: ?>
				<?= h($blogComment->name) ?>
			<?php endif ?>
		</span>
		<?php if (!empty($blogComment->created)): ?>
			<time class="bt-comment-date" datetime="<?= h($blogComment->created->format('Y-m-d')) ?>"><?= h($blogComment->created->format('Y.m.d')) ?></time>
		<?php endif ?>
	</p>
	<p class="bt-comment-text"><?= nl2br($this->BcText->autoLinkUrls(h($blogComment->message), ['escape' => false])) ?></p>
</div>
