<?php
/**
 * @see ../event/render.php
 */

use Yoyaku\Infrastructure\WP\BlockService\CancelBookingBlockService;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

CancelBookingBlockService::prepare_scripts_and_styles($attributes);
?>

<div id="yoyaku-block-cancel-booking"></div>
