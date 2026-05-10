(function($) {
 'use strict';

 window.AOAM = window.AOAM || {};

 AOAM.closeModalBackdrops = function() {
 $('.modal-backdrop, .aoam-modal-backdrop').remove();
 $('body').removeClass('modal-open');
 };

 $(document).on('click', '[data-aoam-dismiss], .aoam-modal-close', function() {
 AOAM.closeModalBackdrops();
 });

 $(document).on('keyup', function(event) {
 if (event.key === 'Escape') {
 AOAM.closeModalBackdrops();
 }
 });
})(jQuery);
