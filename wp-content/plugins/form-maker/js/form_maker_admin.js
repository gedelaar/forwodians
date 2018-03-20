function fm_select_value(obj) {
  obj.focus();
  obj.select();
}

function fm_doNothing(event) {
  var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
  if (keyCode == 13) {
    if (event.preventDefault) {
      event.preventDefault();
    }
    else {
      event.returnValue = false;
    }
  }
}

function fm_ajax_save(form_id) {
  var search_value = jQuery("#search_value").val();
  var current_id = jQuery("#current_id").val();
  var page_number = jQuery("#page_number").val();
  var search_or_not = jQuery("#search_or_not").val();
  var ids_string = jQuery("#ids_string").val();
  var image_order_by = jQuery("#image_order_by").val();
  var asc_or_desc = jQuery("#asc_or_desc").val();
  var ajax_task = jQuery("#ajax_task").val();
  var image_current_id = jQuery("#image_current_id").val();
  ids_array = ids_string.split(",");

  var post_data = {};
  post_data["search_value"] = search_value;
  post_data["current_id"] = current_id;
  post_data["page_number"] = page_number;
  post_data["image_order_by"] = image_order_by;
  post_data["asc_or_desc"] = asc_or_desc;
  post_data["ids_string"] = ids_string;
  post_data["task"] = "ajax_search";
  post_data["ajax_task"] = ajax_task;
  post_data["image_current_id"] = image_current_id;

  jQuery.post(
    jQuery('#' + form_id).action,
    post_data,

    function (data) {
      var str = jQuery(data).find('#images_table').html();
      jQuery('#images_table').html(str);
      var str = jQuery(data).find('#tablenav-pages').html();
      jQuery('#tablenav-pages').html(str);
      jQuery("#show_hide_weights").val("Hide order column");
      fm_show_hide_weights();
      fm_run_checkbox();
    }
  ).success(function (jqXHR, textStatus, errorThrown) {
  });
  return false;
}

function fm_run_checkbox() {
  jQuery("tbody").children().children(".check-column").find(":checkbox").click(function (l) {
    if ("undefined" == l.shiftKey) {
      return true
    }
    if (l.shiftKey) {
      if (!i) {
        return true
      }
      d = jQuery(i).closest("form").find(":checkbox");
      f = d.index(i);
      j = d.index(this);
      h = jQuery(this).prop("checked");
      if (0 < f && 0 < j && f != j) {
        d.slice(f, j).prop("checked", function () {
          if (jQuery(this).closest("tr").is(":visible")) {
            return h
          }
          return false
        })
      }
    }
    i = this;
    var k = jQuery(this).closest("tbody").find(":checkbox").filter(":visible").not(":checked");
    jQuery(this).closest("table").children("thead, tfoot").find(":checkbox").prop("checked", function () {
      return (0 == k.length)
    });
    return true
  });
  jQuery("thead, tfoot").find(".check-column :checkbox").click(function (m) {
    var n = jQuery(this).prop("checked"), l = "undefined" == typeof toggleWithKeyboard ? false : toggleWithKeyboard, k = m.shiftKey || l;
    jQuery(this).closest("table").children("tbody").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (jQuery(this).is(":hidden")) {
        return false
      }
      if (k) {
        return jQuery(this).prop("checked")
      }
      else {
        if (n) {
          return true
        }
      }
      return false
    });
    jQuery(this).closest("table").children("thead,  tfoot").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (k) {
        return false
      }
      else {
        if (n) {
          return true
        }
      }
      return false
    })
  });
}

// Set value by id.
function fm_set_input_value(input_id, input_value) {
  if (document.getElementById(input_id)) {
    document.getElementById(input_id).value = input_value;
  }
}

// Submit form by id.
function fm_form_submit(event, form_id, task, id) {
  if (document.getElementById(form_id)) {
    document.getElementById(form_id).submit();
  }
  if (event.preventDefault) {
    event.preventDefault();
  }
  else {
    event.returnValue = false;
  }
}

// Check if required field is empty.
function fm_check_required(id, name) {
  if (jQuery('#' + id).val() == '') {
    alert(name + '* field is required.');
    jQuery('#' + id).attr('style', 'border-color: #FF0000; border-style: solid; border-width: 1px;');
    jQuery('#' + id).focus();
    jQuery('html, body').animate({
      scrollTop: jQuery('#' + id).offset().top - 200
    }, 500);
    return true;
  }
  else {
    return false;
  }
}

