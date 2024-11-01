jQuery(document).ready(function () {
  /* Open/Close account div in Settings page */
  jQuery(".tkf_display_content").on("click", function() {
    var cont = jQuery(this).parent().find(".tkf_feed_section_content");
    if( cont.hasClass("tkf-hidden") ) {
      cont.removeClass("tkf-hidden");
    } else {
      cont.addClass("tkf-hidden");
    }
  });

  jQuery("#tkf_save_feed_apply").on("click", function () {
    tkf_save_feed();
  });

  jQuery('input[type=radio][name=feed_type]').change(function() {
    if (this.value === 'blog_style') {
      jQuery(".tkf_thumb_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_masonry_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_slideshow_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_blog_style_setting").removeClass("tkf-hidden");
    }
    else if (this.value === 'thumbnails') {
      jQuery(".tkf_blog_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_masonry_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_slideshow_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_thumb_style_setting").removeClass("tkf-hidden");
    }
    else if (this.value === 'masonry') {
      jQuery(".tkf_blog_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_slideshow_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_thumb_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_masonry_style_setting").removeClass("tkf-hidden");
    }
    else if (this.value === 'slideshow') {
      jQuery(".tkf_slideshow_style_setting").removeClass("tkf-hidden");
      jQuery(".tkf_blog_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_thumb_style_setting").addClass("tkf-hidden");
      jQuery(".tkf_masonry_style_setting").addClass("tkf-hidden");
    }
  });

  jQuery(".tkf_pro_notice").mouseenter(function(){
    jQuery(this).append("<div class='tkf_pro_notice_cont'><p>Pro Version will be soon</p></div>");
  }).mouseleave(function() {
    jQuery(".tkf_pro_notice_cont").remove();
  });

});

function tkf_save_feed() {
  jQuery("#tkf_save_feed_apply").text('Updating...').addClass('tkf_loading');
  var data = jQuery("#tkf_save_feed").serialize();
  jQuery.ajax( {
    url: ajaxurl,
    type: "POST",
    data: data,
    tkf_ajax_nonce:  tkf_obj.tkf_ajax_nonce,
    success: function ( result ) {
      if ( result['success'] ) {
          var res = result['data'];
          get_save_videos_api( res['user_id'], res['feed_id'], res['save_action'], res['redirect_url'], 0, 0 );
      }
    },
    error: function ( xhr ) {
      var html = '<div class="tkf_msg_error">Feed does not updated</div>';
      jQuery("#tkf_save_feed_apply").parent().parent().append(html);
    },
  });

}

function get_save_videos_api( user_id, feed_id, save_action, redirect_url, cursor, iter ) {
  jQuery.ajax( {
    url: ajaxurl,
    type: "POST",
    data: {
      action: "feeds_tkf",
      task: "get_save_videos",
      user_id: user_id,
      feed_id: feed_id,
      save_action: save_action,
      redirect_url: redirect_url,
      cursor: cursor,
      iter: iter,
      tkf_ajax_nonce:  tkf_obj.tkf_ajax_nonce,
    },
    tkf_ajax_nonce:  tkf_obj.tkf_ajax_nonce,
    success: function ( result ) {
      var html = '';
      if ( result['success'] ) {
        var res = result['data'];
        if ( res['has_more'] === true ) {
          var iter = parseInt(res['iter']+1);
            get_save_videos_api( res['user_id'], res['feed_id'], res['save_action'], res['redirect_url'], res['cursor'], iter );
        } else {
            jQuery("#tkf_save_feed_apply").text('Update').removeClass('tkf_loading');
            setTimeout(() => { jQuery(".tkf_msg_success, .tkf_msg_error").remove(); }, 3000);

            if( res['save_action'] === 'insert' ) {
                location.href = res['redirect_url'];
            } else {
                var e = jQuery('<div class="tkf_msg_success"></div>');
                e.text(res['msg']);
                jQuery("#tkf_save_feed_apply").parent().parent().append(e);
            }
        }
      } else {
        jQuery("#tkf_save_feed_apply").text('Update').removeClass('tkf_loading');
        setTimeout(() => { jQuery(".tkf_msg_success, .tkf_msg_error").remove(); }, 3000);
        var e = jQuery('<div class="tkf_msg_error"></div>');
        e.text(res['msg']);
        jQuery("#tkf_save_feed_apply").parent().parent().append(e);
      }

    },
    error: function ( xhr ) {
      jQuery("#tkf_save_feed_apply").text('Update').removeClass('tkf_loading');
      setTimeout(() => { jQuery(".tkf_msg_success, .tkf_msg_error").remove(); }, 3000);

      var html = '<div class="tkf_msg_error">Feed does not updated</div>';
      jQuery("#tkf_save_feed_apply").parent().parent().append(html);
    },
  });

}
