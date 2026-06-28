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

 AOAM.showNotice = function($container, noticeClass, title, lines) {
 var $paragraph = $('<p>');
 $('<strong>').text(title).appendTo($paragraph);

 if (lines && lines.length) {
 $paragraph.append($('<br>'));
 lines.forEach(function(line, index) {
 if (index > 0) {
 $paragraph.append(' | ');
 }
 $paragraph.append(document.createTextNode(line));
 });
 }

 $container
 .removeClass('notice-success notice-error')
 .addClass('notice ' + noticeClass)
 .empty()
 .append($paragraph)
 .fadeIn();
 };

  // Handle manual remote import AJAX trigger
  $(document).on('click', '#aoam_run_remote_import_btn', function(e) {
    e.preventDefault();
    var $button = $(this);
    var $spinner = $button.find('.aoam-spinner');
    var $btnText = $button.find('.aoam-btn-text');
    var $resultContainer = $('#aoam-import-result-container');
    var nonce = $button.data('nonce');

    if ($button.prop('disabled')) {
      return;
    }

    // Reset UI
    $resultContainer.hide().removeClass('notice notice-success notice-error').html('');
    $button.prop('disabled', true);
    $spinner.addClass('is-active');
    $btnText.text('Importing, please wait...');

    $.ajax({
      url: aoamAdmin.ajaxUrl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'aoam_run_remote_import_ajax',
        nonce: nonce
      },
      success: function(response) {
        $button.prop('disabled', false);
        $spinner.removeClass('is-active');
        $btnText.text('Run Import Now');

        if (response.success) {
          var data = response.data;
          AOAM.showNotice($resultContainer, 'notice-success', 'Import Completed Successfully!', [
            'Imported: ' + data.imported,
            'Skipped: ' + data.skipped,
            'Failed: ' + data.failed
          ]);
        } else {
          var errorMsg = response.data || 'Unknown error occurred during import.';
          AOAM.showNotice($resultContainer, 'notice-error', 'Import Failed:', [errorMsg]);
        }
      },
      error: function(xhr, status, error) {
        $button.prop('disabled', false);
        $spinner.removeClass('is-active');
        $btnText.text('Run Import Now');

        AOAM.showNotice($resultContainer, 'notice-error', 'Connection Error:', [error]);
      }
    });
  });
})(jQuery);