// Show/hide order column and drag and drop column.
function fm_show_hide_weights() {
  if (jQuery("#show_hide_weights").val() == 'Show order column') {
    jQuery(".connectedSortable").css("cursor", "default");
    jQuery("#tbody_arr").find(".handle").hide(0);
    jQuery("#th_order").show(0);
    jQuery("#tbody_arr").find(".fm_order").show(0);
    jQuery("#show_hide_weights").val("Hide order column");
    if (jQuery("#tbody_arr").sortable()) {
      jQuery("#tbody_arr").sortable("disable");
    }
  }
  else {
    jQuery(".connectedSortable").css("cursor", "move");
    var page_number;
    if (jQuery("#page_number") && jQuery("#page_number").val() != '' && jQuery("#page_number").val() != 1) {
      page_number = (jQuery("#page_number").val() - 1) * 20 + 1;
    }
    else {
      page_number = 1;
    }
    jQuery("#tbody_arr").sortable({
      handle: ".connectedSortable",
      connectWith: ".connectedSortable",
      update: function (event, tr) {
        jQuery("#draganddrop").attr("style", "");
        jQuery("#draganddrop").html("<strong><p>Changes made in this table should be saved.</p></strong>");
        var i = page_number;
        jQuery('.fm_order').each(function (e) {
          if (jQuery(this).find('input').val()) {
            jQuery(this).find('input').val(i++);
          }
        });
      }
    });//.disableSelection();
    jQuery("#tbody_arr").sortable("enable");
    jQuery("#tbody_arr").find(".handle").show(0);
    jQuery("#tbody_arr").find(".handle").attr('class', 'handle connectedSortable');
    jQuery("#th_order").hide(0);
    jQuery("#tbody_arr").find(".fm_order").hide(0);
    jQuery("#show_hide_weights").val("Show order column");
  }
}

function fm_popup(id) {
  if (typeof id === 'undefined') {
    var id = '';
  }
  var thickDims, tbWidth, tbHeight;
  thickDims = function () {
    var tbWindow = jQuery('#TB_window'), H = jQuery(window).height(), W = jQuery(window).width(), w, h;
    w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 40;
    h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 40;
    if (tbWindow.size()) {
      tbWindow.width(w).height(h);
      jQuery('#TB_iframeContent').width(w).height(h - 27);
      tbWindow.css({'margin-left': '-' + parseInt((w / 2), 10) + 'px'});
      if (typeof document.body.style.maxWidth != 'undefined') {
        tbWindow.css({'top': (H - h) / 2, 'margin-top': '0'});
      }
    }
  };
  thickDims();
  jQuery(window).resize(function () {
    thickDims()
  });
  jQuery('a.thickbox-preview' + id).click(function () {
    tb_click.call(this);
    var alink = jQuery(this).parents('.available-theme').find('.activatelink'), link = '', href = jQuery(this).attr('href'), url, text;
    if (tbWidth = href.match(/&width=[0-9]+/)) {
      tbWidth = parseInt(tbWidth[0].replace(/[^0-9]+/g, ''), 10);
    }
    else {
      tbWidth = jQuery(window).width() - 120;
    }

    if (tbHeight = href.match(/&height=[0-9]+/)) {
      tbHeight = parseInt(tbHeight[0].replace(/[^0-9]+/g, ''), 10);
    }
    else {
      tbHeight = jQuery(window).height() - 120;
    }
    if (alink.length) {
      url = alink.attr('href') || '';
      text = alink.attr('title') || '';
      link = '&nbsp; <a href="' + url + '" target="_top" class="tb-theme-preview-link">' + text + '</a>';
    }
    else {
      text = jQuery(this).attr('title') || '';
      link = '&nbsp; <span class="tb-theme-preview-link">' + text + '</span>';
    }
    jQuery('#TB_title').css({'background-color': '#222', 'color': '#dfdfdf'});
    jQuery('#TB_closeAjaxWindow').css({'float': 'right'});
    jQuery('#TB_ajaxWindowTitle').css({'float': 'left'}).html(link);
    jQuery('#TB_iframeContent').width('100%');
    thickDims();
    return false;
  });
  // Theme details
  jQuery('.theme-detail').click(function () {
    jQuery(this).siblings('.themedetaildiv').toggle();
    return false;
  });
}

function bwg_inputs() {
  jQuery(".fm_int_input").keypress(function (event) {
    var chCode1 = event.which || event.paramlist_keyCode;
    if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46) && (chCode1 != 45)) {
      return false;
    }
    return true;
  });
}

function fm_check_isnum(e) {
  var chCode1 = e.which || e.paramlist_keyCode;
  if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46) && (chCode1 != 45)) {
    return false;
  }
  return true;
}

