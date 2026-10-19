<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ページ送り
 * - PC：前へ 1 … 5 6 7 8 9 … 14 次へ（現在のページの前後を多めに出す）
 * - スマホ：‹ 1 … 6 7 8 … 14 ›（1行に収まるように前後を減らす）。CSS のコンテナクエリで切り替える
 * BcBaserHelper::pagination() で呼び出す
 * @var \BaserCore\View\BcFrontAppView $this
 */
try {
	$pageCount = (int)$this->Paginator->counter('{{pages}}');
} catch (\Cake\Core\Exception\CakeException) {
	return;
}
if ($pageCount <= 1) {
	return;
}
$this->Paginator->setTemplates([
	'prevActive' => '<li class="bt-page-prev"><a href="{{url}}" rel="prev">{{text}}</a></li>',
	'prevDisabled' => '',
	'nextActive' => '<li class="bt-page-next"><a href="{{url}}" rel="next">{{text}}</a></li>',
	'nextDisabled' => '',
	'current' => '<li><span aria-current="page">{{text}}</span></li>',
	'number' => '<li><a href="{{url}}">{{text}}</a></li>',
	'first' => '<li><a href="{{url}}">{{text}}</a></li>',
	'last' => '<li><a href="{{url}}">{{text}}</a></li>',
	'ellipsis' => '<li><span class="bt-gap" aria-hidden="true">…</span></li>',
]);
?>
<nav class="bt-pagination" aria-label="ページ送り">
	<ul class="bt-pagination-wide">
		<?= $this->Paginator->prev('前へ') ?>
		<?= $this->Paginator->numbers(['modulus' => 4, 'first' => 1, 'last' => 1]) ?>
		<?= $this->Paginator->next('次へ') ?>
	</ul>
	<ul class="bt-pagination-narrow">
		<?= $this->Paginator->prev('‹', ['escape' => false]) ?>
		<?= $this->Paginator->numbers(['modulus' => 2, 'first' => 1, 'last' => 1]) ?>
		<?= $this->Paginator->next('›', ['escape' => false]) ?>
	</ul>
</nav>
