<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AOAM_Order_Assignment {
    
    public function __construct() {
        add_action('woocommerce_new_order', array($this, 'assign_order_to_specific_moderator'), 10, 2);
        add_action('woocommerce_admin_order_data_after_order_details', array($this, 'add_moderator_section_after_order_details'));
        add_action('wp_ajax_update_order_moderator_direct', array($this, 'handle_update_order_moderator_direct'));
        
        // Filter orders for moderators
        add_action('pre_get_posts', array($this, 'filter_orders_for_moderators_by_assignment'));
        
        // Add moderator columns to orders list
        add_filter('manage_edit-shop_order_columns', array($this, 'add_moderator_sequence_column'));
        add_action('manage_shop_order_posts_custom_column', array($this, 'show_moderator_sequence_in_column'), 10, 2);
    }
    
    /**
     * Main order assignment function
     */
    public function assign_order_to_specific_moderator($order_id, $order) {
        // Get order items to determine products
        $items = $order->get_items();
        $product_ids = array();
        
        foreach ($items as $item) {
            $product_ids[] = $item->get_product_id();
        }
        
        // Sort product IDs to maintain consistent key
        sort($product_ids);
        
        // Get active moderators
        $moderators = aoam_get_eligible_moderators($product_ids);
        
        if (empty($moderators)) {
            $order->add_order_note(__('No moderators found for assignment.', 'auto-order-assign-moderator'));
            return;
        }
        
        // NEW LOGIC: Find moderators who are assigned to these specific products
        $eligible_moderators = array();
        
        foreach ($moderators as $moderator) {
            $assigned_products = get_user_meta($moderator->ID, 'moderator_assigned_products', true);
            
            // MODIFIED: If moderator has no specific products assigned, SKIP them
            if (empty($assigned_products)) {
                continue; // Skip moderators with no product assignments
            }
            
            // Check if any product in this order matches moderator's assigned products
            $common_products = array_intersect($product_ids, $assigned_products);
            if (!empty($common_products)) {
                $eligible_moderators[] = $moderator;
            }
        }
        
        // If no moderators found for these specific products, add order note and return
        if (empty($eligible_moderators)) {
            $order->add_order_note(__('No moderators assigned for the products in this order. Order not assigned to any moderator.', 'auto-order-assign-moderator'));
            return;
        }
        
        // NEW: Get product-specific last assigned sequence
        $product_specific_sequence_key = 'last_assigned_moderator_sequence_products_' . implode('_', $product_ids);
        $last_assigned_sequence = get_option($product_specific_sequence_key, 0);
        
        // Find the next moderator based on SEQUENCE from eligible moderators
        $next_sequence = $last_assigned_sequence + 1;
        
        // If next sequence exceeds max sequence, reset to 1
        $max_sequence = 0;
        foreach ($eligible_moderators as $moderator) {
            $current_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true);
            if ($current_sequence > $max_sequence) {
                $max_sequence = $current_sequence;
            }
        }
        
        if ($next_sequence > $max_sequence) {
            $next_sequence = 1;
        }
        
        // Find moderator with the next sequence from eligible moderators
        $assigned_moderator = null;
        foreach ($eligible_moderators as $moderator) {
            $current_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true);
            if ($current_sequence == $next_sequence) {
                $assigned_moderator = $moderator;
                break;
            }
        }
        
        // If no moderator found with exact sequence, get the next available one
        if (!$assigned_moderator) {
            // Sort eligible moderators by sequence
            usort($eligible_moderators, function($a, $b) {
                $seq_a = get_user_meta($a->ID, 'moderator_sequence', true);
                $seq_b = get_user_meta($b->ID, 'moderator_sequence', true);
                return $seq_a - $seq_b;
            });
            
            // Find the first moderator with sequence >= next_sequence
            foreach ($eligible_moderators as $moderator) {
                $current_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true);
                if ($current_sequence >= $next_sequence) {
                    $assigned_moderator = $moderator;
                    $next_sequence = $current_sequence;
                    break;
                }
            }
            
            // If still no moderator, get the first one
            if (!$assigned_moderator) {
                $assigned_moderator = $eligible_moderators[0];
                $next_sequence = get_user_meta($assigned_moderator->ID, 'moderator_sequence', true);
            }
        }
        
        // Update order with moderator information
        update_post_meta($order_id, '_assigned_moderator_id', $assigned_moderator->ID);
        update_post_meta($order_id, '_assigned_moderator_name', $assigned_moderator->display_name);
        update_post_meta($order_id, '_assigned_moderator_sequence', $next_sequence);
        
        // NEW: Update the product-specific last assigned moderator SEQUENCE
        update_option($product_specific_sequence_key, $next_sequence);
        
        // Add order note with product information
        $assigned_products = get_user_meta($assigned_moderator->ID, 'moderator_assigned_products', true);
        $product_note = '';
        if (!empty($assigned_products)) {
            $product_names = array();
            foreach ($assigned_products as $product_id) {
                $product = wc_get_product($product_id);
                if ($product) {
                    $product_names[] = $product->get_name();
                }
            }
            $product_note = ' (' . __('Specialized in:', 'auto-order-assign-moderator') . ' ' . implode(', ', $product_names) . ')';
        }
        
        $order->add_order_note(sprintf(
            __('Order automatically assigned to Moderator %d: %s%s - Product-specific (Last: %d, Next: %d)', 'auto-order-assign-moderator'),
            $next_sequence,
            $assigned_moderator->display_name,
            $product_note,
            $last_assigned_sequence,
            $next_sequence
        ));
        
        // Optional: Send email to moderator
        aoam_send_moderator_notification($assigned_moderator, $order);
    }
    
    /**
     * Add moderator section to order edit page
     */
    public function add_moderator_section_after_order_details($order) {
        $order_id = $order->get_id();
        $current_moderator_id = get_post_meta($order_id, '_assigned_moderator_id', true);
        $current_moderator_name = get_post_meta($order_id, '_assigned_moderator_name', true);
        
        ?>
        <div class="order_data_column" style="width: 100%; margin: 20px 0; padding: 20px; background: white; border: 1px solid #ccd0d4; border-radius: 4px;">
            <h3 style="margin-top: 0; color: #0073aa;">🔀 <?php _e('Assign Moderator', 'auto-order-assign-moderator'); ?></h3>
            
            <div style="margin-bottom: 15px;">
                <p><strong><?php _e('Current Moderator:', 'auto-order-assign-moderator'); ?></strong></p>
                <?php if ($current_moderator_name): ?>
                    <div style="padding: 10px; background: #f0f6fc; border-radius: 4px; border-left: 4px solid #0073aa;">
                        <strong style="font-size: 16px; color: #0073aa;"><?php echo esc_html($current_moderator_name); ?></strong>
                        <?php 
                        $moderator_sequence = get_user_meta($current_moderator_id, 'moderator_sequence', true);
                        if ($moderator_sequence) {
                            echo ' <span style="color: #666;">(' . sprintf(__('Moderator %s', 'auto-order-assign-moderator'), $moderator_sequence) . ')</span>';
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <div style="padding: 10px; background: #fff8e1; border-radius: 4px; border-left: 4px solid #ffb900;">
                        <span style="color: #cc1818; font-style: italic;"><?php _e('Not assigned to any moderator', 'auto-order-assign-moderator'); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php
            // Get all active moderators
            $moderators = get_users(array(
                'role' => 'moderator',
                'meta_key' => 'moderator_status',
                'meta_value' => 'active',
                'orderby' => 'display_name'
            ));
            ?>
            
            <div class="form-field">
                <label for="assigned_moderator_direct"><strong><?php _e('Change Moderator:', 'auto-order-assign-moderator'); ?></strong></label>
                <select name="assigned_moderator_direct" id="assigned_moderator_direct" style="width: 100%; margin-top: 5px;">
                    <option value="">— <?php _e('Select New Moderator', 'auto-order-assign-moderator'); ?> —</option>
                    <?php foreach ($moderators as $moderator): 
                        $moderator_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true);
                        $display_name = $moderator->display_name . ($moderator_sequence ? ' (' . sprintf(__('Moderator %s', 'auto-order-assign-moderator'), $moderator_sequence) . ')' : '');
                    ?>
                        <option value="<?php echo $moderator->ID; ?>" <?php selected($current_moderator_id, $moderator->ID); ?>>
                            <?php echo esc_html($display_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <p style="margin-top: 15px;">
                <button type="button" id="update_moderator_direct" class="button button-primary">
                    🔄 <?php _e('Update Moderator', 'auto-order-assign-moderator'); ?>
                </button>
            </p>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#update_moderator_direct').click(function() {
                var newModeratorId = $('#assigned_moderator_direct').val();
                var orderId = <?php echo $order_id; ?>;
                
                if (!newModeratorId) {
                    alert('❌ <?php _e('Please select a moderator.', 'auto-order-assign-moderator'); ?>');
                    return;
                }
                
                if (confirm('<?php _e('Are you sure you want to change the assigned moderator?', 'auto-order-assign-moderator'); ?>')) {
                    var $button = $(this);
                    $button.text('<?php _e('Updating...', 'auto-order-assign-moderator'); ?>').prop('disabled', true);
                    
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'update_order_moderator_direct',
                            order_id: orderId,
                            moderator_id: newModeratorId,
                            nonce: '<?php echo wp_create_nonce('direct_update_moderator'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('✅ <?php _e('Moderator updated successfully!', 'auto-order-assign-moderator'); ?>');
                                location.reload();
                            } else {
                                alert('❌ <?php _e('Error:', 'auto-order-assign-moderator'); ?> ' + response.data);
                                $button.text('🔄 <?php _e('Update Moderator', 'auto-order-assign-moderator'); ?>').prop('disabled', false);
                            }
                        },
                        error: function() {
                            alert('❌ <?php _e('Network error. Please try again.', 'auto-order-assign-moderator'); ?>');
                            $button.text('🔄 <?php _e('Update Moderator', 'auto-order-assign-moderator'); ?>').prop('disabled', false);
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX handler for direct moderator update
     */
    public function handle_update_order_moderator_direct() {
        if (!wp_verify_nonce($_POST['nonce'], 'direct_update_moderator')) {
            wp_send_json_error('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $order_id = intval($_POST['order_id']);
        $moderator_id = intval($_POST['moderator_id']);
        
        $order = wc_get_order($order_id);
        $moderator = get_userdata($moderator_id);
        
        if (!$order || !$moderator) {
            wp_send_json_error('Invalid order or moderator');
        }
        
        // Get current moderator before update
        $old_moderator_id = get_post_meta($order_id, '_assigned_moderator_id', true);
        $old_moderator_name = get_post_meta($order_id, '_assigned_moderator_name', true);
        
        // Update moderator information
        update_post_meta($order_id, '_assigned_moderator_id', $moderator_id);
        update_post_meta($order_id, '_assigned_moderator_name', $moderator->display_name);
        
        // Get moderator sequence for note
        $moderator_sequence = get_user_meta($moderator_id, 'moderator_sequence', true);
        
        // Add order note
        $order_note = sprintf(
            __('Assigned moderator changed from %s to %s (Moderator %s) by admin: %s', 'auto-order-assign-moderator'),
            $old_moderator_name ?: __('None', 'auto-order-assign-moderator'),
            $moderator->display_name,
            $moderator_sequence ?: 'N/A',
            wp_get_current_user()->display_name
        );
        
        $order->add_order_note($order_note);
        
        wp_send_json_success(__('Moderator updated successfully', 'auto-order-assign-moderator'));
    }
    
    /**
     * Filter orders for moderators - they only see their own orders
     */
    public function filter_orders_for_moderators_by_assignment($query) {
        if (!is_admin() || !$query->is_main_query()) return;
        
        $current_user = wp_get_current_user();
        
        if (in_array('moderator', $current_user->roles) && 
            isset($query->query['post_type']) && 
            $query->query['post_type'] === 'shop_order') {
            
            $meta_query = array(
                array(
                    'key' => '_assigned_moderator_id',
                    'value' => $current_user->ID,
                    'compare' => '='
                )
            );
            
            $query->set('meta_query', $meta_query);
        }
    }
    
    /**
     * Add moderator sequence column to orders list
     */
    public function add_moderator_sequence_column($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $column) {
            $new_columns[$key] = $column;
            if ($key === 'order_status') {
                $new_columns['assigned_moderator_seq'] = __('Moderator #', 'auto-order-assign-moderator');
                $new_columns['moderator_status'] = __('Mod Status', 'auto-order-assign-moderator');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Show moderator sequence in orders list column
     */
    public function show_moderator_sequence_in_column($column, $post_id) {
        if ($column === 'assigned_moderator_seq') {
            $sequence = get_post_meta($post_id, '_assigned_moderator_sequence', true);
            if ($sequence) {
                echo '<strong>' . sprintf(__('Moderator %s', 'auto-order-assign-moderator'), esc_html($sequence)) . '</strong>';
            } else {
                echo '<span style="color:#ccc;">' . __('Not assigned', 'auto-order-assign-moderator') . '</span>';
            }
        }
        
        if ($column === 'moderator_status') {
            $moderator_id = get_post_meta($post_id, '_assigned_moderator_id', true);
            if ($moderator_id) {
                $status = get_user_meta($moderator_id, 'moderator_status', true) ?: 'active';
                if ($status === 'active') {
                    echo '<span style="color:green; font-weight:bold;">● ' . __('Active', 'auto-order-assign-moderator') . '</span>';
                } else {
                    echo '<span style="color:red; font-weight:bold;">● ' . __('Inactive', 'auto-order-assign-moderator') . '</span>';
                }
            } else {
                echo '<span style="color:#ccc;">N/A</span>';
            }
        }
    }
}