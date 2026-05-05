<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Core functions for Auto Order Assign To Moderator
 */

/**
 * Initialize moderator status for existing moderators
 */
function aoam_initialize_moderator_status() {
    $moderators = get_users(array('role' => 'moderator'));
    
    foreach ($moderators as $moderator) {
        $current_status = get_user_meta($moderator->ID, 'moderator_status', true);
        if (empty($current_status)) {
            update_user_meta($moderator->ID, 'moderator_status', 'active');
        }
    }
}

/**
 * Send notification to moderator when assigned an order
 */
function aoam_send_moderator_notification($moderator, $order) {
    $to = $moderator->user_email;
    $moderator_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true);
    $subject = sprintf(__('New Order Assigned to You - Moderator %s', 'auto-order-assign-moderator'), $moderator_sequence);
    
    $message = sprintf(
        __("Hello %s,\n\nA new order has been assigned to you!\n\nOrder Details:\nOrder #: %s\nCustomer: %s\nTotal: %s\nDate: %s\n\nPlease process this order promptly.\n\nLogin to your dashboard to view details: %s\n\nThank you!\n", 'auto-order-assign-moderator'),
        $moderator->display_name,
        $order->get_id(),
        $order->get_formatted_billing_full_name(),
        $order->get_formatted_order_total(),
        $order->get_date_created()->format('F j, Y g:i A'),
        admin_url('edit.php?post_type=shop_order')
    );

    wp_mail($to, $subject, $message);
    
    // Update last active timestamp
    update_user_meta($moderator->ID, 'moderator_last_active', current_time('mysql'));
}

/**
 * Get eligible moderators for product assignment
 */
function aoam_get_eligible_moderators($product_ids) {
    $active_moderators = get_users(array(
        'role' => 'moderator',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'moderator_sequence',
                'type' => 'NUMERIC',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => 'moderator_status',
                'value' => 'active',
                'compare' => '='
            )
        ),
        'orderby' => 'meta_value_num',
        'meta_key' => 'moderator_sequence',
        'order' => 'ASC',
        'number' => 10
    ));
    
    // Fallback if no active moderators with sequence
    if (empty($active_moderators)) {
        $active_moderators = get_users(array(
            'role' => 'moderator',
            'meta_key' => 'moderator_sequence',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'number' => 10
        ));
    }
    
    // Final fallback - get by registration date
    if (empty($active_moderators)) {
        $active_moderators = get_users(array(
            'role' => 'moderator',
            'orderby' => 'user_registered',
            'order' => 'ASC',
            'number' => 10
        ));
        
        // Add sequence numbers based on registration order
        $sequence = 1;
        foreach ($active_moderators as $moderator) {
            update_user_meta($moderator->ID, 'moderator_sequence', $sequence);
            $sequence++;
        }
    }
    
    // Filter out inactive moderators and sort by sequence
    $filtered_moderators = array();
    foreach ($active_moderators as $moderator) {
        $status = get_user_meta($moderator->ID, 'moderator_status', true);
        if ($status !== 'inactive') {
            $filtered_moderators[] = $moderator;
        }
    }
    
    // Sort by sequence to ensure correct order
    usort($filtered_moderators, function($a, $b) {
        $seq_a = get_user_meta($a->ID, 'moderator_sequence', true);
        $seq_b = get_user_meta($b->ID, 'moderator_sequence', true);
        return $seq_a - $seq_b;
    });
    
    return $filtered_moderators;
}

/**
 * AJAX handler for order details
 */
function aoam_get_moderator_order_details_simple() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'moderator_order_details')) {
        wp_send_json_error('Security check failed');
    }
    
    $order_id = intval($_POST['order_id']);
    $current_user = wp_get_current_user();
    
    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error('Order not found');
    }
    
    ob_start();
    ?>
    <div class="order-details-container">
        <!-- Order Summary -->
        <div class="order-details-section">
            <h4><?php _e('Order Summary', 'auto-order-assign-moderator'); ?></h4>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 30%;"><strong><?php _e('Order Date:', 'auto-order-assign-moderator'); ?></strong></td>
                    <td><?php echo $order->get_date_created()->format('F j, Y g:i A'); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Order Status:', 'auto-order-assign-moderator'); ?></strong></td>
                    <td>
                        <span class="order-status-badge status-<?php echo $order->get_status(); ?>">
                            <?php echo wc_get_order_status_name($order->get_status()); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php _e('Order Total:', 'auto-order-assign-moderator'); ?></strong></td>
                    <td><?php echo $order->get_formatted_order_total(); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Payment Method:', 'auto-order-assign-moderator'); ?></strong></td>
                    <td><?php echo $order->get_payment_method_title(); ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Customer Information -->
        <div class="order-details-section">
            <h4><?php _e('Customer Information', 'auto-order-assign-moderator'); ?></h4>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 30%;"><strong><?php _e('Name:', 'auto-order-assign-moderator'); ?></strong></td>
                    <td><?php echo $order->get_formatted_billing_full_name(); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Address:', 'auto-order-assign-moderator'); ?></strong></td>
                    <td><?php echo $order->get_billing_address_1(); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Phone:', 'auto-order-assign-moderator'); ?></strong></td>
                    <td><?php echo $order->get_billing_phone(); ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Order Items -->
        <div class="order-details-section">
            <h4><?php _e('Order Items', 'auto-order-assign-moderator'); ?></h4>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f1f1f1;">
                        <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;"><?php _e('Product', 'auto-order-assign-moderator'); ?></th>
                        <th style="padding: 8px; text-align: center; border-bottom: 1px solid #ddd;"><?php _e('Quantity', 'auto-order-assign-moderator'); ?></th>
                        <th style="padding: 8px; text-align: right; border-bottom: 1px solid #ddd;"><?php _e('Total', 'auto-order-assign-moderator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order->get_items() as $item): ?>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #eee;">
                            <?php echo $item->get_name(); ?>
                        </td>
                        <td style="padding: 8px; text-align: center; border-bottom: 1px solid #eee;">
                            <?php echo $item->get_quantity(); ?>
                        </td>
                        <td style="padding: 8px; text-align: right; border-bottom: 1px solid #eee;">
                            <?php echo wc_price($item->get_total()); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    wp_send_json_success($content);
}
add_action('wp_ajax_get_moderator_order_details_simple', 'aoam_get_moderator_order_details_simple');