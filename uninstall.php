<?php
/**
 * Uninstall guard for Digitrix OrderFlow for WooCommerce.
 *
 * This plugin stores operational order assignment data. Data is intentionally
 * preserved on uninstall to avoid losing order audit history.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
 exit;
}

// Intentionally no destructive cleanup.
