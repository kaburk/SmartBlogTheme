<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * ブログの RSS（/blog/index.rss）
 * - アイキャッチがある記事は、画像の URL を enclosure に入れる（フィードリーダーのサムネイル用）
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var \Cake\ORM\ResultSet $posts
 */
use Cake\Routing\Router;

if (!$posts->count()) return;
$this->loadHelper('BcBlog.Blog');

echo $this->Rss->items($posts->toArray(), function ($post) {
	$url = $this->BcBaser->getContentsUrl(null, false, null, false) . 'archives/' . ($post->name ?: $post->no);
	$enclosure = ['url' => '', 'type' => '', 'length' => ''];
	if (!empty($post->eye_catch)) {
		$enclosure['url'] = Router::url($this->Blog->getEyeCatch($post, ['imgsize' => '', 'output' => 'url']), true);
	}
	return [
		'title' => $post->title,
		'link' => $url,
		'guid' => $url,
		'category' => $post->blog_category->title ?? null,
		'description' => $this->Blog->removeCtrlChars($post->content . $post->detail),
		'pubDate' => $post->posted,
		'enclosure' => $enclosure,
		'convertEntities' => false,
	];
});
