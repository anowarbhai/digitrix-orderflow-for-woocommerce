<?php
/**
 * Sequence & Status admin page entry point.
 */

if (!defined('ABSPATH')) {
 exit;
}

function aoam_sequence_status_page() {
 if (function_exists('moderator_sequence_status_page')) {
 moderator_sequence_status_page();
 }
}
