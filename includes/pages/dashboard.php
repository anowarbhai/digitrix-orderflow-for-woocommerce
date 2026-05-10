<?php
/**
 * Dashboard admin page entry point.
 */

if (!defined('ABSPATH')) {
 exit;
}

function aoam_dashboard_page() {
 if (function_exists('moderator_settings_main_page')) {
 moderator_settings_main_page();
 }
}
