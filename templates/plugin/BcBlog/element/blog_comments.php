<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ブログのコメント（一覧と投稿フォーム）。ブログ設定で「コメント受付」が有効なときだけ出る
 * - 送信は BcBlog の blog_comment.bundle.js（jQuery・common.bundle が必要）。id（BlogCommentAddForm・BlogCommentList・
 *   BlogCommentAddButton・ResultMessage・AuthCaptchaImage・CaptchaLoader）とフォームの name・id は変えない
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent ブログコンテンツデータ
 * @var \BcBlog\Model\Entity\BlogPost $post
 */
if (!$blogContent->comment_use) return;
$captchaId = mt_rand(0, 99999999);
$this->BcBaser->i18nScript([
	'alertMessageName' => __d('baser_core', 'お名前を入力してください'),
	'alertMessageEmail' => __d('baser_core', 'メールアドレスを入力してください'),
	'alertMessageComment' => __d('baser_core', 'コメントを入力してください'),
	'alertMessageAuthImage' => __d('baser_core', '画像の文字を入力してください'),
	'alertMessageAuthComplete' => __d('baser_core', '送信が完了しました。送信された内容は確認後公開させて頂きます。'),
	'alertMessageError' => __d('baser_core', 'コメントの送信に失敗しました。入力内容を見なおしてください。'),
]);
$comments = array_values(array_filter((array)($post->blog_comments ?? []), fn($c) => $c->status));
?>
<section class="bt-block bt-comments" aria-labelledby="bt-comments-heading">
	<h2 class="bt-section-title" id="bt-comments-heading">コメント<?= $comments? '（' . count($comments) . '件）' : '' ?></h2>

	<div id="BlogCommentList" class="bt-comment-list">
		<?php foreach ($comments as $comment): ?>
			<?php $this->BcBaser->element('blog_comment', ['blogComment' => $comment]) ?>
		<?php endforeach ?>
	</div>
	<?php if (!$comments): ?>
		<p class="bt-comment-none">コメントはまだありません。</p>
	<?php endif ?>

	<?= $this->BcForm->create(null, ['id' => 'BlogCommentAddForm', 'class' => 'bt-comment-form']) ?>
		<h3>コメントを書く</h3>
		<?= $this->BcForm->control('captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>
		<?= $this->BcForm->control('blog_content_id', ['type' => 'hidden', 'value' => $blogContent->id]) ?>
		<?= $this->BcForm->control('blog_post_id', ['type' => 'hidden', 'value' => $post->id]) ?>
		<div class="bt-field">
			<?= $this->BcForm->label('name', __d('baser_core', 'お名前') . ' / ' . __d('baser_core', 'ニックネーム')) ?><span class="bt-req">必須</span>
			<?= $this->BcForm->control('name', ['type' => 'text', 'required' => false, 'autocomplete' => 'nickname']) ?>
		</div>
		<div class="bt-field">
			<?= $this->BcForm->label('email', __d('baser_core', 'メールアドレス')) ?><span class="bt-req">必須</span>
			<?= $this->BcForm->control('email', ['type' => 'email', 'required' => false, 'autocomplete' => 'email']) ?>
			<p class="bt-note"><?= __d('baser_core', 'メールアドレスは公開されません') ?></p>
		</div>
		<div class="bt-field">
			<?= $this->BcForm->label('url', 'URL') ?>
			<?= $this->BcForm->control('url', ['type' => 'url', 'required' => false]) ?>
		</div>
		<div class="bt-field">
			<?= $this->BcForm->label('message', __d('baser_core', 'コメント')) ?><span class="bt-req">必須</span>
			<?= $this->BcForm->control('message', ['type' => 'textarea', 'rows' => 6, 'required' => false]) ?>
		</div>
		<?php if ($blogContent->auth_captcha): ?>
			<div class="bt-captcha">
				<img src="" alt="<?= __d('baser_core', '認証画象') ?>" class="auth-captcha-image" id="AuthCaptchaImage" style="display:none">
				<?php $this->BcBaser->img('admin/captcha_loader.gif', ['alt' => 'Loading...', 'class' => 'auth-captcha-image', 'id' => 'CaptchaLoader']) ?>
				<?= $this->BcForm->control('auth_captcha', ['type' => 'text', 'size' => 12, 'aria-label' => __d('baser_core', '画像の文字を入力してください')]) ?>
				<span><?= __d('baser_core', '画像の文字を入力してください') ?></span>
			</div>
		<?php endif ?>
		<?= $this->BcForm->button(__d('baser_core', '送信する'), ['id' => 'BlogCommentAddButton', 'class' => 'bt-btn', 'type' => 'button']) ?>
	<?= $this->BcForm->end() ?>

	<div id="ResultMessage" class="bt-comment-result" style="display:none" role="status"></div>
</section>
<?php
$this->BcBaser->js('common.bundle', true, [
	'defer' => 'defer',
	'id' => 'AdminScript',
	'data-baseUrl' => h($this->getRequest()->getAttribute('base')),
	'data-baserCorePrefix' => \Cake\Utility\Inflector::underscore(\BaserCore\Utility\BcUtil::getBaserCorePrefix()),
]);
$this->BcBaser->js('BcBlog.blog_comment.bundle.js', true, [
	'defer' => 'defer',
	'id' => 'BlogCommentsScripts',
	'data-captchaId' => $captchaId,
	'data-commentApprove' => $blogContent->comment_approve,
	'data-authCaptcha' => $blogContent->auth_captcha,
	'data-authCaptchaImageBaseUrl' => $this->BcBaser->getUrl(['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'captcha']),
]);
