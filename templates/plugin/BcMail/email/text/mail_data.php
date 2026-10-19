<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * メールフォームの送信メールの内容（bc-front の email/text/mail_data.php をもとにしている）
 * - 変えたところ：「送信しない」（no_send）の項目（個人情報の取り扱いへの同意など）は、見出しごと出さない
 *   （bc-front は見出しだけを出し、値が空の行になる）
 * @var \BcMail\View\MailFrontEmailView $this
 * @var array $mailFields メールフィールドリスト
 * @var array $message 送信内容
 */
$group_field = null;
foreach ($mailFields as $field) {
	if ($field->type === 'hidden' || $field->no_send) continue;
	if ($field->use_field && ($group_field != $field->group_field || (!$group_field && !$field->group_field))) {
?>


◇◆ <?php echo $field->head; ?>　
----------------------------------------
<?php
	}
	if ($field->type != 'file' && !empty($field->before_attachment) && isset($message[$field->field_name])) {
		echo " " . $field->before_attachment;
	}
	if (isset($message[$field->field_name]) && $field->use_field) {
		if ($field->type != 'file') {
			echo $this->Maildata->control($field->type, $message[$field->field_name], $this->Mailfield->getOptions($field));
		} else {
			if ($message[$field->field_name]) {
				echo __d('baser_core', '添付あり');
			} else {
				echo __d('baser_core', '添付なし');
			}
		}
	}
	if ($field->type != 'file' && !empty($field->after_attachment) && isset($message[$field->field_name])) {
		echo " " . $field->after_attachment;
	}
	$group_field = $field->group_field;
}
