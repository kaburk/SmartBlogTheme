<?php
declare(strict_types=1);
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * テンプレートで使う共通の処理（読了時間・抜粋・アイキャッチ・ナビ・シェア・広告のウィジェットエリア・head の meta タグ）
 */

namespace SmartBlogTheme\View\Helper;

use BcBlog\Model\Entity\BlogPost;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;

/**
 * BlogThemeHelper
 *
 * @property \BaserCore\View\Helper\BcBaserHelper $BcBaser
 * @property \BcBlog\View\Helper\BlogHelper $Blog
 */
class BlogThemeHelper extends Helper
{

    /**
     * 使うヘルパー
     * @var array
     */
    public array $helpers = ['BaserCore.BcBaser', 'BcBlog.Blog'];

    /**
     * 1分で読める文字数（読了時間の目安に使う）
     */
    public const CHARS_PER_MINUTE = 500;

    /**
     * キャッシュ
     * @var array
     */
    private array $cache = [];

    /**
     * テーマの設定（config/setting.php の BlogTheme）を取得する
     *
     * @param string $key キー（例: variant / fonts / defaultScheme / tagStrip）
     * @param mixed $default
     * @return mixed
     */
    public function setting(string $key, mixed $default = null): mixed
    {
        return Configure::read('BlogTheme.' . $key, $default);
    }

    /**
     * テーマ設定（管理画面）に保存した、このテーマ独自の値
     *
     * @param string $key bt_color_scheme / bt_scheme_toggle
     * @return string|null 未設定なら null
     */
    public function themeConfig(string $key): ?string
    {
        if (!array_key_exists('themeConfigs', $this->cache)) {
            $this->cache['themeConfigs'] = [];
            if (\Cake\Core\Plugin::isLoaded('BcThemeConfig')) {
                try {
                    $this->cache['themeConfigs'] = TableRegistry::getTableLocator()->get('BcThemeConfig.ThemeConfigs')->getKeyValue();
                } catch (\Throwable) {
                }
            }
        }
        $value = $this->cache['themeConfigs'][$key] ?? null;
        return ($value === null || $value === '')? null : (string)$value;
    }

    /**
     * 配色モード（auto / light / dark）。テーマ設定 → config/setting.php の colorScheme の順
     *
     * @return string
     */
    public function colorScheme(): string
    {
        $mode = $this->themeConfig('bt_color_scheme') ?? (string)$this->setting('colorScheme', 'auto');
        return in_array($mode, ['auto', 'light', 'dark'], true)? $mode : 'auto';
    }

    /**
     * ライト／ダークの切り替えボタンを出すか。テーマ設定 → config/setting.php の schemeToggle の順
     *
     * @return bool
     */
    public function schemeToggle(): bool
    {
        $value = $this->themeConfig('bt_scheme_toggle');
        return $value === null? (bool)$this->setting('schemeToggle', true) : $value === '1';
    }

    /**
     * 本文のテキスト（HTML と BurgerEditor の data 属性を除いたもの）
     *
     * @param BlogPost $post
     * @return string
     */
    public function plainText(BlogPost $post): string
    {
        $html = (string)$post->content . (string)$post->detail;
        // コードブロック（BgeAddonSyntaxHighlight）の base64 などを数えないように、属性ごとタグを消す
        $text = strip_tags(preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $html));
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * 読了時間の目安（分）。最小 1 分
     *
     * @param BlogPost $post
     * @return int
     */
    public function readingMinutes(BlogPost $post): int
    {
        $length = mb_strlen($this->plainText($post));
        return max(1, (int)ceil($length / self::CHARS_PER_MINUTE));
    }

