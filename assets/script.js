jQuery(document).ready(
  function ($) {
    $(document).on(
      'click', '.fed_remove_image', function (e) {
        var closest = $(this).closest('.fed_upload_wrapper');
        closest.find('.fed_upload_input').val('');
        closest.find('.fed_upload_image_dummy').removeClass('fed_hide hidden');
        closest.find('.fed_upload_image_actual').addClass('fed_hide hidden');
        $(this).addClass('fed_hide hidden');
        e.preventDefault();
        e.stopPropagation();
      }
    );
  }
);
