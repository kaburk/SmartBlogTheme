<?php
declare(strict_types=1);
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 */

namespace SmartBlogTheme;

use BaserCore\BcPlugin;
use BaserCore\Utility\BcEvent;
use Cake\Core\Plugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\TableRegistry;

/**
 * plugin for SmartBlogTheme
 */
class SmartBlogThemePlugin extends BcPlugin
{

    /**
     * bootstrap
     *
     * テーマとして読み込まれたとき、baserCMS はテーマのイベントリスナー（src/Event）を登録しないので、ここで登録する
     * （管理画面の「テーマ設定」に配色モードの項目を足す SmartBlogThemeHelperEventListener）
     *
     * @param PluginApplicationInterface $app
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);
        BcEvent::registerPluginEvent($this->getName());
    }

    /**
     * 初期データ読み込み時の更新処理
     *
     * BcBlog は初期データを読み込むと、すべての記事の投稿日を「今」に書き換える（BcBlogPlugin::updateDefaultData）。
     * 見本の記事は投稿日の違い（並び順・月別アーカイブ）を見せたいので、投稿日を CSV の作成日（created）に戻す。
     * 初期データの config/data/<パターン>/SmartBlogTheme/ フォルダがあると、BcBlog の後にこのメソッドが呼ばれる
     * （フォルダはプラグイン名の順に読まれる）。
     * サイト内検索の索引は、初期データの BcSearchIndex/search_indexes.csv で入れている（scripts/build-blog-data.php が作る）。
     *
     * @param array $options
     * @return void
     */
    public function updateDefaultData($options = []): void
    {
        if (!Plugin::isLoaded('BcBlog')) {
            return;
        }
        TableRegistry::getTableLocator()->get('BcBlog.BlogPosts')->updateAll(
            [new QueryExpression('posted = created')],
            ['created IS NOT' => null]
        );
    }

}