    /**
     * 抜粋（概要があれば概要、なければ本文の先頭。見出し・コード・キャプションの文字は除く）
     *
     * @param BlogPost $post
     * @param int $length
     * @return string エスケープ済みではないテキスト
     */
    public function excerpt(BlogPost $post, int $length = 120): string
    {
        $summary = trim(strip_tags((string)$post->content));
        $html = ($summary !== '' && trim(strip_tags((string)$post->detail)) !== '')? (string)$post->content : (string)$post->content . (string)$post->detail;
        $html = preg_replace('/<(h[1-6]|pre|figcaption|script|style|button)\b[^>]*>.*?<\/\1>/is', ' ', $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length) . '…';
        }
        return $text;
    }

    /**
     * 本文の最初の画像の URL（アイキャッチがないときの代わりに使う）
     *
     * @param BlogPost $post
     * @return string
     */
    public function firstImage(BlogPost $post): string
    {
        $html = (string)$post->content . (string)$post->detail;
        if (preg_match('/<img\b[^>]*\bsrc=(["\'])([^"\']+)\1/i', $html, $m)) {
            return html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return '';
    }

    /**
     * 一覧のカードの画像。アイキャッチ → 本文の最初の画像 → 「メインを薄めた背景＋カテゴリ名」の枠 の順
     * スタイルの設定 thumbFallback が none のときは、枠を作らず空文字を返す（カードは文字だけになる）
     *
     * @param BlogPost $post
     * @param string $size アイキャッチの画像サイズ（default / thumb / mobile_thumb）
     * @return string HTML
     */
    public function thumb(BlogPost $post, string $size = 'default'): string
    {
        $img = $this->Blog->getEyeCatch($post, [
            'link' => false,
            'imgsize' => $size,
            'class' => 'bt-thumb-img',
            'alt' => '',
            'loading' => 'lazy',
        ]);
        if ($img) {
            return $img;
        }
        $src = $this->firstImage($post);
        if ($src) {
            return '<img class="bt-thumb-img" src="' . h($src) . '" alt="" loading="lazy">';
        }
        if ($this->setting('thumbFallback', 'frame') === 'none') {
            return '';
        }
        $label = $this->Blog->getCategoryTitle($post) ?: $this->Blog->getTitle();
        return '<div class="bt-card-noimage" aria-hidden="true"><span>' . h($label) . '</span></div>';
    }

    /**
     * タグと公開中の記事数（記事数が 0 のタグは含めない。名前の順）
     *
     * BlogHelper::getTagList(..., ['postCount' => true]) は、BlogTags の検索にほかのプラグインが contain を足すと
     * （例：BlogTagGroup の BcBlog.BlogTags.beforeFind）、記事数を数える SQL でエラーになる。
     * そのため ORM のイベントを通らないよう、データベースに直接問い合わせて数える。
     *
     * @param int $blogContentId ブログコンテンツID
     * @return array [object{name, post_count}, ...]
     */
    public function tagCounts(int $blogContentId): array
    {
        $key = 'tagCounts' . $blogContentId;
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }
        $posts = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
        $tags = TableRegistry::getTableLocator()->get('BcBlog.BlogTags');
        $junction = $posts->getAssociation('BlogTags')->junction()->getTable();
        $now = date('Y-m-d H:i:s');
        $rows = $posts->getConnection()->selectQuery()
            ->select(['name' => 't.name', 'post_count' => 'COUNT(p.id)'])
            ->from(['t' => $tags->getTable()])
            ->innerJoin(['pt' => $junction], 'pt.blog_tag_id = t.id')
            ->innerJoin(['p' => $posts->getTable()], 'p.id = pt.blog_post_id')
            ->where([
                'p.blog_content_id' => $blogContentId,
                'p.status' => true,
                ['OR' => ['p.publish_begin IS' => null, 'p.publish_begin <=' => $now]],
                ['OR' => ['p.publish_end IS' => null, 'p.publish_end >=' => $now]],
            ])
            ->groupBy(['t.id', 't.name'])
            ->orderBy(['t.name' => 'ASC'])
            ->execute()
            ->fetchAll('assoc');
        return $this->cache[$key] = array_map(fn($row) => (object)['name' => $row['name'], 'post_count' => (int)$row['post_count']], $rows);
    }

    /**
     * 記事のカテゴリ一覧の URL
     *
     * @param BlogPost $post
     * @return string
     */
    public function categoryUrl(BlogPost $post): string
    {
        if (empty($post->blog_category->name)) {
            return '';
        }
        return $this->blogUrl($post) . 'archives/category/' . rawurlencode($post->blog_category->name);
    }

    /**
     * 記事のタグ一覧の URL
     *
     * @param BlogPost $post
     * @param \BcBlog\Model\Entity\BlogTag $tag
     * @return string
     */
    public function tagUrl(BlogPost $post, $tag): string
    {
        return $this->blogUrl($post) . 'archives/tag/' . rawurlencode($tag->name);
    }

    /**
     * 記事が属するブログのトップの URL（/blog/ の形）
     *
     * @param BlogPost $post
     * @return string
     */
    public function blogUrl(BlogPost $post): string
    {
        $url = $post->blog_content->content->url ?? null;
        if (!$url) {
            $url = $this->Blog->getContentsUrl((int)$post->blog_content_id, false);
        }
        return $this->BcBaser->getContentsUrl($url, false, null, false);
    }

    /**
     * 著者の表示名
     *
     * @param BlogPost $post
     * @return string
     */
    public function authorName(BlogPost $post): string
    {
        if (empty($post->user)) {
            return '';
        }
        return (string)$this->BcBaser->getUserName($post->user);
    }

    /**
     * 更新日を出すかどうか（投稿日と同じ日なら出さない）
     *
     * @param BlogPost $post
     * @return bool
     */
    public function isUpdated(BlogPost $post): bool
    {
        if (empty($post->modified) || empty($post->posted)) {
            return false;
        }
        return $post->modified->format('Ymd') > $post->posted->format('Ymd');
    }

    /**
     * カード・記事の日付（書式はスタイルの設定 dateFormat。初期値 Y.m.d）
     *
     * @param \DateTimeInterface|null $date
     * @return string
     */
    public function dateText(?\DateTimeInterface $date): string
    {
        return $date? $date->format((string)$this->setting('dateFormat', 'Y.m.d')) : '';
    }

    /**
     * サイト名（サイト管理の「サイトタイトル」。空なら「表示名」）
     *
     * @return string
     */
    public function siteTitle(): string
    {
        $site = $this->getView()->getRequest()->getAttribute('currentSite');
        return $site? (string)($site->title ?: $site->display_name) : '';
    }

    /**
     * いま表示しているサイトの最初のブログ（ナビ・サイドバーの既定に使う）
     *
     * @return \BaserCore\Model\Entity\Content|null
     */
    public function mainBlog()
    {
        if (array_key_exists('mainBlog', $this->cache)) {
            return $this->cache['mainBlog'];
        }
        $site = $this->getView()->getRequest()->getAttribute('currentSite');
        $content = TableRegistry::getTableLocator()->get('BaserCore.Contents')->find()
            ->where([
                'Contents.type' => 'BlogContent',
                'Contents.site_id' => $site->id ?? 1,
                'Contents.status' => true,
            ])
            ->orderBy(['Contents.lft' => 'ASC'])
            ->first();
        return $this->cache['mainBlog'] = $content;
    }

    /**
     * サイトの説明1行（最初のブログの「ブログ説明文」の最初の1文）
     *
     * @return string
     */
    public function tagline(): string
    {
        if (isset($this->cache['tagline'])) {
            return $this->cache['tagline'];
        }
        $text = '';
        $blog = $this->mainBlog();
        if ($blog) {
            $blogContent = TableRegistry::getTableLocator()->get('BcBlog.BlogContents')->find()
                ->where(['BlogContents.id' => $blog->entity_id])->first();
            $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string)($blogContent->description ?? ''))));
            if (mb_strlen($text) > 60 && preg_match('/^(.+?。)/u', $text, $m)) {
                $text = $m[1];
            }
        }
        return $this->cache['tagline'] = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * ヘッダー・フッターのナビの作り方（contents / categories）。テーマ設定 → config/setting.php の navSource の順
     *
     * @return string
     */
    public function navSource(): string
    {
        $source = $this->themeConfig('bt_nav_source') ?? (string)$this->setting('navSource', 'contents');
        return in_array($source, ['contents', 'categories'], true)? $source : 'contents';
    }

    /**
     * ヘッダー・フッターのナビ
     *
     * - contents：コンテンツ管理のルート直下（ブログ・固定ページ・リンク・メールフォームなど。並び順どおり）。
     *   「メニューに表示しない」とトップページ（index）は除く。「別ウィンドウで開く」は blank = true
     * - categories：最初のブログのカテゴリ（第1階層）＋ルート直下の固定ページ・メールフォーム・リンク（「メニューに表示しない」を除く）
     * @return array [['title' => '', 'url' => '', 'current' => bool, 'blank' => bool], ...]
     */
    public function navItems(): array
    {
        if (isset($this->cache['nav'])) {
            return $this->cache['nav'];
        }
        $here = rawurldecode($this->getView()->getRequest()->getPath());
        $site = $this->getView()->getRequest()->getAttribute('currentSite');
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $items = [];
        if ($this->navSource() === 'contents') {
            $root = $contentsTable->find()->where(['Contents.site_id' => $site->id ?? 1, 'Contents.site_root' => true])->first();
            $contents = $root? $contentsTable->find()
                ->where([
                    'Contents.parent_id' => $root->id,
                    'Contents.status' => true,
                    'Contents.exclude_menu' => false,
                    'Contents.name !=' => 'index',
                ])
                ->orderBy(['Contents.lft' => 'ASC'])
                ->all() : [];
            foreach ($contents as $content) {
                if (!$content->title) continue;
                $url = $this->BcBaser->getContentsUrl($content->url, false, null, false);
                $path = rawurldecode((string)$content->url);
                // フォルダ・ブログ（/blog/ のように / で終わる）は、その下のページでも現在地にする
                $current = $here === $path || (str_ends_with($path, '/') && $path !== '/' && str_starts_with($here, $path));
                $items[] = ['title' => $content->title, 'url' => $url, 'current' => $current, 'blank' => !empty($content->blank_link)];
            }
            return $this->cache['nav'] = $items;
        }

        $blog = $this->mainBlog();
        if ($blog) {
            $base = $this->BcBaser->getContentsUrl($blog->url, false, null, false);
            $categories = $this->Blog->getCategories(['blogContentId' => $blog->entity_id, 'depth' => 1]);
            foreach ($categories as $category) {
                $url = $base . 'archives/category/' . rawurlencode($category->name);
                $items[] = ['title' => $category->title, 'url' => $url, 'current' => $here === rawurldecode($url), 'blank' => false];
            }
            $post = $this->getView()->get('post');
            if ($post instanceof BlogPost && !empty($post->blog_category->name)) {
                foreach ($items as $i => $item) {
                    if ($item['title'] === $post->blog_category->title) {
                        $items[$i]['current'] = true;
                    }
                }
            }
        }
        // ルート直下の固定ページ・メールフォーム・リンク（お問い合わせもナビに出す）
        $pages = $contentsTable->find()
            ->where([
                'Contents.type IN' => ['Page', 'MailContent', 'ContentLink'],
                'Contents.site_id' => $site->id ?? 1,
                'Contents.level' => 1,
                'Contents.status' => true,
                'Contents.exclude_menu' => false,
                'Contents.name !=' => 'index',
            ])
            ->orderBy(['Contents.lft' => 'ASC'])
            ->all();
        foreach ($pages as $page) {
            if (!$page->title) continue;
            $url = $this->BcBaser->getContentsUrl($page->url, false, null, false);
            $path = rawurldecode((string)$page->url);
            $current = $here === $path || (str_ends_with($path, '/') && $path !== '/' && str_starts_with($here, $path));
            $items[] = ['title' => $page->title, 'url' => $url, 'current' => $current, 'blank' => !empty($page->blank_link)];
        }
        return $this->cache['nav'] = $items;
    }

    /**
     * RSS の URL（最初のブログ）
     *
     * @return string
     */
    public function rssUrl(): string
    {
        $blog = $this->mainBlog();
        if (!$blog) {
            return '';
        }
        return $this->BcBaser->getContentsUrl($blog->url, false, null, false) . 'index.rss';
    }

    /**
     * 名前でウィジェットエリアを探して出力する（広告の枠など）。見つからない・中身が空のときは何も出さない
     *
     * @param string $name ウィジェットエリア名（例: 広告：記事の上）
     * @param string $class 外側の class
     * @param string|null $label 読み上げ用のラベル（null なら付けない）
     * @return string HTML
     */
    public function namedWidgetArea(string $name, string $class = 'bt-ad', ?string $label = '広告'): string
    {
        if (!\Cake\Core\Plugin::isLoaded('BcWidgetArea')) {
            return '';
        }
        $area = TableRegistry::getTableLocator()->get('BcWidgetArea.WidgetAreas')->find()
            ->where(['WidgetAreas.name' => $name])
            ->first();
        if (!$area) {
            return '';
        }
        // 固定ページなどでは BcWidgetArea のヘルパーが読み込まれていないので読み込む
        $this->getView()->loadHelper('BcWidgetArea.BcWidgetArea');
        $html = $this->getView()->element('BcWidgetArea.widget_area', ['no' => $area->id]);
        if (trim(strip_tags($html, '<img><iframe><ins><script>')) === '') {
            return '';
        }
        $attr = $label === null? '' : ' role="complementary" aria-label="' . h($label) . '"';
        return '<div class="' . h($class) . '"' . $attr . '>' . $html . '</div>';
    }

    /**
     * SNS のシェア用 URL
     *
     * @param string $url ページの URL（フル）
     * @param string $title ページのタイトル
     * @return array [['key' => 'x', 'label' => 'X', 'url' => ''], ...]
     */
    public function shareLinks(string $url, string $title): array
    {
        $u = rawurlencode($url);
        $t = rawurlencode($title);
        $links = [
            ['key' => 'x', 'label' => 'X', 'url' => "https://x.com/intent/post?url={$u}&text={$t}"],
            ['key' => 'hatena', 'label' => 'はてブ', 'url' => "https://b.hatena.ne.jp/add?mode=confirm&url={$u}&title={$t}"],
            ['key' => 'facebook', 'label' => 'Facebook', 'url' => "https://www.facebook.com/sharer/sharer.php?u={$u}"],
            ['key' => 'line', 'label' => 'LINE', 'url' => "https://social-plugins.line.me/lineit/share?url={$u}"],
        ];
        $rss = $this->rssUrl();
        if ($rss) {
            $fullRss = $this->BcBaser->getUri($rss);
            $links[] = ['key' => 'feedly', 'label' => 'Feedly', 'url' => 'https://feedly.com/i/subscription/feed%2F' . rawurlencode($fullRss)];
            $links[] = ['key' => 'rss', 'label' => 'RSS', 'url' => $rss];
        }
        return $links;
    }

    /**
     * head の meta タグ（description・keywords・SEO設定・OGP・X のカード）
     *
     * SEO設定（bc-seo）が有効なら、管理画面の SEO設定の値を優先する。bc-seo は入力された値しか出さない
     * （og:title・og:url は記事ごとに入力が必要、twitter:card は項目がない、og:image はアイキャッチのサムネイル）ため、
     * 足りないものをページの情報で補う。
     * - og:title：ページのタイトル（トップはサイト名）／og:description：ディスクリプション
     * - og:type：記事は article、それ以外は website／og:url：そのページの URL
     * - og:image：記事のアイキャッチ（元の大きさ）→ 本文の最初の画像。bc-seo がサムネイルを出す場合も元の大きさにする
     * - og:site_name：サイト名／twitter:card：画像があれば summary_large_image、なければ summary
     * - canonical は BcBaserHelper が出すので、ここでは出さない
     *
     * @return string HTML
     */
    public function metaTags(): string
    {
        $seo = Plugin::isLoaded('BcSeo')? (array)$this->BcBaser->getSeoMeta() : [];
        $value = fn(string $key): string => trim(strip_tags((string)($seo[$key]['value'] ?? '')));
        $view = $this->getView();
        $request = $view->getRequest();
        $post = $view->get('post');
        $isPost = $post instanceof BlogPost && $request->getParam('controller') === 'Blog' && $request->getParam('action') === 'archives';
        $full = fn(string $url): string => preg_match('/^https?:/i', $url)? $url : $this->BcBaser->getUrl($url, true);

        $description = $value('description') ?: trim(strip_tags((string)$this->BcBaser->getDescription()));
        $tags = [
            ['name', 'description', $description],
            ['name', 'keywords', $value('keywords') ?: (string)$this->BcBaser->getKeywords()],
        ];

        // SEO設定にあって、下で扱わない項目（設定で足した項目など）はそのまま出す
        $handled = ['description', 'keywords', 'canonical_url', 'og_title', 'og_description', 'og_type', 'og_url', 'og_image'];
        foreach ($seo as $key => $field) {
            if (in_array($key, $handled, true) || $value($key) === '') continue;
            foreach (['property', 'name'] as $attr) {
                if (!empty($field[$attr])) {
                    $tags[] = [$attr, $field[$attr], $value($key)];
                    break;
                }
            }
        }

        $title = $value('og_title') ?: ($this->BcBaser->isHome()? $this->siteTitle() : trim(strip_tags((string)$this->BcBaser->getContentsTitle())));
        $image = $value('og_image');
        if ($isPost) {
            // bc-seo はアイキャッチのサムネイル（600×600）を og:image にするので、元の大きさに差し替える
            // （画像の URL の末尾には呼ぶたびに変わる ?数字 が付くので、それを除いて比べる）
            $path = fn(string $url): string => (string)strtok($url, '?');
            $thumb = (string)$this->Blog->getEyeCatch($post, ['output' => 'url']);
            $large = (string)$this->Blog->getEyeCatch($post, ['output' => 'url', 'imgsize' => 'default']);
            if ($large && ($image === '' || ($thumb && $path($image) === $path($full($thumb))))) {
                $image = $large;
            }
            if ($image === '' && $this->firstImage($post)) {
                $image = $this->firstImage($post);
            }
        }
        $tags[] = ['property', 'og:title', $title];
        $tags[] = ['property', 'og:description', $value('og_description') ?: $description];
        $tags[] = ['property', 'og:type', $value('og_type') ?: ($isPost? 'article' : 'website')];
        $tags[] = ['property', 'og:url', $value('og_url') ?: $this->BcBaser->getUrl($this->BcBaser->getHere(), true)];
        $tags[] = ['property', 'og:image', $image? $full($image) : ''];
        $tags[] = ['property', 'og:site_name', $this->siteTitle()];
        $tags[] = ['name', 'twitter:card', $image? 'summary_large_image' : 'summary'];

        $html = '';
        foreach ($tags as [$attr, $key, $content]) {
            // description・keywords は空でも出す（BcBaserHelper::metaDescription() などと同じ）。ほかは空なら出さない
            if ($content === '' && !in_array($key, ['description', 'keywords'], true)) continue;
            $html .= '<meta ' . $attr . '="' . h($key) . '" content="' . h($content) . '">' . "\n";
        }
        return $html;
    }

    /**
     * アイコン（element/icons.php の SVG シンボルを使う）
     *
     * @param string $name シンボル名（search / sun / moon / rss / menu / close / calendar / refresh / clock / ...）
     * @param string $class
     * @return string
     */
    public function icon(string $name, string $class = 'bt-ico'): string
    {
        return '<svg class="' . h($class) . '" aria-hidden="true"><use href="#bt-i-' . h($name) . '"></use></svg>';
    }

    /**
     * サブフォルダに設置したサイト（例：https://example.com/hoge/）で、ページの HTML の中の
     * ルートからのパス（/contact/・/<テーマ>/img/… など）に、設置したフォルダ（/hoge）を足す
     *
     * - 初期データの本文は、ルートに置いたときのパスで書いているため、サブフォルダではリンクが切れる
     * - ルートに置いたサイト（base が空）では何もしない（出力は変わらない）
     * - View.afterLayout で呼ばれる（Helper::implementedEvents()）
     *
     * @param \Cake\Event\EventInterface $event
     * @param string $layoutFile
     * @return void
     */
    public function afterLayout(\Cake\Event\EventInterface $event, $layoutFile): void
    {
        $base = rtrim((string)$this->getView()->getRequest()->getAttribute('base'), '/');
        if ($base === '') {
            return;
        }
        $view = $this->getView();
        $view->assign('content', self::addBase($view->fetch('content'), $base));
    }

    /**
     * HTML の href・src・action・poster・srcset のうち、ルートからのパスに $base を足す
     * - //（プロトコル相対）で始まるもの、すでに $base/ で始まるものはそのまま
     *
     * @param string $html
     * @param string $base 設置したフォルダ（例：/hoge）
     * @return string
     */
    public static function addBase(string $html, string $base): string
    {
        $base = rtrim($base, '/');
        if ($base === '') {
            return $html;
        }
        $fix = function (string $url) use ($base): string {
            if (!str_starts_with($url, '/') || str_starts_with($url, '//') || $url === $base || str_starts_with($url, $base . '/')) {
                return $url;
            }
            return $base . $url;
        };
        $html = preg_replace_callback(
            '/(?<![\w-])(href|src|action|poster)=(["\'])(\/[^"\']*)\2/i',
            fn(array $m): string => $m[1] . '=' . $m[2] . $fix($m[3]) . $m[2],
            $html
        ) ?? $html;
        return preg_replace_callback(
            '/(?<![\w-])srcset=(["\'])([^"\']*)\1/i',
            function (array $m) use ($fix): string {
                $items = array_map(function (string $item) use ($fix): string {
                    $parts = preg_split('/\s+/', trim($item), 2);
                    return trim($fix($parts[0]) . ' ' . ($parts[1] ?? ''));
                }, explode(',', $m[2]));
                return 'srcset=' . $m[1] . implode(', ', $items) . $m[1];
            },
            $html
        ) ?? $html;
    }

}
