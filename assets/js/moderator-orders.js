(function($) {
 'use strict';

 $(document).on('change', '.mobile-status-form select[name="order_status"]', function() {
 $(this).closest('.mobile-order-card').addClass('is-updating');
 });
})(jQuery);
