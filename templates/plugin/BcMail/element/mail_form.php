<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * メールフォーム本体（入力・確認の両方で使う）。項目の行は bc-front の mail_input element が出力する
 * @var \BcMail\View\MailFrontAppView $this
 * @var bool $freezed 確認画面かどうか
 * @var \BcMail\Model\Entity\MailContent $mailContent
 * @var \BcMail\Model\Entity\MailMessage $mailMessage
 */
// ブラウザのヒストリーバック（戻るボタン）対応
$this->Mail->token();
?>
<?php if (!$freezed): ?>
	<?= $this->BcBaser->createMailForm($mailMessage, ['url' => $this->BcBaser->getContentsUrl(null, false, null, false) . 'confirm', 'type' => 'file', 'valueSources' => ['context'], 'novalidate' => true]) ?>
<?php else: ?>
	<?= $this->BcBaser->createMailForm($mailMessage, ['url' => $this->BcBaser->getContentsUrl(null, false, null, false) . 'submit', 'valueSources' => ['context']]) ?>
<?php endif ?>

<?php $this->BcBaser->unlockMailFormField('mode') ?>
<?= $this->BcBaser->mailFormHidden('mode', ['id' => 'MailMessageMode']) ?>

<table class="bs-mail-form-body">
	<?php $this->BcBaser->element('mail_input', ['blockStart' => 1]) ?>
</table>

<?php if ($mailContent->auth_captcha): ?>
	<div class="bt-form-captcha">
		<?php $this->BcBaser->mailFormAuthCaptcha('auth_captcha', ['helper' => $this->BcBaser]) ?>
	</div>
<?php endif ?>

<div class="bt-form-actions">
	<?php if ($freezed): ?>
		<?= $this->BcBaser->mailFormSubmit(__d('baser_core', '書き直す'), ['div' => false, 'class' => 'bt-btn bt-btn--ghost', 'id' => 'BtnMessageBack']) ?>
		<?= $this->BcBaser->mailFormSubmit(__d('baser_core', '送信する'), ['div' => false, 'class' => 'bt-btn', 'id' => 'BtnMessageSubmit']) ?>
	<?php else: ?>
		<?= $this->BcBaser->mailFormSubmit(__d('baser_core', '入力内容を確認する'), ['div' => false, 'class' => 'bt-btn', 'id' => 'BtnMessageConfirm']) ?>
	<?php endif ?>
</div>

<?= $this->BcBaser->endMailForm() ?>
