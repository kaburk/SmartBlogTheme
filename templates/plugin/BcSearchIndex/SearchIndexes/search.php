<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * サイト内検索の結果（見出し・件数・結果の一覧・0件の案内・ページ送り）
 * - 検索結果は記事に限らない（固定ページも含む）ので、カードはタイトル・抜粋・URL の形にする
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $query 検索キーワード
 * @var \Cake\ORM\ResultSet $searchIndexes 検索結果リスト
 */
$this->loadHelper('SmartBlogTheme.BlogTheme');
$this->BcBaser->setTitle('検索結果');
$keyword = trim(implode(' ', (array)$query));
try {
	$count = (int)$this->Paginator->counter('{{count}}');
} catch (\Cake\Core\Exception\CakeException) {
	$count = $searchIndexes->count();
}
$blog = $this->BlogTheme->mainBlog();
?>
<header class="bt-list-head">
	<p class="bt-list-kicker">サイト内検索</p>
	<h1 class="bt-list-title">
		<span><?= $keyword !== ''? '"' . h($keyword) . '" の検索結果' : '検索' ?></span>
		<?php if ($keyword !== ''): ?><span class="bt-list-count"><?= h($count) ?>件</span><?php endif ?>
	</h1>
</header>

<?php if ($searchIndexes->count() > 0): ?>
	<ul class="bt-post-grid bt-search-results">
		<?php foreach ($searchIndexes as $searchIndex): ?>
			<li>
				<article class="bt-card">
					<div class="bt-card-body">
						<h3 class="bt-card-title"><a href="<?= h($this->BcBaser->getUrl($searchIndex->url)) ?>"><?= $this->BcBaser->mark($query, h($searchIndex->title)) ?></a></h3>
						<p class="bt-card-excerpt"><?php $detail = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$searchIndex->detail))) ?>
						<?= $this->BcBaser->mark($query, h(mb_strlen($detail) > 120? mb_substr($detail, 0, 120) . '…' : $detail)) ?></p>
						<div class="bt-card-foot">
							<p class="bt-card-url"><?= h(rawurldecode($searchIndex->url)) ?></p>
						</div>
					</div>
				</article>
			</li>
		<?php endforeach ?>
	</ul>
	<?php $this->BcBaser->pagination('simple', [], ['subDir' => false]) ?>
<?php else: ?>
	<div class="bt-empty">
		<p class="bt-empty-title"><?= $keyword !== ''? '記事が見つかりませんでした' : '検索するキーワードを入力してください' ?></p>
		<p>ことばを変えて検索するか、タグから探してみてください。</p>
		<?php if ($blog): ?>
			<?php $this->BcBaser->element('BcBlog.tag_list', ['blogContentId' => (int)$blog->entity_id, 'limit' => 8]) ?>
		<?php endif ?>
	</div>
<?php endif ?>
