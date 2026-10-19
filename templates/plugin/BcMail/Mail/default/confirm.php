<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * メールフォーム：確認（入力エラーのときは入力の画面と同じ見た目で出る）
 * @var \BcMail\View\MailFrontAppView $this
 * @var bool $freezed 確認画面かどうか
 */
if ($freezed) {
	$this->Mailform->freeze();
}
?>
<article class="bt-article bt-form-page">
	<div class="bt-article-inner">
		<?php $this->BcBaser->element('mail_steps', ['current' => $freezed? 2 : 1]) ?>
		<header class="bt-article-head">
			<?php if ($freezed): ?>
				<h1 class="bt-article-title">入力内容の確認</h1>
			<?php else: ?>
				<h1 class="bt-article-title"><?php $this->BcBaser->contentsTitle() ?></h1>
			<?php endif ?>
		</header>
		<?php if ($freezed): ?>
			<p class="bt-form-lead">内容をご確認のうえ、「送信する」を押してください。</p>
		<?php else: ?>
			<div class="bt-post-body bt-form-lead"><?php $this->Mail->description() ?></div>
		<?php endif ?>
		<?php $this->BcBaser->flash() ?>
		<div class="bt-form<?= $freezed? ' is-confirm' : '' ?>">
			<?php $this->BcBaser->element('mail_form') ?>
		</div>
	</div>
</article>