function fm_change_payment_method(payment_method) {
  switch (payment_method) {
    case 'paypal':
      jQuery('.fm_payment_option').show();
      jQuery('.fm_paypal_option').show();
      jQuery('.fm_stripe_option').hide();
      break;
    case 'stripe':
      jQuery('.fm_payment_option').show();
      jQuery('.fm_paypal_option').hide();
      jQuery('.fm_stripe_option').show();
      break;
    default:
      jQuery('.fm_payment_option').hide();
      jQuery('.fm_paypal_option').hide();
      jQuery('.fm_stripe_option').hide();
  }
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type == "text")) {
    return false;
  }
}

document.onkeypress = stopRKey;

function fmRemoveHeaderImage(e, callback) {
  jQuery('#header_image_url').val('');
  jQuery("#header_image").css("background-image", '');
  jQuery("#header_image").addClass("fm-hide");
}
function fmOpenMediaUploader(e, callback) {
  if (typeof callback == "undefined") {
    callback = false;
  }
  e.preventDefault();
  var custom_uploader = wp.media({
    title: 'Upload',
    button: {
      text: 'Add Image'
    },
    multiple: false
  }).on('select', function () {
    var attachment = custom_uploader.state().get('selection').first().toJSON();
    jQuery('#header_image_url').val(attachment.url);
    jQuery("#header_image").css("background-image", 'url("' + attachment.url + '")');
    jQuery("#header_image").css("background-position", 'center');
    jQuery("#header_image").removeClass("fm-hide");
  }).open();
  return false;
}

jQuery(document).ready(function () {
  jQuery('.pp_display_on #pt0').click(function () {
    var isChecked = jQuery(this).prop('checked');
    jQuery('.pp_display_on input[type="checkbox"]').prop('checked', isChecked);
    if (isChecked) {
      jQuery('.fm-posts-show, .fm-pages-show, .fm-cat-show').removeClass('fm-hide').addClass('fm-show');
    }
    else {
      jQuery('.fm-posts-show, .fm-pages-show, .fm-cat-show').removeClass('fm-show').addClass('fm-hide');
    }
  });

  jQuery('.pp_display_on input[type="checkbox"]:not("#pt0")').click(function () {
    var isChecked = jQuery(this).prop('checked');
    var everythingChecked = jQuery('.pp_display_on #pt0').prop('checked');
    if (everythingChecked && !isChecked) {
      jQuery('.pp_display_on #pt0').prop('checked', false);
    }
  });

  jQuery('.pp_display_on #pt4').click(function () {
    fm_toggle_pages(this);
  });

  jQuery('.pp_display_on #pt3').click(function () {
    fm_toggle_posts(this);
  });

  jQuery('body').on('focusin', '.pp_search_posts', function () {
    var this_input = jQuery(this);
    this_input.closest('ul').find('.pp_live_search').removeClass('fm-hide');
    if (!this_input.hasClass('already_triggered')) {
      this_input.addClass('already_triggered');
      pp_live_search(this_input, 500, true);
    }
  });

  jQuery(document).click(function () {
    jQuery('.pp_live_search').addClass('fm-hide');
  });

  jQuery('body').on('click', '.pp_search_posts', function () {
    return false;
  });

  jQuery('body').on('input', '.pp_search_posts', function () {
    pp_live_search(jQuery(this), 500, true);
  });

  jQuery('body').on('click', '.pp_search_results li', function () {
    var this_item = jQuery(this);

    if (!this_item.hasClass('pp_no_res')) {
      var text = this_item.text(),
        id = this_item.data('post_id'),
        main_container = this_item.closest('.fm-pp'),
        display_box = main_container.find('.pp_selected'),
        value_field = main_container.find('.pp_exclude'),
        new_item = '<span data-post_id="' + id + '">' + text + '<span class="pp_selected_remove">x</span></span>';

      if (-1 === display_box.html().indexOf('data-post_id="' + id + '"')) {
        display_box.append(new_item);
        if ('' === value_field.val()) {
          value_field.val(id);
        }
        else {
          value_field.val(function (index, value) {
            return value + "," + id;
          });
        }
      }
    }

    return false;
  });

  jQuery('body').on('click', '.pp_selected span.pp_selected_remove', function () {
    var this_item = jQuery(this).parent(),
      value_field = this_item.closest('.fm-pp').find('.pp_exclude'),
      value_string = value_field.val(),
      id = this_item.data('post_id');
    if (-1 !== value_string.indexOf(id)) {
      var str_toreplace = -1 !== value_string.indexOf(',' + id) ? ',' + id : id + ',',
        str_toreplace = -1 !== value_string.indexOf(',') ? str_toreplace : id,
        new_value = value_string;

      new_value = value_string.replace(str_toreplace, '');
      value_field.val(new_value);
    }

    this_item.remove();
    return false;
  });
});

