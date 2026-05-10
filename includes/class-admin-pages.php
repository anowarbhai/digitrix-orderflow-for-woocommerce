<?php
/**
 * Admin page proxy class.
 *
 * The production plugin currently uses legacy procedural callbacks from the
 * main plugin file. This class keeps a clean compatibility surface for future
 * page extraction without registering duplicate menus.
 */

if (!defined('ABSPATH')) {
 exit;
}

class AOAM_Admin_Pages {
 public function dashboard() {
  if (function_exists('moderator_settings_main_page')) {
   moderator_settings_main_page();
  }
 }

 public function recent_assignments() {
  if (function_exists('moderator_recent_assignments_page')) {
   moderator_recent_assignments_page();
  }
 }

 public function sequence_status() {
  if (function_exists('moderator_sequence_status_page')) {
   moderator_sequence_status_page();
  }
 }

 public function product_assignments() {
  if (function_exists('moderator_product_assignments_page')) {
   moderator_product_assignments_page();
  }
 }

 public function plugin_settings() {
  if (function_exists('aoam_plugin_settings_page')) {
   aoam_plugin_settings_page();
  }
 }

 public function remote_import() {
  if (function_exists('aoam_remote_order_import_page')) {
   aoam_remote_order_import_page();
  }
 }

 public function reassign_orders() {
  if (function_exists('moderator_reassign_orders_page')) {
   moderator_reassign_orders_page();
  }
 }
}
