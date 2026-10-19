<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * メールフォーム：完了（メールフォームの設定の「送信完了メッセージ」を出す）
 * @var \BcMail\View\MailFrontAppView $this
 * @var \BcMail\Model\Entity\MailContent $mailContent
 */
use Cake\Core\Configure;

if (Configure::read('debug') == 0 && $mailContent->redirect_url) {
	$this->BcBaser->meta(['http-equiv' => 'Refresh'], null, ['content' => '5;url=' . $mailContent->redirect_url, 'block' => true]);
}
?>
<article class="bt-article bt-form-page">
	<div class="bt-article-inner">
		<?php $this->BcBaser->element('mail_steps', ['current' => 3]) ?>
		<div class="bt-form-done">
			<span class="bt-form-done-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg></span>
			<h1 class="bt-article-title">送信が完了しました</h1>
		</div>
		<div class="bt-post-body">
			<?php if ($this->BcBaser->mailFormThanksExists()): ?>
				<?php $this->BcBaser->mailFormThanks() ?>
			<?php else: ?>
				<p>お問い合わせいただき、ありがとうございました。ご入力のメールアドレスに、受付確認のメールをお送りしました。</p>
			<?php endif ?>
		</div>
		<p class="bt-more"><a class="bt-btn bt-btn--ghost" href="<?= h($this->BcBaser->getUrl('/')) ?>">トップページに戻る</a></p>
	</div>
</article>
