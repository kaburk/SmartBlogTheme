<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * メールフォームの進み具合（入力 → 確認 → 完了）
 * @var \BcMail\View\MailFrontAppView $this
 * @var int $current 今の段階（1 = 入力／2 = 確認／3 = 完了）
 */
$steps = [1 => '入力', 2 => '確認', 3 => '完了'];
?>
<ol class="bt-form-steps" aria-label="お問い合わせの流れ">
	<?php foreach ($steps as $no => $label): ?>
		<li<?= $no === (int)$current? ' class="is-current" aria-current="step"' : ($no < (int)$current? ' class="is-done"' : '') ?>><span class="bt-form-steps-no"><?= $no ?></span><?= h($label) ?></li>
	<?php endforeach ?>
</ol>
