(function($) {
 'use strict';

 $(document).on('aoam:recent-loading', function(event, isLoading) {
 $('#aoam-recent-assignments-app').toggleClass('is-loading', !!isLoading);
 });
})(jQuery);
