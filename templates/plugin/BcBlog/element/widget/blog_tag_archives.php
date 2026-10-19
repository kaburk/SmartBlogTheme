<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ウィジェット：ブログのタグ一覧（タグの形で並べる。「記事数を表示」で件数つき）
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var string $name
 * @var bool $use_title
 * @var int $blog_content_id
 * @var bool $view_count
 */
$this->BcBaser->element('BcBlog.tag_list', [
	'blogContentId' => (int)$blog_content_id,
	'heading' => ($name && $use_title)? $name : '',
	'viewCount' => !empty($view_count),
	'wrap' => 'widget',
]);
