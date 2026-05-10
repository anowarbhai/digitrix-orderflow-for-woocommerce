<?php
/**
 * Product Assignments admin page entry point.
 */

if (!defined('ABSPATH')) {
 exit;
}

function aoam_product_assignments_page() {
 if (function_exists('moderator_product_assignments_page')) {
 moderator_product_assignments_page();
 }
}