function fm_toggle_posts(that) {
  var isChecked = jQuery(that).prop('checked');
  if (isChecked) {
    jQuery('.fm-posts-show, .fm-cat-show').removeClass('fm-hide').addClass('fm-show');
  }
  else {
    jQuery('.fm-posts-show, .fm-cat-show').removeClass('fm-show').addClass('fm-hide');
  }
}

function fm_toggle_pages(that) {
  var isChecked = jQuery(that).prop('checked');
  if (isChecked) {
    jQuery('.fm-pages-show').removeClass('fm-hide').addClass('fm-show');
  }
  else {
    jQuery('.fm-pages-show').removeClass('fm-show').addClass('fm-hide');
  }
}

function fm_apply_options(task) {
  fm_set_input_value('task', task);
  document.getElementById('adminForm').submit();
}

function pp_live_search(input, delay, full_content) {
  var this_input = input,
    search_value = this_input.val(),
    post_type = this_input.data('post_type');

  setTimeout(function () {
    if (search_value === this_input.val()) {
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          action: 'manage_fm',
          task: 'fm_live_search',
          nonce_fm: nonce_fm,
          pp_live_search: search_value,
          pp_post_type: post_type,
          pp_full_content: full_content
        },
        beforeSend: function (data) {
          this_input.css('width', '95%');
          this_input.parent().find('.fm-loading').css('display', 'inline-block');
        },
        success: function (data) {
          this_input.css('width', '100%');
          this_input.parent().find('.fm-loading').css('display', 'none');
          /* if ( true === full_content ) { */
          this_input.closest('.fm-pp').find('.pp_search_results').replaceWith(data);
          /* } else {
           this_input.closest('.fm-pp').find('.pp_search_results').append(data);
           } */
        },
        error: function (err) {
          console.log(err);
        }
      });
    }
  }, delay);
}

function fm_toggle(elem) {
  jQuery(elem).parent().next().toggleClass('hide');
}

function change_tab(elem) {
  jQuery('.fm-subscriber-header .fm-button').removeClass('active-button');
  jQuery('.fm-subscriber-header .' + elem).addClass('active-button');
  jQuery('.fm-subscriber-content').hide();
  jQuery('.' + elem + '-tab').show();
}

function change_form_type(type) {
  jQuery('.fm-form-types span').removeClass('active');
  jQuery('.fm-form-types').find('.fm-' + type).addClass('active');
  jQuery('#type_settings_fieldset tr').removeClass('fm-show').addClass('fm-hide');
}

function change_hide_show(className) {
  jQuery('.' + className + '.fm-hide').removeClass('fm-hide').addClass('fm-temporary');
  jQuery('.' + className + '.fm-show').removeClass('fm-show').addClass('fm-hide');
  jQuery('.' + className + '.fm-show-table').removeClass('fm-show-table').addClass('fm-hide');
  jQuery('.' + className + '.fm-temporary').removeClass('fm-temporary').addClass('fm-show');
  if (className != 'fm-embedded') {
    fm_toggle_posts(jQuery('.pp_display_on #pt3'));
    fm_toggle_pages(jQuery('.pp_display_on #pt4'));
  }
}

function fm_change_radio_checkbox_text(elem) {
  var labels_array = [];
  labels_array['stripemode'] = ['Test', 'Live'];
  labels_array['checkout_mode'] = ['Testmode', 'Production'];
  labels_array['mail_mode'] = ['Text', 'HTML'];
  labels_array['mail_mode_user'] = ['Text', 'HTML'];
  labels_array['value'] = ['1', '0'];
  labels_array['popover_show_on'] = ['Page Exit', 'Page Load'];
  labels_array['topbar_position'] = ['Bottom', 'Top'];
  labels_array['scrollbox_position'] = ['Left', 'Right'];

  jQuery(elem).val(labels_array['value'][jQuery(elem).val()]);
  jQuery(elem).next().val(jQuery(elem).val());

  var clicked_element = labels_array[jQuery(elem).attr('name')];
  jQuery(elem).find('label').html(clicked_element[jQuery(elem).val()]);
  if (jQuery(elem).hasClass("fm-text-yes")) {
    jQuery(elem).removeClass('fm-text-yes').addClass('fm-text-no');
    jQuery(elem).find("span").animate({
      right: parseInt(jQuery(elem).css("width")) - 14 + 'px'
    }, 400, function () {
    });
  }
  else {
    jQuery(elem).removeClass('fm-text-no').addClass('fm-text-yes');
    jQuery(elem).find("span").animate({
      right: 0
    }, 400, function () {
    });
  }
}

function fm_show_hide(class_name) {
  if (jQuery('.' + class_name).hasClass('fm-hide')) {
    jQuery('.' + class_name).removeClass('fm-hide').addClass('fm-show');
  }
  else {
    jQuery('.' + class_name).removeClass('fm-show').addClass('fm-hide');
  }
}