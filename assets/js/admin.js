(function($) {
 'use strict';

 // Auto Order Assign Moderator Admin JavaScript
 window.AOAM_Admin = {
 
 init: function() {
 this.bindEvents();
 this.initializeSortable();
 this.initializeStatusColors();
 },
 
 bindEvents: function() {
 // Update row appearance when status changes
 $('.moderator-status').on('change', this.handleStatusChange);
 
 // Clear products for specific moderator
 $('.clear-products').on('click', this.handleClearProducts);
 
 // View all products in modal
 $('.view-products').on('click', this.handleViewProducts);
 
 // Modal close events
 $('#close-modal, #modal-close-btn').on('click', this.closeProductsModal);
 $(document).on('keyup', this.handleEscapeKey);
 $('#products-modal').on('click', this.handleModalBackdrop);
 
 // Auto-update assignment summary when selection changes
 $('.moderator-products-select').on('change', this.handleProductSelectionChange);
 
 // Direct moderator update
 $('#update_moderator_direct').on('click', this.handleDirectModeratorUpdate);
 },
 
 initializeSortable: function() {
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
 },
 
 initializeStatusColors: function() {
 // Initialize row colors based on current status
 $('.moderator-status').each(function() {
 var row = $(this).closest('tr');
 if ($(this).val() === 'inactive') {
 row.addClass('moderator-inactive');
 }
 });
 },
 
 handleStatusChange: function() {
 var row = $(this).closest('tr');
 if ($(this).val() === 'inactive') {
 row.addClass('moderator-inactive');
 } else {
 row.removeClass('moderator-inactive');
 }
 },
 
 handleClearProducts: function(e) {
 e.preventDefault();
 
 var $button = $(this);
 var moderatorName = $button.closest('tr').find('.moderator-info strong').text();
 var $select = $button.closest('tr').find('.moderator-products-select');
 var $assignmentSummary = $select.siblings('.assignment-summary');
 
 if (confirm(AOAM_Admin.getText('confirm_clear_products').replace('%s', moderatorName))) {
 // Clear Select2
 $select.val(null).trigger('change');
 
 // Update assignment summary
 $assignmentSummary.html(AOAM_Admin.getNoAssignmentHTML());
 
 // Show success feedback
 $button.html('<span class="dashicons dashicons-yes"></span> ' + AOAM_Admin.getText('cleared')).prop('disabled', true);
 setTimeout(function() {
 $button.html('<span class="dashicons dashicons-trash"></span> ' + AOAM_Admin.getText('clear_all')).prop('disabled', false);
 }, 2000);
 }
 },
 
 handleViewProducts: function(e) {
 e.preventDefault();
 
 var moderatorName = $(this).data('moderator-name');
 var productsData = $(this).data('products');
 
 if (!productsData || productsData.length === 0) {
 alert(AOAM_Admin.getText('no_products_assigned').replace('%s', moderatorName));
 return;
 }
 
 // Show modal with products
 AOAM_Admin.showProductsModal(moderatorName, productsData);
 },
 
 showProductsModal: function(moderatorName, products) {
 var modal = $('#products-modal');
 var modalTitle = $('#modal-title');
 var modalContent = $('#modal-content');
 
 // Set modal title
 modalTitle.text(AOAM_Admin.getText('products_assigned_to') + ' ' + moderatorName);
 
 // Build products list HTML
 var html = '<div class="modal-products-container">';
 
 // Summary section
 html += AOAM_Admin.getModalSummaryHTML(products);
 
 // Products list
 html += AOAM_Admin.getProductsListHTML(products);
 
 html += '</div>';
 
 modalContent.html(html);
 modal.show();
 },
 
 getModalSummaryHTML: function(products) {
 var html = '<div class="modal-summary" style="background: #e7f3ff; padding: 15px; border-radius: 6px; margin-bottom: 20px;">';
 html += '<h4 style="margin: 0 0 10px 0; color: #0073aa;"> ' + AOAM_Admin.getText('assignment_summary') + '</h4>';
 html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">';
 html += '<div style="text-align: center; padding: 10px; background: white; border-radius: 4px;">';
 html += '<div style="font-size: 24px; font-weight: bold; color: #0073aa;">' + products.length + '</div>';
 html += '<div style="font-size: 12px; color: #666;">' + AOAM_Admin.getText('total_products') + '</div>';
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
 
 return html;
 },
 
 getProductsListHTML: function(products) {
 var html = '<div class="modal-products-list">';
 html += '<h4 style="margin: 0 0 15px 0; color: #0073aa;"> ' + AOAM_Admin.getText('assigned_products') + '</h4>';
 html += '<div style="display: grid; gap: 10px; max-height: 400px; overflow-y: auto;">';
 
 products.forEach(function(product, index) {
 html += '<div class="modal-product-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #0073aa;">';
 html += '<div style="flex: 1;">';
 html += '<div style="font-weight: bold; color: #333; margin-bottom: 4px;">' + (index + 1) + '. ' + product.name + '</div>';
 html += '<div style="display: flex; gap: 15px; font-size: 12px; color: #666;">';
 html += '<span>ID: ' + product.id + '</span>';
 html += '<span>Type: ' + (product.type || 'simple') + '</span>';
 if (product.price) {
 html += '<span>Price: ' + product.price + '</span>';
 }
 html += '</div>';
 html += '</div>';
 html += '<div>';
 html += '<a href="' + aoam_ajax.admin_url + 'post.php?post=' + product.id + '&action=edit" target="_blank" class="button button-small" style="font-size: 11px; padding: 4px 8px;">' + AOAM_Admin.getText('edit_product') + '</a>';
 html += '</div>';
 html += '</div>';
 });
 
 html += '</div>';
 html += '</div>';
 
 return html;
 },
 
 closeProductsModal: function() {
 $('#products-modal').hide();
 },
 
 handleEscapeKey: function(e) {
 if (e.keyCode === 27) {
 AOAM_Admin.closeProductsModal();
 }
 },
 
 handleModalBackdrop: function(e) {
 if (e.target === this) {
 AOAM_Admin.closeProductsModal();
 }
 },
 
 handleProductSelectionChange: function() {
 var $select = $(this);
 var selectedData = $select.select2('data');
 var $assignmentSummary = $select.siblings('.assignment-summary');
 
 if (selectedData.length === 0) {
 $assignmentSummary.html(AOAM_Admin.getNoAssignmentHTML());
 } else {
 $assignmentSummary.html(AOAM_Admin.getAssignmentHTML(selectedData));
 }
 },
 
 getNoAssignmentHTML: function() {
 return '<div class="no-assignment">' +
 '<span class="dashicons dashicons-warning"></span>' +
 '<div class="warning-text">' +
 '<strong>' + AOAM_Admin.getText('no_products_assigned_title') + '</strong>' +
 '<span>' + AOAM_Admin.getText('no_products_assigned_desc') + '</span>' +
 '</div>' +
 '</div>';
 },
 
 getAssignmentHTML: function(selectedData) {
 var productsHTML = '';
 var displayCount = Math.min(4, selectedData.length);
 
 for (var i = 0; i < displayCount; i++) {
 productsHTML += '<div class="product-chip">' +
 '<span class="product-name">' + selectedData[i].text + '</span>' +
 '</div>';
 }
 
 if (selectedData.length > 4) {
 productsHTML += '<div class="more-products-chip">' +
 '+ ' + (selectedData.length - 4) + ' ' + (selectedData.length - 4 > 1 ? AOAM_Admin.getText('more_products') : AOAM_Admin.getText('more_product')) +
 '</div>';
 }
 
 return '<div class="has-assignment">' +
 '<div class="assignment-header">' +
 '<span class="dashicons dashicons-yes-alt"></span>' +
 '<div class="assignment-stats">' +
 '<strong>' + selectedData.length + ' ' + (selectedData.length > 1 ? AOAM_Admin.getText('products_assigned') : AOAM_Admin.getText('product_assigned')) + '</strong>' +
 '<span>' + AOAM_Admin.getText('will_receive_orders') + '</span>' +
 '</div>' +
 '</div>' +
 '<div class="products-quick-view">' +
 productsHTML +
 '</div>' +
 '</div>';
 },
 
 handleDirectModeratorUpdate: function() {
 var newModeratorId = $('#assigned_moderator_direct').val();
 var orderId = $('#assigned_moderator_direct').closest('.order_data_column').find('input[name="order_id"]').val();
 
 if (!newModeratorId) {
 alert(' ' + AOAM_Admin.getText('please_select_moderator'));
 return;
 }
 
 if (confirm(AOAM_Admin.getText('confirm_change_moderator'))) {
 var $button = $(this);
 $button.text(AOAM_Admin.getText('updating') + '...').prop('disabled', true);
 
 $.ajax({
 url: aoam_ajax.ajax_url,
 type: 'POST',
 data: {
 action: 'update_order_moderator_direct',
 order_id: orderId,
 moderator_id: newModeratorId,
 nonce: aoam_ajax.nonce
 },
 success: function(response) {
 if (response.success) {
 alert(' ' + AOAM_Admin.getText('moderator_updated'));
 location.reload();
 } else {
 alert(' ' + AOAM_Admin.getText('error') + ': ' + response.data);
 $button.text(' ' + AOAM_Admin.getText('update_moderator')).prop('disabled', false);
 }
 },
 error: function() {
 alert(' ' + AOAM_Admin.getText('network_error'));
 $button.text(' ' + AOAM_Admin.getText('update_moderator')).prop('disabled', false);
 }
 });
 }
 },
 
 getText: function(key) {
 var texts = {
 'confirm_clear_products': 'Are you sure you want to clear ALL product assignments for %s?',
 'cleared': 'Cleared!',
 'clear_all': 'Clear All',
 'no_products_assigned': 'No products assigned to %s',
 'products_assigned_to': 'Products Assigned to:',
 'assignment_summary': 'Assignment Summary',
 'total_products': 'Total Products',
 'assigned_products': 'Assigned Products',
 'edit_product': 'Edit Product',
 'no_products_assigned_title': 'No Products Assigned',
 'no_products_assigned_desc': 'This moderator will NOT receive any orders',
 'products_assigned': 'Products Assigned',
 'product_assigned': 'Product Assigned',
 'will_receive_orders': 'Will receive orders for these products',
 'more_products': 'more products',
 'more_product': 'more product',
 'please_select_moderator': 'Please select a moderator.',
 'confirm_change_moderator': 'Are you sure you want to change the assigned moderator?',
 'updating': 'Updating',
 'moderator_updated': 'Moderator updated successfully!',
 'error': 'Error',
 'update_moderator': 'Update Moderator',
 'network_error': 'Network error. Please try again.'
 };
 
 return texts[key] || key;
 }
 };
 
 // Initialize when document is ready
 $(document).ready(function() {
 AOAM_Admin.init();
 
 // Focus on search field when page loads if there's a search term
 var searchTerm = $('#search').val();
 if (searchTerm && searchTerm.length > 0) {
 $('#search').focus();
 }
 });
 
})(jQuery);