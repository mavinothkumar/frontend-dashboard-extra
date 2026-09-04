jQuery(document).ready(
  function ($) {
    var remove_image = $('.fed_remove_image')
    if (remove_image.length) {
      $('body').on(
        'click', '.fed_remove_image', function (e) {
          var closest = $(this).closest('.fed_upload_wrapper')
          closest.find('.fed_upload_input').val('')
          closest.find('.fed_upload_image_container').find('.fed_upload_image_dummy').removeClass('fed_hide')
          closest.find('.fed_upload_image_container').find('.fed_upload_image_actual').addClass('fed_hide')
          $(this).addClass('fed_hide')
          e.preventDefault()
        }
      )
    }
  }
)
