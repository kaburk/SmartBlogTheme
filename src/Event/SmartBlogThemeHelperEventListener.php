<?php
declare(strict_types=1);
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * 管理画面の「テーマ設定」に、このテーマの項目（配色モード・切り替えボタン・ヘッダーのナビ・ヘッダーのボタン）を足す
 * - 値は BcThemeConfig のキーと値（theme_configs の bt_color_scheme・bt_scheme_toggle・bt_nav_source・bt_header_cta_label・bt_header_cta_url）にそのまま保存される
 * - テーマのイベントリスナーは自動では登録されないので、SmartBlogThemePlugin::bootstrap() で登録している
 */

namespace SmartBlogTheme\Event;

use BaserCore\Event\BcHelperEventListener;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;

/**
 * SmartBlogThemeHelperEventListener
 */
class SmartBlogThemeHelperEventListener extends BcHelperEventListener
{

    /**
     * Events
     *
     * @var array
     */
    public $events = ['Form.afterForm'];

    /**
     * フォームの最後に行を足す（テーマ設定の画面で、このテーマを使っているときだけ）
     *
     * @param EventInterface $event
     * @return void
     */
    public function formAfterForm(EventInterface $event): void
    {
        if (!BcUtil::isAdminSystem()) return;
        /** @var \BaserCore\View\BcAdminAppView $view */
        $view = $event->getSubject();
        $request = $view->getRequest();
        if ($request->getParam('plugin') !== 'BcThemeConfig' || $request->getParam('controller') !== 'ThemeConfigs') return;
        if (BcUtil::getCurrentTheme() !== 'SmartBlogTheme') return;

        $fields = (array)$event->getData('fields');
        foreach ($fields as $field) {
            // 二重に登録されたときに、同じ項目を2回出さない
            if (str_contains((string)($field['input'] ?? ''), 'bt_color_scheme')) return;
        }
        $form = $view->BcAdminForm;
        // テーマ設定の画面の既存の行は class のない <th>・<td>（見出しは太字・中央寄せ、下線なし）。
        // 差し込む行には管理画面の標準の class（bca-form-table__label・__input）が付き、下線が出るので、この2行だけ既存の行にそろえる
        $style = '<style>'
            . 'tr:has([name="bt_color_scheme"]) > *, tr:has([name="bt_scheme_toggle"]) > *, tr:has([name="bt_nav_source"]) > *, tr:has([name="bt_header_cta_label"]) > * { border: 0 !important; box-shadow: none !important; }'
            . 'tr:has([name="bt_color_scheme"]) > th, tr:has([name="bt_scheme_toggle"]) > th, tr:has([name="bt_nav_source"]) > th, tr:has([name="bt_header_cta_label"]) > th { text-align: center; font-weight: 700; }'
            . '</style>';
        $title = fn(string $field, string $text): string => $form->label($field, $text);
        $mode = (string)Configure::read('BlogTheme.colorScheme', 'auto');
        $toggle = Configure::read('BlogTheme.schemeToggle', true)? '1' : '0';
        $fields[] = [
            'title' => $title('bt_color_scheme', __d('baser_core', '配色モード')),
            'input' => $form->control('bt_color_scheme', [
                    'type' => 'select',
                    'options' => [
                        'auto' => __d('baser_core', 'OS の設定に合わせる'),
                        'light' => __d('baser_core', 'ライト'),
                        'dark' => __d('baser_core', 'ダーク'),
                    ],
                    'default' => $mode,
                ]) .
                '<p class="bca-form-table__note">' . __d('baser_core', 'サイトを開いたときの配色です。切り替えボタンを表示するときは、閲覧者が選んだ配色が優先されます。') . '</p>' . $style,
        ];
        $fields[] = [
            'title' => $title('bt_scheme_toggle', __d('baser_core', 'ライト／ダークの切り替えボタン')),
            'input' => $form->control('bt_scheme_toggle', [
                    'type' => 'radio',
                    'options' => ['1' => __d('baser_core', '表示する'), '0' => __d('baser_core', '表示しない')],
                    'default' => $toggle,
                ]) .
                '<p class="bca-form-table__note">' . __d('baser_core', '表示しないときは、配色モードの配色に固定されます（「OS の設定に合わせる」なら OS に従います）。') . '</p>',
        ];
        $fields[] = [
            'title' => $title('bt_nav_source', __d('baser_core', 'ヘッダーのナビ')),
            'input' => $form->control('bt_nav_source', [
                    'type' => 'select',
                    'options' => [
                        'contents' => __d('baser_core', 'コンテンツ管理のメニュー'),
                        'categories' => __d('baser_core', 'ブログのカテゴリ＋固定ページなど'),
                    ],
                    'default' => (string)Configure::read('BlogTheme.navSource', 'contents'),
                ]) .
                '<p class="bca-form-table__note">' . __d('baser_core', '「コンテンツ管理のメニュー」は、ルート直下のブログ・固定ページ・リンク・メールフォームを並び順どおりに出します（「メニューに表示しない」は除く）。「ブログのカテゴリ＋固定ページなど」は、最初のブログのカテゴリと、ルート直下の固定ページ・メールフォーム・リンクを出します。') . '</p>',
        ];
        $cta = (array)Configure::read('BlogTheme.headerCta', []);
        $fields[] = [
            'title' => $title('bt_header_cta_label', __d('baser_core', 'ヘッダーのボタン')),
            'input' => $form->control('bt_header_cta_label', [
                    'type' => 'text',
                    'size' => 20,
                    'placeholder' => __d('baser_core', '例：資料請求'),
                    'default' => (string)($cta['label'] ?? ''),
                ]) . ' ' .
                $form->control('bt_header_cta_url', [
                    'type' => 'text',
                    'size' => 40,
                    'placeholder' => __d('baser_core', '例：/contact/?purpose=資料請求'),
                    'default' => (string)($cta['url'] ?? ''),
                ]) .
                '<p class="bca-form-table__note">' . __d('baser_core', 'ヘッダーの右に目立つボタンを出します（左：ボタンの文字、右：リンク先）。どちらかが空なら出しません。メールフォームへのリンクに「?項目名=値」を付けると、その値を選んだ状態で開きます。') . '</p>',
        ];
        $event->setData('fields', $fields);
    }

}
