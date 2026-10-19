<?php
/**
 * SmartBlogTheme : baserCMS5 ブログテーマ（スマート）
 *
 * テーマの読み込み時の設定
 * - <meta name="generator"> を出さない（使っている CMS とバージョンを公開しない）
 */
use Cake\Core\Configure;

Configure::write('BcApp.outputMetaGenerator', false);
