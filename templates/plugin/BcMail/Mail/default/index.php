<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * メールフォーム：入力（記事と同じ枠の中に、進み具合・見出し・説明文・フォーム）
 * @var \BcMail\View\MailFrontAppView $this
 */
?>
<article class="bt-article bt-form-page">
	<div class="bt-article-inner">
		<?php $this->BcBaser->element('mail_steps', ['current' => 1]) ?>
		<header class="bt-article-head">
			<h1 class="bt-article-title"><?php $this->BcBaser->contentsTitle() ?></h1>
		</header>
		<div class="bt-post-body bt-form-lead"><?php $this->Mail->description() ?></div>
		<?php $this->BcBaser->flash() ?>
		<div class="bt-form">
			<?php $this->BcBaser->element('mail_form') ?>
		</div>
	</div>
</article>
