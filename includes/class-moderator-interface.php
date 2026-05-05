<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AOAM_Moderator_Interface {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'simple_moderator_orders_menu'));
        add_action('admin_menu', array($this, 'remove_woocommerce_menus_for_moderators'), 999);
    }
    
    /**
     * Create simple orders menu for moderators
     */
    public function simple_moderator_orders_menu() {
        $current_user = wp_get_current_user();
        
        if (in_array('moderator', $current_user->roles)) {
            add_menu_page(
                __('My Orders', 'auto-order-assign-moderator'),
                __('My Orders', 'auto-order-assign-moderator'), 
                'read',
                'moderator-simple-orders',
                array($this, 'simple_moderator_orders_page'),
                'dashicons-clipboard',
                25
            );
        }
    }
    
    /**
     * Simple orders page for moderators
     */
    public function simple_moderator_orders_page() {
        $current_user = wp_get_current_user();
        $moderator_id = $current_user->ID;
        
        // Get status filter from URL
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
        
        echo '<div class="wrap"><h1>' . sprintf(__('My Orders - %s', 'auto-order-assign-moderator'), $current_user->display_name) . '</h1>';
        
        // Get orders assigned to this moderator using direct SQL
        global $wpdb;
        $order_ids = $wpdb->get_col($wpdb->prepare("
            SELECT post_id 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_assigned_moderator_id' 
            AND meta_value = %d
            ORDER BY meta_id DESC
        ", $moderator_id));
        
        // Convert to WC_Order objects and apply status filter
        $orders = array();
        $status_counts = array(
            'all' => 0,
            'pending' => 0,
            'processing' => 0,
            'on-hold' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'refunded' => 0
        );
        
        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $order_status = $order->get_status();
                $status_counts['all']++;
                if (isset($status_counts[$order_status])) {
                    $status_counts[$order_status]++;
                }
                
                // Apply status filter
                if ($status_filter === 'all' || $order_status === $status_filter) {
                    $orders[] = $order;
                }
            }
        }
        
        // Status filter tabs
        echo '<div class="order-status-filters">';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="' . add_query_arg('status', 'all') . '" class="nav-tab ' . ($status_filter === 'all' ? 'nav-tab-active' : '') . '">' . sprintf(__('All (%d)', 'auto-order-assign-moderator'), $status_counts['all']) . '</a>';
        echo '<a href="' . add_query_arg('status', 'pending') . '" class="nav-tab ' . ($status_filter === 'pending' ? 'nav-tab-active' : '') . '">' . sprintf(__('Pending (%d)', 'auto-order-assign-moderator'), $status_counts['pending']) . '</a>';
        echo '<a href="' . add_query_arg('status', 'processing') . '" class="nav-tab ' . ($status_filter === 'processing' ? 'nav-tab-active' : '') . '">' . sprintf(__('Processing (%d)', 'auto-order-assign-moderator'), $status_counts['processing']) . '</a>';
        echo '<a href="' . add_query_arg('status', 'on-hold') . '" class="nav-tab ' . ($status_filter === 'on-hold' ? 'nav-tab-active' : '') . '">' . sprintf(__('On Hold (%d)', 'auto-order-assign-moderator'), $status_counts['on-hold']) . '</a>';
        echo '<a href="' . add_query_arg('status', 'completed') . '" class="nav-tab ' . ($status_filter === 'completed' ? 'nav-tab-active' : '') . '">' . sprintf(__('Completed (%d)', 'auto-order-assign-moderator'), $status_counts['completed']) . '</a>';
        echo '<a href="' . add_query_arg('status', 'cancelled') . '" class="nav-tab ' . ($status_filter === 'cancelled' ? 'nav-tab-active' : '') . '">' . sprintf(__('Cancelled (%d)', 'auto-order-assign-moderator'), $status_counts['cancelled']) . '</a>';
        echo '</h2>';
        echo '</div>';
        
        if (empty($orders)) {
            echo '<div class="notice notice-info"><p>';
            if ($status_filter !== 'all') {
                echo sprintf(__('No orders found with status: %s.', 'auto-order-assign-moderator'), $status_filter);
            } else {
                _e('No orders found.', 'auto-order-assign-moderator');
            }
            echo '</p></div>';
            echo '</div>';
            return;
        }
        
        // Display orders count
        echo '<div class="card" style="background: #fff; padding: 15px; margin: 10px 0; border-left: 4px solid #46b450;max-width:100%;">';
        echo '<h3>📦 ';
        if ($status_filter === 'all') {
            echo sprintf(__('All Your Orders: %d', 'auto-order-assign-moderator'), count($orders));
        } else {
            echo sprintf(__('%s Orders: %d', 'auto-order-assign-moderator'), ucfirst($status_filter), count($orders));
        }
        echo '</h3>';
        echo '</div>';
        
        // The rest of the orders table implementation would go here
        // For brevity, I'm showing the structure
        
        echo '</div>';
    }
    
    /**
     * Remove WooCommerce menus for moderators
     */
    public function remove_woocommerce_menus_for_moderators() {
        $current_user = wp_get_current_user();
        
        if (in_array('moderator', $current_user->roles)) {
            remove_menu_page('woocommerce');
            remove_menu_page('edit.php?post_type=shop_order');
            remove_menu_page('wc-admin');
            remove_menu_page('woocommerce-marketing');
            remove_menu_page('admin.php?page=wc-settings&tab=checkout&from=PAYMENTS_MENU_ITEM');
            remove_menu_page('tools.php');
            remove_menu_page('edit-comments.php');
            remove_menu_page('edit.php');
        }
    }
}