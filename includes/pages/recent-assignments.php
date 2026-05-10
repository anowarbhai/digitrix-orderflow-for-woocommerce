<?php
/**
 * Recent Assignments admin page entry point.
 */

if (!defined('ABSPATH')) {
 exit;
}

function aoam_recent_assignments_page() {
 if (function_exists('moderator_recent_assignments_page')) {
 moderator_recent_assignments_page();
 }
}
