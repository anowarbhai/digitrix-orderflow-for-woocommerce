<?php
/**
 * Plugin Settings admin page entry point.
 */

if (!defined('ABSPATH')) {
 exit;
}

function aoam_settings_page() {
 if (function_exists('aoam_plugin_settings_page')) {
 aoam_plugin_settings_page();
 }
}
