<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ウィジェット：テキスト（プロフィール・広告のコードなど）
 * - 「タイトルを表示」をオフにすると、見出しなし・枠なしで中身だけを出す（広告のコード向け）
 * @var \BaserCore\View\BcFrontAppView $this
 * @var int $id ウィジェットID
 * @var string $name タイトル
 * @var bool $use_title タイトルを利用するかどうか
 * @var string $text 登録したテキスト
 */
?>
<?php if ($name && $use_title): ?>
	<section class="bt-widget bt-widget-text bt-widget-text-<?= h($id) ?>">
		<h2 class="bt-widget-title"><?= h($name) ?></h2>
		<div class="bt-widget-body">
			<?= $text ?>
		</div>
	</section>
<?php else: ?>
	<div class="bt-widget-raw bt-widget-text-<?= h($id) ?>">
		<?= $text ?>
	</div>
<?php endif ?>
