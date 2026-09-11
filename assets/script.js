jQuery(document).ready(function ($) {
  // File upload remove handler
  $(document).on('click', '.fed_remove_image', function (e) {
    var closest = $(this).closest('.fed_upload_wrapper');
    closest.find('.fed_upload_input').val('');
    closest.find('.fed_upload_image_dummy').removeClass('fed_hide hidden');
    closest.find('.fed_upload_image_actual').addClass('fed_hide hidden');
    $(this).addClass('fed_hide hidden');
    e.preventDefault();
    e.stopPropagation();
  });

  // Color picker: Native color input change/input
  $(document).on('input change', '.fed_color_native', function () {
    var hex = $(this).val().toUpperCase();
    var container = $(this).closest('.fed_color_picker_container');
    container.find('.fed_color_swatch').css('background-color', hex);
    var input = container.find('.fed_color_input');
    if (input.val() !== hex) {
      input.val(hex).trigger('change');
    }
  });

  // Color picker: Text input typing/pasting
  $(document).on('input', '.fed_color_input', function () {
    var val = $(this).val().trim();
    if (!val) return;
    if (val.charAt(0) !== '#') {
      val = '#' + val;
    }
    var container = $(this).closest('.fed_color_picker_container');
    var valid3 = /^#([0-9A-Fa-f]{3})$/;
    var valid6 = /^#([0-9A-Fa-f]{6})$/;
    var fullHex = '';
    if (valid6.test(val)) {
      fullHex = val.toUpperCase();
    } else if (valid3.test(val)) {
      var r = val.charAt(1), g = val.charAt(2), b = val.charAt(3);
      fullHex = ('#' + r + r + g + g + b + b).toUpperCase();
    }
    if (fullHex) {
      container.find('.fed_color_swatch').css('background-color', fullHex);
      container.find('.fed_color_native').val(fullHex.toLowerCase());
    }
  });

  // Color picker: Text input blur formatting
  $(document).on('blur', '.fed_color_input', function () {
    var val = $(this).val().trim();
    var container = $(this).closest('.fed_color_picker_container');
    if (!val) return;
    if (val.charAt(0) !== '#') {
      val = '#' + val;
    }
    var valid3 = /^#([0-9A-Fa-f]{3})$/;
    var valid6 = /^#([0-9A-Fa-f]{6})$/;
    if (valid6.test(val)) {
      var upper = val.toUpperCase();
      $(this).val(upper);
      container.find('.fed_color_swatch').css('background-color', upper);
      container.find('.fed_color_native').val(val.toLowerCase());
    } else if (valid3.test(val)) {
      var r = val.charAt(1), g = val.charAt(2), b = val.charAt(3);
      var full = ('#' + r + r + g + g + b + b).toUpperCase();
      $(this).val(full);
      container.find('.fed_color_swatch').css('background-color', full);
      container.find('.fed_color_native').val(full.toLowerCase());
    }
  });
});

