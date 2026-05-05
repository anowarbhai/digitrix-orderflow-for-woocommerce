<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AOAM_Admin_Pages {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'moderator_sequence_settings_menu'));
        add_action('admin_init', array($this, 'initialize_moderator_status'));
    }
    
    /**
     * Initialize moderator status for existing moderators
     */
    public function initialize_moderator_status() {
        $moderators = get_users(array('role' => 'moderator'));
        
        foreach ($moderators as $moderator) {
            $current_status = get_user_meta($moderator->ID, 'moderator_status', true);
            if (empty($current_status)) {
                update_user_meta($moderator->ID, 'moderator_status', 'active');
            }
        }
    }
    
    /**
     * Create admin menu structure
     */
    public function moderator_sequence_settings_menu() {
        add_menu_page(
            __('Order Management', 'auto-order-assign-moderator'),
            __('Order Management', 'auto-order-assign-moderator'),
            'manage_options',
            'moderator-settings',
            array($this, 'moderator_settings_main_page'),
            'dashicons-sort',
            56
        );
        
        // Submenu for Recent Assignments
        add_submenu_page(
            'moderator-settings',
            __('Recent Assignments', 'auto-order-assign-moderator'),
            __('Recent Assignments', 'auto-order-assign-moderator'),
            'manage_options',
            'moderator-recent-assignments',
            array($this, 'moderator_recent_assignments_page')
        );
        
        // Submenu for Sequence & Status
        add_submenu_page(
            'moderator-settings',
            __('Moderator Sequence & Status', 'auto-order-assign-moderator'),
            __('Sequence & Status', 'auto-order-assign-moderator'),
            'manage_options',
            'moderator-sequence-status',
            array($this, 'moderator_sequence_status_page')
        );
        
        // Submenu for Product Assignments
        add_submenu_page(
            'moderator-settings',
            __('Assign Products to Moderators', 'auto-order-assign-moderator'),
            __('Product Assignments', 'auto-order-assign-moderator'),
            'manage_options',
            'moderator-product-assignments',
            array($this, 'moderator_product_assignments_page')
        );
    }
    
    /**
     * Main settings page
     */
    public function moderator_settings_main_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Moderator Settings', 'auto-order-assign-moderator'); ?></h1>
            
            <div class="card" style="text-align: center; padding: 40px;">
                <h2>🎛️ <?php _e('Moderator Management Dashboard', 'auto-order-assign-moderator'); ?></h2>
                <p><?php _e('Choose a section to manage your moderators and order assignments:', 'auto-order-assign-moderator'); ?></p>
                <div style="background: #f0f6ff; padding: 20px; border-radius: 8px; margin-top: 30px;">
                    <h3>📊 <?php _e('Quick Stats', 'auto-order-assign-moderator'); ?></h3>
                    <?php
                    $moderators = get_users(array('role' => 'moderator'));
                    $active_moderators = array_filter($moderators, function($moderator) {
                        $status = get_user_meta($moderator->ID, 'moderator_status', true);
                        return $status !== 'inactive';
                    });
                    
                    $moderators_with_products = array_filter($moderators, function($moderator) {
                        $products = get_user_meta($moderator->ID, 'moderator_assigned_products', true);
                        return !empty($products);
                    });
                    ?>
                    <div style="display: flex; justify-content: center; gap: 30px; margin-top: 20px;">
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #0073aa;"><?php echo count($moderators); ?></div>
                            <div><?php _e('Total Moderators', 'auto-order-assign-moderator'); ?></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #46b450;"><?php echo count($active_moderators); ?></div>
                            <div><?php _e('Active Moderators', 'auto-order-assign-moderator'); ?></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: bold; color: #ffb900;"><?php echo count($moderators_with_products); ?></div>
                            <div><?php _e('With Product Assignments', 'auto-order-assign-moderator'); ?></div>
                        </div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 40px 0;">
                    
                    <div class="card" style="text-align: center; padding: 30px;">
                        <h3>📋 <?php _e('Recent Assignments', 'auto-order-assign-moderator'); ?></h3>
                        <p><?php _e('View recent order assignments and history', 'auto-order-assign-moderator'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=moderator-recent-assignments'); ?>" class="button button-primary">
                            <?php _e('View Assignments', 'auto-order-assign-moderator'); ?>
                        </a>
                    </div>
                    <div class="card" style="text-align: center; padding: 30px;">
                        <h3>🔢 <?php _e('Sequence & Status', 'auto-order-assign-moderator'); ?></h3>
                        <p><?php _e('Set moderator sequence numbers and activation status', 'auto-order-assign-moderator'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=moderator-sequence-status'); ?>" class="button button-primary">
                            <?php _e('Manage Sequence & Status', 'auto-order-assign-moderator'); ?>
                        </a>
                    </div>
                    
                    <div class="card" style="text-align: center; padding: 30px;">
                        <h3>🛍️ <?php _e('Product Assignments', 'auto-order-assign-moderator'); ?></h3>
                        <p><?php _e('Assign specific products to moderators', 'auto-order-assign-moderator'); ?></p>
                        <a href="<?php echo admin_url('admin.php?page=moderator-product-assignments'); ?>" class="button button-primary">
                            <?php _e('Manage Product Assignments', 'auto-order-assign-moderator'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .card { 
            margin: 20px 0; 
            padding: 20px; 
            background: #fff; 
            border: 1px solid #ccd0d4; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 100%;
        }
        </style>
        <?php
    }
    
    /**
     * Recent assignments page
     */
    
        // Separate page for Recent Assignments
function moderator_recent_assignments_page() {
    // Get filter parameters
    $moderator_filter = isset($_GET['moderator_filter']) ? intval($_GET['moderator_filter']) : 0;
    $status_filter = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : 'all';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    
    $moderators = get_users(array(
        'role' => 'moderator',
        'orderby' => 'display_name'
    ));

    ?>
        <div class="wrap">
            <h1>📋 Recent Assignments</h1>
            
            <!-- Navigation Tabs -->
            <div class="nav-tab-wrapper">
                <a href="<?php echo admin_url('admin.php?page=moderator-settings'); ?>" class="nav-tab">Dashboard</a>
                <a href="<?php echo admin_url('admin.php?page=moderator-recent-assignments'); ?>" class="nav-tab nav-tab-active">Recent Assignments</a>
                <a href="<?php echo admin_url('admin.php?page=moderator-sequence-status'); ?>" class="nav-tab">Sequence & Status</a>
                <a href="<?php echo admin_url('admin.php?page=moderator-product-assignments'); ?>" class="nav-tab">Product Assignments</a>
            </div>

            

            <?php
            // Get orders based on filters
            global $wpdb;
            
            // Get all order IDs with assigned moderators
            if ($moderator_filter > 0) {
                // Specific moderator
                $order_ids = $wpdb->get_col($wpdb->prepare("
                    SELECT post_id 
                    FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_assigned_moderator_id' 
                    AND meta_value = %d
                    ORDER BY meta_id DESC
                ", $moderator_filter));
            } else {
                // All moderators - get orders assigned to any moderator
                $moderator_ids = array();
                foreach ($moderators as $mod) {
                    $moderator_ids[] = $mod->ID;
                }
                
                if (!empty($moderator_ids)) {
                    $placeholders = implode(',', array_fill(0, count($moderator_ids), '%d'));
                    $order_ids = $wpdb->get_col($wpdb->prepare("
                        SELECT post_id 
                        FROM {$wpdb->postmeta} 
                        WHERE meta_key = '_assigned_moderator_id' 
                        AND meta_value IN ($placeholders)
                        ORDER BY meta_id DESC
                    ", $moderator_ids));
                } else {
                    $order_ids = array();
                }
            }
            
            // Convert to WC_Order objects and apply status filter
            $all_orders = array();
            $filtered_orders = array();
            $status_counts = array(
                'all' => 0,
                'pending' => 0,
                'processing' => 0,
                'on-hold' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'refunded' => 0,
                'failed' => 0
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
                        $filtered_orders[] = $order;
                    }
                }
            }
            
            // Sort by date descending (most recent first)
            usort($filtered_orders, function($a, $b) {
                return $b->get_date_created()->getTimestamp() - $a->get_date_created()->getTimestamp();
            });
            
            // Apply pagination
            $total_orders = count($filtered_orders);
            $total_pages = ceil($total_orders / $per_page);
            $offset = ($paged - 1) * $per_page;
            $orders = array_slice($filtered_orders, $offset, $per_page);
            ?>
            
            <!-- Results Summary -->
            <div class="card">
                <h2>Assignment Results</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 15px 0;">
                    <div style="text-align: center; padding: 15px; background: #f0f6ff; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #0073aa;"><?php echo $total_orders; ?></div>
                        <div>Total Orders</div>
                    </div>
                    <div style="text-align: center; padding: 15px; background: #f0f6ff; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #46b450;"><?php echo count($orders); ?></div>
                        <div>Showing</div>
                    </div>
                    <div style="text-align: center; padding: 15px; background: #f0f6ff; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #ffb900;"><?php echo $total_pages; ?></div>
                        <div>Total Pages</div>
                    </div>
                    <div style="text-align: center; padding: 15px; background: #f0f6ff; border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: bold; color: #cc1818;"><?php echo count($moderators); ?></div>
                        <div>Total Moderators</div>
                    </div>
                </div>
                
                <!-- Status Breakdown -->
                <div style="margin-top: 20px;">
                    <h3>Order Status Breakdown</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        <?php foreach ($status_counts as $status => $count): 
                            if ($status === 'all') continue;
                            if ($count > 0):
                                $status_class = 'status-' . $status;
                                $status_label = wc_get_order_status_name($status);
                        ?>
                            <div class="status-count-badge <?php echo $status_class; ?>">
                                <span class="status-label"><?php echo $status_label; ?></span>
                                <span class="status-count"><?php echo $count; ?></span>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                
                <?php if ($moderator_filter > 0): ?>
                    <?php 
                    $moderator = get_userdata($moderator_filter);
                    $moderator_name = $moderator ? $moderator->display_name : 'Unknown';
                    $moderator_sequence = get_user_meta($moderator_filter, 'moderator_sequence', true);
                    ?>
                    <div style="margin-top: 15px; padding: 10px; background: #e7f3ff; border-radius: 4px;">
                        <strong>Currently viewing orders for:</strong> 
                        <?php echo esc_html($moderator_name); ?>
                        <?php if ($moderator_sequence): ?>
                            (Moderator <?php echo $moderator_sequence; ?>)
                        <?php endif; ?>
                        <?php if ($status_filter !== 'all'): ?>
                            | Status: <strong><?php echo ucfirst($status_filter); ?></strong>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
    <!-- Filters -->
            <div class="card">
                <h2>Assignment Filters</h2>
                <div class="assignment-filters" style="padding: 15px; background: #f9f9f9; border-radius: 4px;">
                    <form method="get" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <input type="hidden" name="page" value="moderator-recent-assignments">
                        
                        <div class="filter-group">
                            <label for="moderator_filter" style="font-weight: bold; margin-right: 5px;">Filter by Moderator:</label>
                            <select name="moderator_filter" id="moderator_filter" onchange="this.form.submit()">
                                <option value="0">All Moderators</option>
                                <?php foreach ($moderators as $mod): 
                                    $mod_sequence = get_user_meta($mod->ID, 'moderator_sequence', true);
                                    $display_name = $mod->display_name . ($mod_sequence ? ' (Moderator ' . $mod_sequence . ')' : '');
                                ?>
                                    <option value="<?php echo $mod->ID; ?>" <?php selected($moderator_filter, $mod->ID); ?>>
                                        <?php echo esc_html($display_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="status_filter" style="font-weight: bold; margin-right: 5px;">Order Status:</label>
                            <select name="status_filter" id="status_filter" onchange="this.form.submit()">
                                <option value="all" <?php selected($status_filter, 'all'); ?>>All Statuses</option>
                                <option value="pending" <?php selected($status_filter, 'pending'); ?>>Pending</option>
                                <option value="processing" <?php selected($status_filter, 'processing'); ?>>Processing</option>
                                <option value="on-hold" <?php selected($status_filter, 'on-hold'); ?>>On Hold</option>
                                <option value="completed" <?php selected($status_filter, 'completed'); ?>>Completed</option>
                                <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>>Cancelled</option>
                                <option value="refunded" <?php selected($status_filter, 'refunded'); ?>>Refunded</option>
                                <option value="failed" <?php selected($status_filter, 'failed'); ?>>Failed</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="per_page" style="font-weight: bold; margin-right: 5px;">Orders per page:</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()">
                                <option value="10" <?php selected($per_page, 10); ?>>10</option>
                                <option value="20" <?php selected($per_page, 20); ?>>20</option>
                                <option value="50" <?php selected($per_page, 50); ?>>50</option>
                                <option value="100" <?php selected($per_page, 100); ?>>100</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <button type="submit" class="button button-primary">Apply Filters</button>
                            <a href="?page=moderator-recent-assignments" class="button button-secondary">Reset Filters</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Orders Table -->
            <div class="card">
                <h2>Assigned Orders</h2>
                
                <?php if (!empty($orders)): ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Order #</th>
                                <th style="width: 120px;">Date</th>
                                <th style="width: 150px;">Customer</th>
                                <th style="width: 200px;">Products</th>
                                <th style="width: 100px;">Total</th>
                                <th style="width: 150px;">Assigned Moderator</th>
                                <th style="width: 80px;">Sequence</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $moderator_id = get_post_meta($order->get_id(), '_assigned_moderator_id', true);
                                $moderator_sequence = get_post_meta($order->get_id(), '_assigned_moderator_sequence', true);
                                if ($moderator_id):
                                    $moderator = get_userdata($moderator_id);
                                    $moderator_status = get_user_meta($moderator_id, 'moderator_status', true) ?: 'active';
                                    
                                    // Get order items
                                    $items = $order->get_items();
                                    $product_names = array();
                                    foreach ($items as $item) {
                                        $product_names[] = $item->get_name();
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo $order->get_id(); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo $order->get_date_created()->format('M j, Y'); ?><br>
                                        <small style="color: #666;"><?php echo $order->get_date_created()->format('g:i A'); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($order->get_formatted_billing_full_name()); ?></strong><br>
                                        <small><?php echo $order->get_billing_email(); ?></small><br>
                                        <small style="color: #666;"><?php echo $order->get_billing_phone(); ?></small>
                                    </td>
                                    <td>
                                        <div class="order-products">
                                            <?php 
                                            $display_count = 0;
                                            foreach ($product_names as $product_name) {
                                                if ($display_count >= 2) break;
                                                echo '<div class="product-name">• ' . esc_html($product_name) . '</div>';
                                                $display_count++;
                                            }
                                            if (count($product_names) > 2): ?>
                                                <div class="more-products">
                                                    + <?php echo count($product_names) - 2; ?> more products
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo $order->get_formatted_order_total(); ?></strong>
                                    </td>
                                    <td>
                                        <div class="moderator-info">
                                            <strong><?php echo esc_html($moderator->display_name); ?></strong>
                                            <?php if ($moderator_status == 'inactive'): ?>
                                                <span class="moderator-inactive-badge">(Inactive)</span>
                                            <?php endif; ?>
                                            <br>
                                            <small><?php echo $moderator->user_email; ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($moderator_sequence): ?>
                                            <span class="sequence-badge-small"><?php echo $moderator_sequence; ?></span>
                                        <?php else: ?>
                                            <span style="color:#ccc; font-size: 12px;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $order_status = $order->get_status();
                                        $status_class = 'status-' . $order_status;
                                        $status_label = wc_get_order_status_name($order_status);
                                        ?>
                                        <span class="order-status-badge <?php echo $status_class; ?>">
                                            <?php echo $status_label; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="order-actions">
                                            <a href="<?php echo get_edit_post_link($order->get_id()); ?>" class="button button-small" target="_blank" title="Edit Order">
                                                <span class="dashicons dashicons-edit"></span>
                                                Edit
                                            </a>
                                            <button type="button" class="button button-small view-order-details" data-order-id="<?php echo $order->get_id(); ?>" title="View Details">
                                                <span class="dashicons dashicons-visibility"></span>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="tablenav" style="margin-top: 20px;">
                            <div class="tablenav-pages">
                                <span class="displaying-num"><?php echo $total_orders; ?> items</span>
                                <span class="pagination-links">
                                    <?php
                                    $base_url = admin_url('admin.php?page=moderator-recent-assignments');
                                    $base_url .= '&moderator_filter=' . $moderator_filter . '&status_filter=' . $status_filter . '&per_page=' . $per_page;
                                    
                                    // Previous page
                                    if ($paged > 1) {
                                        echo '<a class="prev-page button" href="' . $base_url . '&paged=' . ($paged - 1) . '">‹ Previous</a>';
                                    }
                                    
                                    // Page numbers
                                    $start_page = max(1, $paged - 2);
                                    $end_page = min($total_pages, $paged + 2);
                                    
                                    for ($i = $start_page; $i <= $end_page; $i++) {
                                        if ($i == $paged) {
                                            echo '<span class="current-page button">' . $i . '</span>';
                                        } else {
                                            echo '<a class="page-number button" href="' . $base_url . '&paged=' . $i . '">' . $i . '</a>';
                                        }
                                    }
                                    
                                    // Next page
                                    if ($paged < $total_pages) {
                                        echo '<a class="next-page button" href="' . $base_url . '&paged=' . ($paged + 1) . '">Next ›</a>';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <div style="font-size: 48px; color: #ccc; margin-bottom: 20px;">📭</div>
                        <h3>No orders found</h3>
                        <p>No orders match the current filters.</p>
                        <?php if (count($all_orders) > 0 && $status_filter !== 'all'): ?>
                            <p style="color: #666;">
                                There are <?php echo count($all_orders); ?> orders in database, but none with status "<?php echo $status_filter; ?>".
                            </p>
                        <?php endif; ?>
                        <a href="?page=moderator-recent-assignments" class="button button-primary">Reset Filters</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Export Options -->
            <div class="card">
                <h2>Export Options</h2>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="button" onclick="exportAssignments('csv')">
                        <span class="dashicons dashicons-download"></span>
                        Export to CSV
                    </button>
                    <button type="button" class="button" onclick="exportAssignments('pdf')">
                        <span class="dashicons dashicons-pdf"></span>
                        Export to PDF
                    </button>
                    <button type="button" class="button" onclick="printAssignments()">
                        <span class="dashicons dashicons-printer"></span>
                        Print Report
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Order Details Modal -->
        <div id="order-details-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 2px solid #0073aa; z-index: 10000; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                <h3 style="margin: 0;">Order Details - #<span id="modal-order-id"></span></h3>
                <button type="button" class="button" onclick="closeOrderModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
            </div>
            <div id="order-details-content"></div>
        </div>
        
        <style>
        .card { 
            margin: 20px 0; 
            padding: 20px; 
            background: #fff; 
            border: 1px solid #ccd0d4; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 100%;
        }
        .filter-group {
            width: 23% !important;
        }
        .nav-tab-wrapper { margin: 20px 0; }
        .nav-tab { 
            text-decoration: none; 
            padding: 10px 15px; 
            margin: 0 5px 0 0; 
            border: 1px solid #ccd0d4;
            border-bottom: none;
            background: #f0f0f0;
        }
        .nav-tab-active { 
            background: #0073aa; 
            color: white; 
            border-bottom: 1px solid #0073aa;
        }
        .order-status-badge { 
            padding: 6px 12px; 
            border-radius: 4px; 
            font-size: 12px; 
            font-weight: bold;
            display: inline-block;
            text-align: center;
            min-width: 80px;
        }
        .status-pending { background: #ffb900; color: #000; }
        .status-processing { background: #00a0d2; color: #fff; }
        .status-on-hold { background: #cc1818; color: #fff; }
        .status-completed { background: #46b450; color: #fff; }
        .status-cancelled { background: #aaa; color: #fff; }
        .status-refunded { background: #aaa; color: #fff; }
        .status-failed { background: #cc1818; color: #fff; }
        .sequence-badge-small {
            display: inline-block;
            width: 25px;
            height: 25px;
            background: #0073aa;
            color: white;
            text-align: center;
            line-height: 25px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 12px;
        }
        .moderator-inactive-badge {
            font-size: 10px;
            color: #cc1818;
            font-weight: bold;
        }
        .order-products {
            max-height: 80px;
            overflow-y: auto;
        }
        .product-name {
            font-size: 12px;
            margin-bottom: 3px;
            line-height: 1.3;
        }
        .more-products {
            font-size: 11px;
            color: #666;
            font-style: italic;
        }
        .order-actions {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .button-small {
            padding: 4px 8px;
            font-size: 12px;
            line-height: 1.5;
            text-align: center;
        }
        .status-count-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-count-badge.status-pending { background: #fff8e5; color: #8a6d3b; }
        .status-count-badge.status-processing { background: #e7f3ff; color: #0073aa; }
        .status-count-badge.status-on-hold { background: #ffe5e5; color: #cc1818; }
        .status-count-badge.status-completed { background: #e5f7e5; color: #46b450; }
        .status-count-badge.status-cancelled { background: #f0f0f0; color: #666; }
        .status-count-badge.status-refunded { background: #f0f0f0; color: #666; }
        .status-count-badge.status-failed { background: #ffe5e5; color: #cc1818; }
        .status-count {
            background: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // View order details
            $('.view-order-details').click(function() {
                var orderId = $(this).data('order-id');
                viewOrderDetails(orderId);
            });
            
            // Close modal with ESC key
            $(document).keyup(function(e) {
                if (e.keyCode === 27) {
                    closeOrderModal();
                }
            });
        });
        
        function viewOrderDetails(orderId) {
            // Simple loading message
            document.getElementById("modal-order-id").textContent = orderId;
            document.getElementById("order-details-content").innerHTML = "<div style='text-align: center; padding: 40px;'><div class='spinner is-active' style='float: none;'></div><p>Loading order details...</p></div>";
            
            // Show modal and backdrop
            document.body.appendChild(document.createElement("div")).className = "modal-backdrop";
            document.getElementById("order-details-modal").style.display = "block";
            
            // Load details via AJAX
            jQuery.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: {
                    action: "get_moderator_order_details_simple",
                    order_id: orderId,
                    nonce: "<?php echo wp_create_nonce('moderator_order_details'); ?>"
                },
                success: function(response) {
                    if (response.success) {
                        jQuery("#order-details-content").html(response.data);
                    } else {
                        jQuery("#order-details-content").html("<div style='text-align: center; padding: 40px; color: #cc1818;'><p>Error loading order details.</p></div>");
                    }
                },
                error: function() {
                    jQuery("#order-details-content").html("<div style='text-align: center; padding: 40px; color: #cc1818;'><p>Error loading order details.</p></div>");
                }
            });
        }
        
        function closeOrderModal() {
            document.getElementById("order-details-modal").style.display = "none";
            var backdrops = document.getElementsByClassName("modal-backdrop");
            while (backdrops.length > 0) {
                backdrops[0].remove();
            }
        }
        
        // Close modal when clicking backdrop
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("modal-backdrop")) {
                closeOrderModal();
            }
        });
        
        // Export functions (placeholder)
        function exportAssignments(format) {
            alert('Export to ' + format.toUpperCase() + ' functionality would be implemented here.');
            // In a real implementation, this would make an AJAX call to generate and download the file
        }
        
        function printAssignments() {
            window.print();
        }
        </script>
        <?php
    }

}
    
    /**
     * Sequence & Status page
     */
public function moderator_sequence_status_page() {
    // Handle form submissions for this page only
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Handle status update
        if (isset($_POST['update_status']) && wp_verify_nonce($_POST['moderator_nonce'], 'moderator_settings')) {
            if (isset($_POST['moderator_status'])) {
                foreach ($_POST['moderator_status'] as $moderator_id => $status) {
                    $moderator_id = intval($moderator_id);
                    $status = sanitize_text_field($status);
                    update_user_meta($moderator_id, 'moderator_status', $status);
                    
                    if ($status === 'active') {
                        update_user_meta($moderator_id, 'moderator_last_active', current_time('mysql'));
                    }
                }
                echo '<div class="notice notice-success"><p>Moderator status updated successfully!</p></div>';
            }
        }
        
        // Handle sequence update
        if (isset($_POST['update_sequence']) && wp_verify_nonce($_POST['moderator_nonce'], 'moderator_settings')) {
            if (isset($_POST['moderator_sequence'])) {
                foreach ($_POST['moderator_sequence'] as $moderator_id => $sequence) {
                    $moderator_id = intval($moderator_id);
                    $sequence = intval($sequence);
                    update_user_meta($moderator_id, 'moderator_sequence', $sequence);
                    update_user_meta($moderator_id, 'moderator_last_active', current_time('mysql'));
                }
                echo '<div class="notice notice-success"><p>Moderator sequence updated successfully!</p></div>';
            }
        }
        
        // Handle individual moderator update
        if (isset($_POST['update_single_moderator']) && wp_verify_nonce($_POST['moderator_nonce'], 'moderator_settings')) {
            $moderator_id = intval($_POST['moderator_id']);
            $status = sanitize_text_field($_POST['single_moderator_status']);
            $sequence = intval($_POST['single_moderator_sequence']);
            
            update_user_meta($moderator_id, 'moderator_status', $status);
            update_user_meta($moderator_id, 'moderator_sequence', $sequence);
            
            if ($status === 'active') {
                update_user_meta($moderator_id, 'moderator_last_active', current_time('mysql'));
            }
            
            echo '<div class="notice notice-success"><p>Moderator updated successfully!</p></div>';
        }
        
        // Handle bulk actions
        if (isset($_POST['bulk_update']) && wp_verify_nonce($_POST['moderator_nonce'], 'moderator_settings')) {
            $bulk_action = $_POST['bulk_action'] ?? '';
            $moderators = get_users(array('role' => 'moderator'));
            
            if ($bulk_action === 'activate_all') {
                foreach ($moderators as $moderator) {
                    update_user_meta($moderator->ID, 'moderator_status', 'active');
                    update_user_meta($moderator->ID, 'moderator_last_active', current_time('mysql'));
                }
                echo '<div class="notice notice-success"><p>All moderators activated!</p></div>';
            } elseif ($bulk_action === 'deactivate_all') {
                foreach ($moderators as $moderator) {
                    update_user_meta($moderator->ID, 'moderator_status', 'inactive');
                }
                echo '<div class="notice notice-success"><p>All moderators deactivated!</p></div>';
            } elseif ($bulk_action === 'reset_all_sequences') {
                global $wpdb;
                $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'last_assigned_moderator_sequence_products_%'");
                update_option('last_assigned_moderator_sequence', 0);
                echo '<div class="notice notice-success"><p>All assignment sequences reset!</p></div>';
            }
        }
        
        // Handle cycle reset
        if (isset($_POST['reset_cycle']) && wp_verify_nonce($_POST['moderator_nonce'], 'moderator_settings')) {
            update_option('last_assigned_moderator_sequence', 0);
            global $wpdb;
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'last_assigned_moderator_sequence_products_%'");
            echo '<div class="notice notice-success"><p>All assignment cycles reset to Moderator 1!</p></div>';
        }
    }
    
    // Get filter parameters
    $search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $status_filter = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : 'all';
    $sequence_filter = isset($_GET['sequence_filter']) ? sanitize_text_field($_GET['sequence_filter']) : 'all';
    
    // Base query for moderators
    $moderator_args = array(
        'role' => 'moderator',
        'orderby' => 'meta_value_num',
        'meta_key' => 'moderator_sequence',
        'order' => 'ASC',
        'number' => -1
    );
    
    // Apply search filter
    if (!empty($search_term)) {
        $moderator_args['search'] = '*' . $search_term . '*';
        $moderator_args['search_columns'] = array('user_login', 'user_nicename', 'user_email', 'display_name');
    }
    
    $all_moderators = get_users($moderator_args);
    
    // Apply status filter
    $moderators = array();
    foreach ($all_moderators as $moderator) {
        $current_status = get_user_meta($moderator->ID, 'moderator_status', true) ?: 'active';
        
        if ($status_filter === 'all' || $current_status === $status_filter) {
            // Apply sequence filter if set
            if ($sequence_filter === 'all') {
                $moderators[] = $moderator;
            } else {
                $current_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true);
                if ($sequence_filter === 'with_sequence' && $current_sequence) {
                    $moderators[] = $moderator;
                } elseif ($sequence_filter === 'without_sequence' && !$current_sequence) {
                    $moderators[] = $moderator;
                }
            }
        }
    }
    
    $active_moderators = array_filter($moderators, function($moderator) {
        $status = get_user_meta($moderator->ID, 'moderator_status', true);
        return $status !== 'inactive';
    });
    ?>

    <div class="wrap">
        <h1>🔢 Moderator Sequence & Status</h1>
        
        <!-- Navigation Tabs -->
        <div class="nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=moderator-settings'); ?>" class="nav-tab">Dashboard</a>
            <a href="<?php echo admin_url('admin.php?page=moderator-recent-assignments'); ?>" class="nav-tab">Recent Assignments</a>
            <a href="<?php echo admin_url('admin.php?page=moderator-sequence-status'); ?>" class="nav-tab nav-tab-active">Sequence & Status</a>
            <a href="<?php echo admin_url('admin.php?page=moderator-product-assignments'); ?>" class="nav-tab">Product Assignments</a>
        </div>

        <div class="card">
            <h2>Current Assignment Status</h2>
            <?php
            global $wpdb;
            $product_sequences = $wpdb->get_results(
                "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'last_assigned_moderator_sequence_products_%'"
            );
            
            $last_sequence = get_option('last_assigned_moderator_sequence', 0);
            ?>
            
            <p><strong>Active Moderators:</strong> <?php echo count($active_moderators); ?> / <?php echo count($moderators); ?> (Showing)</p>
            <p><strong>Global Last Assigned:</strong> Moderator <?php echo $last_sequence; ?></p>
            
            <?php if (!empty($product_sequences)): ?>
                <h3>Product-Specific Sequences:</h3>
                <?php foreach ($product_sequences as $seq): 
                    $product_ids = str_replace('last_assigned_moderator_sequence_products_', '', $seq->option_name);
                    $product_ids = explode('_', $product_ids);
                    $product_names = array();
                    foreach ($product_ids as $product_id) {
                        $product = wc_get_product($product_id);
                        if ($product) {
                            $product_names[] = $product->get_name();
                        }
                    }
                ?>
                    <p><strong><?php echo implode(', ', $product_names); ?>:</strong> Last assigned to Moderator <?php echo $seq->option_value; ?></p>
                <?php endforeach; ?>
            <?php else: ?>
                <p><strong>Product-Specific Sequences:</strong> None yet</p>
            <?php endif; ?>
            
            <div style="background: #e7f3ff; padding: 10px; border-radius: 4px; margin: 10px 0;">
                <strong>🔄 Product-Specific :</strong> Each product combination maintains its own sequence for fair distribution.
            </div>
            
            <form method="post" style="margin-top: 15px;">
                <?php wp_nonce_field('moderator_settings', 'moderator_nonce'); ?>
                <input type="hidden" name="reset_cycle" value="1">
                <input type="submit" class="button button-secondary" value="Reset All Assignment Cycles" 
                    onclick="return confirm('Are you sure? This will reset ALL product-specific assignment sequences.')">
            </form>
        </div>

        <!-- Search and Filters Card -->
        <div class="card">
            <h2>Set Moderator Sequence & Status</h2>
            <p style="color: #cc1818; font-weight: bold;">⚠️ Only moderators with product assignments will receive orders.</p>
            <h2>🔍 Search & Filter Moderators</h2>
            
            <form method="get" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; align-items: end;">
                <input type="hidden" name="page" value="moderator-sequence-status">
                
                <!-- Search Field -->
                <div class="filter-group" style="width:100%">
                    <label for="search" style="display: block; margin-bottom: 5px; font-weight: bold;">Search Moderators:</label>
                    <input type="text" name="search" id="search" value="<?php echo esc_attr($search_term); ?>" 
                           placeholder="Search by name or email..." style="width: 100%; padding: 8px;">
                    <small style="color: #666;color: #666;position: absolute;bottom: -4px;">Search by name, username, or email</small>
                </div>
                
                <!-- Status Filter -->
                <div class="filter-group" style="width:100%">
                    <label for="status_filter" style="display: block; margin-bottom: 5px; font-weight: bold;">Status Filter:</label>
                    <select name="status_filter" id="status_filter" style="width: 100%; padding: 8px;">
                        <option value="all" <?php selected($status_filter, 'all'); ?>>All Statuses</option>
                        <option value="active" <?php selected($status_filter, 'active'); ?>>Active Only</option>
                        <option value="inactive" <?php selected($status_filter, 'inactive'); ?>>Inactive Only</option>
                    </select>
                </div>
                
                <!-- Sequence Filter -->
                <div class="filter-group" style="width:100%">
                    <label for="sequence_filter" style="display: block; margin-bottom: 5px; font-weight: bold;">Sequence Filter:</label>
                    <select name="sequence_filter" id="sequence_filter" style="width: 100%; padding: 8px;">
                        <option value="all" <?php selected($sequence_filter, 'all'); ?>>All Sequences</option>
                        <option value="with_sequence" <?php selected($sequence_filter, 'with_sequence'); ?>>With Sequence</option>
                        <option value="without_sequence" <?php selected($sequence_filter, 'without_sequence'); ?>>Without Sequence</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="filter-group" style="display: flex; gap: 10px; align-items: center; width:100%">
                    <button type="submit" class="button button-primary" style="padding: 8px 20px;">
                        <span class="dashicons dashicons-search"></span> Apply Filters
                    </button>
                    <a href="?page=moderator-sequence-status" class="button button-secondary" style="padding: 8px 20px;">
                        <span class="dashicons dashicons-update"></span> Reset
                    </a>
                </div>
            </form>
            
            <!-- Results Summary -->
            <div style="margin-top: 20px; padding: 15px; background: #f0f6ff; border-radius: 6px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; text-align: center;">
                    <div>
                        <div style="font-size: 24px; font-weight: bold; color: #0073aa;"><?php echo count($moderators); ?></div>
                        <div>Showing Moderators</div>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: bold; color: #46b450;"><?php echo count($active_moderators); ?></div>
                        <div>Active</div>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: bold; color: #cc1818;"><?php echo count($moderators) - count($active_moderators); ?></div>
                        <div>Inactive</div>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: bold; color: #ffb900;"><?php echo count($all_moderators); ?></div>
                        <div>Total Moderators</div>
                    </div>
                </div>
                
                <?php if (!empty($search_term)): ?>
                    <div style="margin-top: 10px; text-align: center;">
                        <strong>Search results for:</strong> "<?php echo esc_html($search_term); ?>"
                    </div>
                <?php endif; ?>
            </div>
                       
            
            <?php if (empty($moderators)): ?>
                <div style="text-align: center; padding: 40px;">
                    <div style="font-size: 48px; color: #ccc; margin-bottom: 20px;">🔍</div>
                    <h3>No moderators found</h3>
                    <p>No moderators match your current search and filter criteria.</p>
                    <a href="?page=moderator-sequence-status" class="button button-primary">Show All Moderators</a>
                </div>
            <?php else: ?>
            
            <form method="post" id="moderator-sequence-form">
                <?php wp_nonce_field('moderator_settings', 'moderator_nonce'); ?>
                
                <table class="wp-list-table widefat fixed striped" id="moderator-sequence-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Sequence</th>
                            <th style="width: 150px;">Moderator Name</th>
                            <th style="width: 150px;">Email</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 250px;">Assigned Products</th>
                            <th style="width: 100px;">Assigned Orders</th>
                            <th style="width: 120px;">Last Active</th>
                            <th style="width: 100px;">Quick Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sequence = 1;
                        foreach ($moderators as $moderator) {
                            $current_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true) ?: $sequence;
                            $current_status = get_user_meta($moderator->ID, 'moderator_status', true);
                            if (empty($current_status)) {
                                $current_status = 'active';
                                update_user_meta($moderator->ID, 'moderator_status', 'active');
                            }
                            $last_active = get_user_meta($moderator->ID, 'moderator_last_active', true);
                            $assigned_products = get_user_meta($moderator->ID, 'moderator_assigned_products', true) ?: array();

                            global $wpdb;
                            $order_ids = $wpdb->get_col($wpdb->prepare("
                                SELECT post_id 
                                FROM {$wpdb->postmeta} 
                                WHERE meta_key = '_assigned_moderator_id' 
                                AND meta_value = %d
                            ", $moderator->ID));
                            
                            $orders = array();
                            foreach ($order_ids as $order_id) {
                                $order = wc_get_order($order_id);
                                if ($order) {
                                    $orders[] = $order;
                                }
                            }
                             
                            ?>
                            <tr class="moderator-<?php echo $current_status; ?>" data-moderator-id="<?php echo $moderator->ID; ?>">
                                <td>
                                    <input style="width:60px;" type="text" name="moderator_sequence[<?php echo $moderator->ID; ?>]" 
                                           value="<?php echo $current_sequence; ?>" class="sequence-input">
                                </td>
                                <td>
                                    <strong><?php echo esc_html($moderator->display_name); ?></strong>
                                    <div class="sequence-number">#<?php echo $current_sequence; ?></div>
                                    <?php if ($current_status == 'inactive'): ?>
                                        <span class="dashicons dashicons-hidden" style="color:#ff0000; margin-left:5px;" title="Inactive"> </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo esc_html($moderator->user_email); ?></small>
                                </td>
                                <td>
                                    <select name="moderator_status[<?php echo $moderator->ID; ?>]" class="moderator-status">
                                        <option value="active" <?php selected($current_status, 'active'); ?>>Active</option>
                                        <option value="inactive" <?php selected($current_status, 'inactive'); ?>>Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <?php 
                                    if (empty($assigned_products)) {
                                        echo '<div class="no-products-assigned">';
                                        echo '<span class="dashicons dashicons-warning" style="color:#cc1818;"></span>';
                                        echo '<span style="color:#cc1818; font-weight:bold;">No Products</span>';
                                        echo '<br><small style="color:#666;">Will NOT receive orders</small>';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="products-assigned">';
                                        echo '<span class="dashicons dashicons-yes-alt" style="color:#46b450;"></span>';
                                        echo '<span style="color:#46b450; font-weight:bold;">' . count($assigned_products) . ' Products</span>';
                                        
                                        echo '<div class="product-list">';
                                        $display_count = 0;
                                        foreach ($assigned_products as $product_id) {
                                            if ($display_count >= 3) break;
                                            $product = wc_get_product($product_id);
                                            if ($product) {
                                                echo '<div class="product-item">';
                                                echo '<span class="product-name">' . esc_html($product->get_name()) . '</span>';
                                                echo '<span class="product-id">(ID: ' . $product_id . ')</span>';
                                                echo '</div>';
                                                $display_count++;
                                            }
                                        }
                                        
                                        if (count($assigned_products) > 3) {
                                            echo '<div class="more-products">';
                                            echo '+ ' . (count($assigned_products) - 3) . ' more products';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="order-count">
                                        <strong><?php echo count($orders); ?></strong>
                                        <br>
                                        <small>orders</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="last-active">
                                        <?php 
                                        if ($last_active) {
                                            echo '<span class="date">' . date('M j, Y', strtotime($last_active)) . '</span>';
                                            echo '<br><small class="time">' . date('g:i A', strtotime($last_active)) . '</small>';
                                        } else {
                                            echo '<span style="color:#666;">Never</span>';
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="button button-small update-single-moderator" 
                                            data-moderator-id="<?php echo $moderator->ID; ?>"
                                            data-moderator-name="<?php echo esc_attr($moderator->display_name); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            <?php
                            $sequence++;
                        }
                        ?>
                    </tbody>
                </table>
                
                <p style="margin-top: 15px;">
                    <input type="submit" name="update_sequence" class="button button-primary" value="Update Sequence">
                    <input type="submit" name="update_status" class="button button-secondary" value="Update Status Only">
                </p>
            </form>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Quick Status Update</h2>
            <form method="post" style="display: flex; gap: 10px; align-items: center;">
                <?php wp_nonce_field('moderator_settings', 'moderator_nonce'); ?>
                <select name="bulk_action" id="bulk_action">
                    <option value="">Bulk Actions</option>
                    <option value="activate_all">Activate All Moderators</option>
                    <option value="deactivate_all">Deactivate All Moderators</option>
                    <option value="reset_all_sequences">Reset All Assignment Sequences</option>
                </select>
                <input type="submit" name="bulk_update" class="button" value="Apply" onclick="return confirm('Are you sure?')">
            </form>
        </div>
    </div>
    
    <style>
    .card { margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ccd0d4;max-width:100%; }
    .sequence-number { 
        display: inline-block; 
        width: 30px; 
        height: 30px; 
        background: #0073aa; 
        color: white; 
        text-align: center; 
        line-height: 30px; 
        border-radius: 50%; 
        font-weight: bold;
        margin-top: 5px;
    }
    .moderator-inactive { 
        background-color: #fff8f8; 
        opacity: 0.7;
    }
    .no-products-assigned {
        padding: 8px;
        background: #fff5f5;
        border: 1px solid #ffd6d6;
        border-radius: 4px;
        text-align: center;
    }
    .products-assigned {
        padding: 8px;
        background: #f8fff8;
        border: 1px solid #d6ffd6;
        border-radius: 4px;
    }
    .product-list {
        margin-top: 8px;
        max-height: 120px;
        overflow-y: auto;
        padding: 5px;
        background: white;
        border-radius: 3px;
        border: 1px solid #f0f0f0;
    }
    .product-item {
        padding: 3px 5px;
        margin: 2px 0;
        background: #f9f9f9;
        border-radius: 3px;
        border-left: 3px solid #0073aa;
        font-size: 11px;
        line-height: 1.3;
    }
    .more-products {
        padding: 5px;
        text-align: center;
        background: #f0f6fc;
        color: #0073aa;
        font-size: 10px;
        font-weight: bold;
        border-radius: 3px;
        margin-top: 5px;
    }
    .order-count {
        text-align: center;
        padding: 5px;
    }
    .order-count strong {
        font-size: 18px;
        color: #0073aa;
        display: block;
    }
    .last-active {
        text-align: center;
        padding: 5px;
    }
    .nav-tab-wrapper { margin: 20px 0; }
    .nav-tab { 
        text-decoration: none; 
        padding: 10px 15px; 
        margin: 0 5px 0 0; 
        border: 1px solid #ccd0d4;
        border-bottom: none;
        background: #f0f0f0;
    }
    .nav-tab-active { 
        background: #0073aa; 
        color: white; 
        border-bottom: 1px solid #0073aa;
    }
    .filter-group {
        margin-bottom: 0;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Make table sortable
        $('#moderator-sequence-table tbody').sortable({
            update: function(event, ui) {
                $('#moderator-sequence-table tbody tr').each(function(index) {
                    var sequence = index + 1;
                    $(this).find('.sequence-number').text('#' + sequence);
                    $(this).find('.sequence-input').val(sequence);
                });
            }
        });
        
        // Update row appearance when status changes
        $('.moderator-status').change(function() {
            var row = $(this).closest('tr');
            if ($(this).val() === 'inactive') {
                row.addClass('moderator-inactive');
            } else {
                row.removeClass('moderator-inactive');
            }
        });
        
        // Initialize row colors based on current status
        $('.moderator-status').each(function() {
            var row = $(this).closest('tr');
            if ($(this).val() === 'inactive') {
                row.addClass('moderator-inactive');
            }
        });

        // Focus on search field when page loads if there's a search term
        <?php if (!empty($search_term)): ?>
            $('#search').focus();
        <?php endif; ?>
    });
    </script>
    <?php
}

    
    /**
     * Product assignments page
     */
    function moderator_product_assignments_page() {
    // Handle form submissions for product assignments
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Handle product assignment update
        if (isset($_POST['update_products']) && wp_verify_nonce($_POST['moderator_nonce'], 'moderator_settings')) {
            if (isset($_POST['moderator_products'])) {
                foreach ($_POST['moderator_products'] as $moderator_id => $products) {
                    $moderator_id = intval($moderator_id);
                    $product_ids = array_map('intval', $products);
                    update_user_meta($moderator_id, 'moderator_assigned_products', $product_ids);
                }
                echo '<div class="notice notice-success"><p>Moderator product assignments updated successfully!</p></div>';
            }
        }
    }
    
    // Get filter parameters
    $search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $status_filter = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : 'all';
    $assignment_filter = isset($_GET['assignment_filter']) ? sanitize_text_field($_GET['assignment_filter']) : 'all';
    
    // Base query for moderators
    $moderator_args = array(
        'role' => 'moderator',
        'orderby' => 'meta_value_num',
        'meta_key' => 'moderator_sequence',
        'order' => 'ASC',
        'number' => -1
    );
    
    // Apply search filter
    if (!empty($search_term)) {
        $moderator_args['search'] = '*' . $search_term . '*';
        $moderator_args['search_columns'] = array('user_login', 'user_nicename', 'user_email', 'display_name');
    }
    
    $all_moderators = get_users($moderator_args);
    
    // Apply filters
    $moderators = array();
    $stats = array(
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'with_products' => 0,
        'without_products' => 0
    );
    
    foreach ($all_moderators as $moderator) {
        $current_status = get_user_meta($moderator->ID, 'moderator_status', true) ?: 'active';
        $assigned_products = get_user_meta($moderator->ID, 'moderator_assigned_products', true) ?: array();
        $has_products = !empty($assigned_products);
        
        // Update stats
        $stats['total']++;
        if ($current_status === 'active') $stats['active']++;
        if ($current_status === 'inactive') $stats['inactive']++;
        if ($has_products) $stats['with_products']++;
        if (!$has_products) $stats['without_products']++;
        
        // Apply status filter
        $status_match = ($status_filter === 'all' || $current_status === $status_filter);
        
        // Apply assignment filter
        $assignment_match = false;
        if ($assignment_filter === 'all') {
            $assignment_match = true;
        } elseif ($assignment_filter === 'with_products' && $has_products) {
            $assignment_match = true;
        } elseif ($assignment_filter === 'without_products' && !$has_products) {
            $assignment_match = true;
        }
        
        if ($status_match && $assignment_match) {
            $moderators[] = $moderator;
        }
    }
    
    // Get all products for product assignment
    $products = wc_get_products(array(
        'status' => 'publish',
        'limit' => -1,
    ));
    ?>

    <div class="wrap">
        <h1>🛍️ Assign Products to Moderators</h1>
        
        <!-- Navigation Tabs -->
        <div class="nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=moderator-settings'); ?>" class="nav-tab">Dashboard</a>
            <a href="<?php echo admin_url('admin.php?page=moderator-recent-assignments'); ?>" class="nav-tab">Recent Assignments</a>
            <a href="<?php echo admin_url('admin.php?page=moderator-sequence-status'); ?>" class="nav-tab">Sequence & Status</a>
            <a href="<?php echo admin_url('admin.php?page=moderator-product-assignments'); ?>" class="nav-tab nav-tab-active">Product Assignments</a>
        </div>

        <!-- Search and Filters Card -->

        <div class="card">
            <h2>Product Assignment Management</h2>
            <p style="color: #cc1818; font-weight: bold;">⚠️ Moderators without product assignments will NOT receive any orders.</p>
            <p>Select specific products for each moderator. Only moderators with product assignments will receive orders.</p>
            <h2>🔍 Search & Filter Moderators</h2>
            
            <form method="get" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; align-items: end;">
                <input type="hidden" name="page" value="moderator-product-assignments">
                
                <!-- Search Field -->
                <div class="filter-group"  style="width:100%;">
                    <label for="search" style="display: block; margin-bottom: 5px; font-weight: bold;">Search Moderators:</label>
                    <input type="text" name="search" id="search" value="<?php echo esc_attr($search_term); ?>" 
                           placeholder="Search by name or email..." style="width: 100%; padding: 8px;">
                    <small style="color: #666;position: absolute;bottom: -4px;">Search by name, username, or email</small>
                </div>
                
                <!-- Status Filter -->
                <div class="filter-group"  style="width:100%;">
                    <label for="status_filter" style="display: block; margin-bottom: 5px; font-weight: bold;">Status Filter:</label>
                    <select name="status_filter" id="status_filter" style="width: 100%; padding: 8px;">
                        <option value="all" <?php selected($status_filter, 'all'); ?>>All Statuses</option>
                        <option value="active" <?php selected($status_filter, 'active'); ?>>Active Only</option>
                        <option value="inactive" <?php selected($status_filter, 'inactive'); ?>>Inactive Only</option>
                    </select>
                </div>
                
                <!-- Product Assignment Filter -->
                <div class="filter-group"  style="width:100%;">
                    <label for="assignment_filter" style="display: block; margin-bottom: 5px; font-weight: bold;">Product Assignment:</label>
                    <select name="assignment_filter" id="assignment_filter" style="width: 100%; padding: 8px;">
                        <option value="all" <?php selected($assignment_filter, 'all'); ?>>All Assignments</option>
                        <option value="with_products" <?php selected($assignment_filter, 'with_products'); ?>>With Products</option>
                        <option value="without_products" <?php selected($assignment_filter, 'without_products'); ?>>Without Products</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="filter-group" style="width:100%;display: flex; gap: 10px; align-items: center;">
                    <button type="submit" class="button button-primary" style="padding: 8px 20px;">
                        <span class="dashicons dashicons-search"></span> Apply Filters
                    </button>
                    <a href="?page=moderator-product-assignments" class="button button-secondary" style="padding: 8px 20px;">
                        <span class="dashicons dashicons-update"></span> Reset
                    </a>
                </div>
            </form>
            
            <!-- Results Summary -->
            <div style="margin-top: 20px; padding: 15px; background: #f0f6ff; border-radius: 6px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; text-align: center;">
                    <div>
                        <div style="font-size: 20px; font-weight: bold; color: #0073aa;"><?php echo count($moderators); ?></div>
                        <div style="font-size: 12px;">Showing</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: bold; color: #46b450;"><?php echo $stats['active']; ?></div>
                        <div style="font-size: 12px;">Active</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: bold; color: #cc1818;"><?php echo $stats['inactive']; ?></div>
                        <div style="font-size: 12px;">Inactive</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: bold; color: #ffb900;"><?php echo $stats['with_products']; ?></div>
                        <div style="font-size: 12px;">With Products</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: bold; color: #dc3232;"><?php echo $stats['without_products']; ?></div>
                        <div style="font-size: 12px;">Without Products</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: bold; color: #666;"><?php echo $stats['total']; ?></div>
                        <div style="font-size: 12px;">Total</div>
                    </div>
                </div>
                
                <?php if (!empty($search_term)): ?>
                    <div style="margin-top: 10px; text-align: center;">
                        <strong>Search results for:</strong> "<?php echo esc_html($search_term); ?>"
                    </div>
                <?php endif; ?>
                
                <?php if ($status_filter !== 'all' || $assignment_filter !== 'all'): ?>
                    <div style="margin-top: 5px; text-align: center; font-size: 12px; color: #666;">
                        <strong>Filters:</strong> 
                        <?php 
                        $active_filters = array();
                        if ($status_filter !== 'all') $active_filters[] = 'Status: ' . ucfirst($status_filter);
                        if ($assignment_filter !== 'all') $active_filters[] = 'Assignment: ' . str_replace('_', ' ', ucfirst($assignment_filter));
                        echo implode(' • ', $active_filters);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (empty($moderators)): ?>
                <div style="text-align: center; padding: 40px;">
                    <div style="font-size: 48px; color: #ccc; margin-bottom: 20px;">🔍</div>
                    <h3>No moderators found</h3>
                    <p>No moderators match your current search and filter criteria.</p>
                    <a href="?page=moderator-product-assignments" class="button button-primary">Show All Moderators</a>
                </div>
            <?php else: ?>
            
            <form method="post" id="moderator-products-form">
                <?php wp_nonce_field('moderator_settings', 'moderator_nonce'); ?>
                
                <table class="wp-list-table widefat fixed striped" id="moderator-products-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Sequence</th>
                            <th style="width: 150px;">Moderator Name</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 400px;">Assigned Products</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($moderators as $moderator): 
                            $current_sequence = get_user_meta($moderator->ID, 'moderator_sequence', true) ?: 0;
                            $assigned_products = get_user_meta($moderator->ID, 'moderator_assigned_products', true) ?: array();
                            $current_status = get_user_meta($moderator->ID, 'moderator_status', true) ?: 'active';
                            
                            // Get product details for display
                            $assigned_product_details = array();
                            foreach ($assigned_products as $product_id) {
                                $product = wc_get_product($product_id);
                                if ($product) {
                                    $assigned_product_details[] = array(
                                        'id' => $product_id,
                                        'name' => $product->get_name(),
                                        'price' => $product->get_price(),
                                        'type' => $product->get_type()
                                    );
                                }
                            }
                        ?>
                            <tr class="moderator-<?php echo $current_status; ?>">
                                <td>
                                    <div class="sequence-badge"><?php echo $current_sequence; ?></div>
                                </td>
                                <td>
                                    <div class="moderator-info">
                                        <strong><?php echo esc_html($moderator->display_name); ?></strong>
                                        <div class="moderator-email"><?php echo esc_html($moderator->user_email); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $current_status; ?>">
                                        <span class="dashicons dashicons-<?php echo $current_status === 'active' ? 'yes' : 'no'; ?>"></span>
                                        <?php echo ucfirst($current_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="assigned-products-container">
                                        <!-- Product Selection with Select2 -->
                                        <select name="moderator_products[<?php echo $moderator->ID; ?>][]" multiple="multiple" class="moderator-products-select" style="width: 100%;">
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?php echo $product->get_id(); ?>" 
                                                    <?php selected(in_array($product->get_id(), $assigned_products)); ?>>
                                                    <?php echo esc_html($product->get_name()); ?> (ID: <?php echo $product->get_id(); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <!-- Current Assignment Summary -->
                                        <div class="assignment-summary">
                                            <?php if (empty($assigned_products)): ?>
                                                <div class="no-assignment">
                                                    <span class="dashicons dashicons-warning"></span>
                                                    <div class="warning-text">
                                                        <strong>No Products Assigned</strong>
                                                        <span>This moderator will NOT receive any orders</span>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="has-assignment">
                                                    <div class="assignment-header">
                                                        <span class="dashicons dashicons-yes-alt"></span>
                                                        <div class="assignment-stats">
                                                            <strong><?php echo count($assigned_products); ?> Product<?php echo count($assigned_products) > 1 ? 's' : ''; ?> Assigned</strong>
                                                            <span>Will receive orders for these products</span>
                                                        </div>
                                                    </div>
                                                    <div class="products-quick-view">
                                                        <?php 
                                                        $display_count = 0;
                                                        foreach ($assigned_product_details as $product_detail) {
                                                            if ($display_count >= 4) break;
                                                            ?>
                                                            <div class="product-chip">
                                                                <span class="product-name"><?php echo esc_html($product_detail['name']); ?></span>
                                                                <span class="product-id">#<?php echo $product_detail['id']; ?></span>
                                                            </div>
                                                            <?php
                                                            $display_count++;
                                                        }
                                                        
                                                        if (count($assigned_products) > 4): ?>
                                                            <div class="more-products-chip">
                                                                + <?php echo count($assigned_products) - 4; ?> more product<?php echo (count($assigned_products) - 4) > 1 ? 's' : ''; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="button button-small clear-products" data-moderator-id="<?php echo $moderator->ID; ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                            Clear All
                                        </button>
                                        <button type="button" class="button button-small view-products" 
                                                data-moderator-id="<?php echo $moderator->ID; ?>" 
                                                data-moderator-name="<?php echo esc_attr($moderator->display_name); ?>"
                                                data-products='<?php echo json_encode($assigned_product_details); ?>'>
                                            <span class="dashicons dashicons-visibility"></span>
                                            View All
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <p style="margin-top: 15px;">
                    <input type="submit" name="update_products" class="button button-primary" value="💾 Update All Product Assignments">
                </p>
            </form>
            <?php endif; ?>
        </div>

        <!-- Quick Bulk Actions -->
        <div class="card">
            <h2>Quick Product Management</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <h4 style="margin-top: 0;">Bulk Actions</h4>
                    <form method="post" style="display: flex; gap: 10px; align-items: center;">
                        <?php wp_nonce_field('moderator_settings', 'moderator_nonce'); ?>
                        <select name="bulk_action" id="bulk_action" style="flex: 1;">
                            <option value="">Select Action</option>
                            <option value="clear_all_products">Clear All Product Assignments</option>
                            <option value="clear_filtered_products">Clear Filtered Assignments</option>
                        </select>
                        <input type="submit" name="bulk_update" class="button" value="Apply" 
                               onclick="return confirm('Are you sure? This will remove product assignments.')">
                    </form>
                </div>
                
                <div>
                    <h4 style="margin-top: 0;">Quick Filters</h4>
                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                        <a href="?page=moderator-product-assignments&assignment_filter=without_products" 
                           class="button button-small <?php echo $assignment_filter === 'without_products' ? 'button-primary' : 'button-secondary'; ?>">
                            🚫 Without Products
                        </a>
                        <a href="?page=moderator-product-assignments&status_filter=inactive" 
                           class="button button-small <?php echo $status_filter === 'inactive' ? 'button-primary' : 'button-secondary'; ?>">
                            ⚠️ Inactive
                        </a>
                        <a href="?page=moderator-product-assignments" 
                           class="button button-small button-secondary">
                            📋 Show All
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Modal HTML -->
    <div id="products-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; width: 90%; max-width: 800px; max-height: 80vh; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #ddd; background: #f8f9fa;">
                <h3 style="margin: 0; color: #0073aa;" id="modal-title">Assigned Products</h3>
                <button type="button" id="close-modal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
            </div>
            <div style="padding: 20px; max-height: calc(80vh - 80px); overflow-y: auto;">
                <div id="modal-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div style="padding: 15px 20px; border-top: 1px solid #ddd; text-align: right; background: #f8f9fa;">
                <button type="button" id="modal-close-btn" class="button button-primary">Close</button>
            </div>
        </div>
    </div>

    <!-- Select2 Implementation Script -->
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Initialize Select2 on all product selects
        $('.moderator-products-select').select2({
            placeholder: "Select products - Required for order assignment",
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });

        // Clear products for specific moderator
        $('.clear-products').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var moderatorName = $button.closest('tr').find('.moderator-info strong').text();
            var $select = $button.closest('tr').find('.moderator-products-select');
            var $assignmentSummary = $select.siblings('.assignment-summary');
            
            if (confirm('Are you sure you want to clear ALL product assignments for ' + moderatorName + '?')) {
                // Clear Select2
                $select.val(null).trigger('change');
                
                // Update assignment summary
                $assignmentSummary.html(`
                    <div class="no-assignment">
                        <span class="dashicons dashicons-warning"></span>
                        <div class="warning-text">
                            <strong>No Products Assigned</strong>
                            <span>This moderator will NOT receive any orders</span>
                        </div>
                    </div>
                `);
                
                // Show success feedback
                $button.html('<span class="dashicons dashicons-yes"></span> Cleared!').prop('disabled', true);
                setTimeout(function() {
                    $button.html('<span class="dashicons dashicons-trash"></span> Clear All').prop('disabled', false);
                }, 2000);
            }
        });

        // View all products in modal
        $('.view-products').on('click', function(e) {
            e.preventDefault();
            
            var moderatorName = $(this).data('moderator-name');
            var productsData = $(this).data('products');
            
            if (!productsData || productsData.length === 0) {
                alert('No products assigned to ' + moderatorName);
                return;
            }
            
            // Show modal with products
            showProductsModal(moderatorName, productsData);
        });

        // Function to show products modal
        function showProductsModal(moderatorName, products) {
            var modal = $('#products-modal');
            var modalTitle = $('#modal-title');
            var modalContent = $('#modal-content');
            
            // Set modal title
            modalTitle.text('Products Assigned to: ' + moderatorName);
            
            // Build products list HTML
            var html = '<div class="modal-products-container">';
            
            // Summary section
            html += '<div class="modal-summary" style="background: #e7f3ff; padding: 15px; border-radius: 6px; margin-bottom: 20px;">';
            html += '<h4 style="margin: 0 0 10px 0; color: #0073aa;">📊 Assignment Summary</h4>';
            html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">';
            html += '<div style="text-align: center; padding: 10px; background: white; border-radius: 4px;">';
            html += '<div style="font-size: 24px; font-weight: bold; color: #0073aa;">' + products.length + '</div>';
            html += '<div style="font-size: 12px; color: #666;">Total Products</div>';
            html += '</div>';
            
            // Count by product type
            var typeCount = {};
            products.forEach(function(product) {
                var type = product.type || 'simple';
                typeCount[type] = (typeCount[type] || 0) + 1;
            });
            
            for (var type in typeCount) {
                html += '<div style="text-align: center; padding: 10px; background: white; border-radius: 4px;">';
                html += '<div style="font-size: 20px; font-weight: bold; color: #46b450;">' + typeCount[type] + '</div>';
                html += '<div style="font-size: 12px; color: #666;">' + type.charAt(0).toUpperCase() + type.slice(1) + '</div>';
                html += '</div>';
            }
            
            html += '</div>';
            html += '</div>';
            
            // Products list
            html += '<div class="modal-products-list">';
            html += '<h4 style="margin: 0 0 15px 0; color: #0073aa;">🛍️ Assigned Products</h4>';
            html += '<div style="display: grid; gap: 10px; max-height: 400px; overflow-y: auto;">';
            
            products.forEach(function(product, index) {
                html += '<div class="modal-product-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #0073aa;">';
                html += '<div style="flex: 1;">';
                html += '<div style="font-weight: bold; color: #333; margin-bottom: 4px;">' + (index + 1) + '. ' + product.name + '</div>';
                html += '<div style="display: flex; gap: 15px; font-size: 12px; color: #666;">';
                html += '<span>ID: ' + product.id + '</span>';
                html += '<span>Type: ' + (product.type || 'simple') + '</span>';
                if (product.price) {
                    html += '<span>Price: ৳' + product.price + '</span>';
                }
                html += '</div>';
                html += '</div>';
                html += '<div>';
                html += '<a href="' + '<?php echo admin_url('post.php?post='); ?>' + product.id + '&action=edit" target="_blank" class="button button-small" style="font-size: 11px; padding: 4px 8px;">Edit Product</a>';
                html += '</div>';
                html += '</div>';
            });
            
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            
            modalContent.html(html);
            modal.show();
        }

        // Close modal functions
        $('#close-modal, #modal-close-btn').on('click', function() {
            $('#products-modal').hide();
        });

        // Close modal when clicking outside
        $('#products-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });

        // Close modal with ESC key
        $(document).on('keyup', function(e) {
            if (e.keyCode === 27) {
                $('#products-modal').hide();
            }
        });

        // Auto-update assignment summary when selection changes
        $('.moderator-products-select').on('change', function() {
            var $select = $(this);
            var selectedData = $select.select2('data');
            var $assignmentSummary = $select.siblings('.assignment-summary');
            
            if (selectedData.length === 0) {
                $assignmentSummary.html(`
                    <div class="no-assignment">
                        <span class="dashicons dashicons-warning"></span>
                        <div class="warning-text">
                            <strong>No Products Assigned</strong>
                            <span>This moderator will NOT receive any orders</span>
                        </div>
                    </div>
                `);
            } else {
                var productsHTML = '';
                var displayCount = Math.min(4, selectedData.length);
                
                for (var i = 0; i < displayCount; i++) {
                    productsHTML += `
                        <div class="product-chip">
                            <span class="product-name">${selectedData[i].text}</span>
                        </div>
                    `;
                }
                
                if (selectedData.length > 4) {
                    productsHTML += `
                        <div class="more-products-chip">
                            + ${selectedData.length - 4} more product${selectedData.length - 4 > 1 ? 's' : ''}
                        </div>
                    `;
                }
                
                $assignmentSummary.html(`
                    <div class="has-assignment">
                        <div class="assignment-header">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <div class="assignment-stats">
                                <strong>${selectedData.length} Product${selectedData.length > 1 ? 's' : ''} Assigned</strong>
                                <span>Will receive orders for these products</span>
                            </div>
                        </div>
                        <div class="products-quick-view">
                            ${productsHTML}
                        </div>
                    </div>
                `);
            }
        });

        // Focus on search field when page loads if there's a search term
        <?php if (!empty($search_term)): ?>
            $('#search').focus();
        <?php endif; ?>
    });
    </script>

    <style>
    .card{max-width: 100%;}
    .sequence-badge {
        display: inline-block;
        width: 30px;
        height: 30px;
        background: #0073aa;
        color: white;
        text-align: center;
        line-height: 30px;
        border-radius: 50%;
        font-weight: bold;
    }
    .status-badge {
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-active { background: #46b450; color: white; }
    .status-inactive { background: #cc1818; color: white; }
    .no-assignment {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #fff5f5;
        border: 1px solid #ffd6d6;
        border-radius: 4px;
        margin-top: 10px;
    }
    .no-assignment .dashicons { color: #cc1818; }
    .has-assignment {
        padding: 10px;
        background: #f8fff8;
        border: 1px solid #d6ffd6;
        border-radius: 4px;
        margin-top: 10px;
    }
    .assignment-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .assignment-header .dashicons { color: #46b450; }
    .products-quick-view {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .product-chip {
        padding: 4px 8px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .product-name { font-weight: 500; }
    .product-id { color: #666; font-size: 10px; }
    .more-products-chip {
        padding: 4px 8px;
        background: #f0f6fc;
        color: #0073aa;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
    }
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .action-buttons .button {
        display: flex;
        align-items: center;
        gap: 5px;
        justify-content: center;
        text-align: center;
        padding: 6px 10px;
        font-size: 12px;
        line-height: 1.3;
    }
    .clear-products {
        background: #dc3232;
        color: white;
        border-color: #dc3232;
    }
    .clear-products:hover {
        background: #a00;
        border-color: #a00;
        color: white;
    }
    .view-products {
        background: #0073aa;
        color: white;
        border-color: #0073aa;
    }
    .view-products:hover {
        background: #005a87;
        border-color: #005a87;
        color: white;
    }
    .nav-tab-wrapper { margin: 20px 0; }
    .nav-tab { 
        text-decoration: none; 
        padding: 10px 15px; 
        margin: 0 5px 0 0; 
        border: 1px solid #ccd0d4;
        border-bottom: none;
        background: #f0f0f0;
    }
    .nav-tab-active { 
        background: #0073aa; 
        color: white; 
        border-bottom: 1px solid #0073aa;
    }
    .filter-group {
        margin-bottom: 0;
    }
    </style>
    <?php
}
