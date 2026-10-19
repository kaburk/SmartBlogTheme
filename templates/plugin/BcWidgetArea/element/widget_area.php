<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ウィジェットエリア（サイドバー・広告の枠）
 * BcBaserHelper::widgetArea() で呼び出す
 * @var \BaserCore\View\BcFrontAppView $this
 * @var int $no ウィジェットエリアID
 */
if ($this->getRequest()->is('maintenance') || empty($no)) return;
?>
<div class="bt-widget-area bt-widget-area-<?= h($no) ?>">
	<?php $this->BcWidgetArea->show($no) ?>
</div>
