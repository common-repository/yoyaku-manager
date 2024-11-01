<?php
/**
 * フロントエンドに表示するためにサーバー上でブロックタイプをレンダリングするときに使用するファイル
 * 以下の変数が使える
 * $attributes (array): The block attributes.
 * $content (string): The block default content.
 * $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * プラグインの設定を即時反映させるためにダイナミックレンダリングを使う
 * @see https://developer.wordpress.org/block-editor/getting-started/fundamentals/static-dynamic-rendering/
 *
 * jsのbool型の変数にする場合は、var_export(true, true) でphpのbool型を文字列にする必要がある
 */

use Yoyaku\Infrastructure\WP\BlockService\EventBlockService;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

EventBlockService::prepare_scripts_and_styles($attributes);
?>

<div id="yoyaku-block-event"></div>
