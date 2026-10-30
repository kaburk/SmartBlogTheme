<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * スタイルの設定（scripts/new-blog-theme.sh が variants/smart から書き出す）
 */
return [
    'BlogTheme' => [
        // スタイル名（<html data-variant>）
        'variant' => 'smart',
        // Google Fonts（layout で読み込む）
        'fonts' => 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Murecho:wght@400;500;700&display=swap',
        // 配色モードの初期値（auto = OS の設定に合わせる／light = ライト／dark = ダーク）
        // 管理画面の「テーマ設定」で変えられる（テーマ設定の値が優先）
        'colorScheme' => 'light',
        // ヘッダーにライト／ダークの切り替えボタンを出す（テーマ設定で変えられる）
        'schemeToggle' => true,
        // ヘッダー・フッターのナビの作り方（テーマ設定で変えられる）
        //   contents = コンテンツ管理のルート直下（ブログ・固定ページ・リンク・メールフォームなど）
        //   categories = 最初のブログのカテゴリ（第1階層）＋ルート直下の固定ページ・メールフォーム・リンク
        'navSource' => 'contents',
        // トップの一覧の上に「タグから探す」を出す
        'tagStrip' => false,
        // 記事の目次（PC はサイドバー、スマホは本文の上）
        'toc' => true,
        // 記事の上のシェア（下のシェアは常に出す）
        'shareTop' => true,
        // アイキャッチも本文の画像もない記事のカード：frame = 色の枠＋カテゴリ名／none = 画像の場所を出さない（文字だけ）
        'thumbFallback' => 'frame',
        // カード・記事の日付の書式（PHP の date の書式）
        'dateFormat' => 'Y.m.d',
        // テーマ設定の4色の初期値（css の代わりの値にも使う）
        'colors' => ['main' => '#001800', 'sub' => '#333333', 'link' => '#333333', 'hover' => '#c63d2f'],
    ],
];
