<?php
/**
 * Remote Import admin page entry point.
 */

if (!defined('ABSPATH')) {
 exit;
}

function aoam_remote_import_page() {
 if (function_exists('aoam_remote_order_import_page')) {
 aoam_remote_order_import_page();
 }
}
