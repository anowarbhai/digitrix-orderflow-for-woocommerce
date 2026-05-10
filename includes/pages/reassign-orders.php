<?php
/**
 * Reassign Orders admin page entry point.
 */

if (!defined('ABSPATH')) {
 exit;
}

function aoam_reassign_orders_page() {
 if (function_exists('moderator_reassign_orders_page')) {
 moderator_reassign_orders_page();
 }
}
