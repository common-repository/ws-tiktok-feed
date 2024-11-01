jQuery(document).ready(function () {

  /* Open/Close account div in Settings page */
  jQuery(".tkf-account-openfull-icon").on("click", function() {
    var tkf_account_row = jQuery(this).parents('.tkf-account-row');
    if( tkf_account_row.find(".tkf-account-row-content").hasClass("tkf-hidden") ) {
      tkf_account_row.find(".tkf-account-row-content").removeClass("tkf-hidden");
      tkf_account_row.find(".tkf-account-openfull-icon i").removeClass("dashicons-arrow-down-alt2").addClass("dashicons-arrow-up-alt2");
    } else {
      tkf_account_row.find(".tkf-account-row-content").addClass("tkf-hidden");
      tkf_account_row.find(".tkf-account-openfull-icon i").removeClass("dashicons-arrow-up-alt2").addClass("dashicons-arrow-down-alt2");
    }
  });


});

/**
 * Redirect to core page for login to TikTok.
 *
 * @param url string
*/
function login_tiktok( url ) {
  window.location.href = url;
}

/**
 * Ajax action to remove acount.
 *
 * @param open_id string
 * @param user_id integer
 */
function tkf_account_remove( open_id, user_id ) {
  jQuery(".tkf-message").removeClass("tkf-message-success").removeClass("tkf-message-error").addClass("tkf-hidden");
  jQuery.ajax( {
    url: ajaxurl,
    type: "POST",
    data: {
      action: "settings_tkf",
      task: "tkf_account_remove",
      open_id: open_id,
      tkf_ajax_nonce: tkf_obj.tkf_ajax_nonce,
    },
    success: function ( result ) {
      if( result['success'] ) {
        jQuery(".tkf-account-row[data-user-id='" + user_id + "']").remove();
        jQuery(".tkf-message").text(tkf_obj.account_remove_success);
        jQuery(".tkf-message").addClass("tkf-message-success");
      } else {
        jQuery(".tkf-message").text(tkf_obj.something_wrong);
        jQuery(".tkf-message").addClass("tkf-message-error");
      }
    },
    error: function ( xhr ) {
      jQuery(".tkf-message").text(tkf_obj.something_wrong);
      jQuery(".tkf-message").addClass("tkf-message-error");
    },
  });

}

/**
 * Ajax action to refresh token.
 *
 * @param open_id string
 * @param user_id integer
*/
function tkf_refresh_token( open_id, user_id ) {
  jQuery(".tkf-refresh-token-button").text('Refreshing...').addClass('tkf_loading');
  var current_account = jQuery(".tkf-account-row[data-user-id='" + user_id + "']");
  current_account.find(".tkf-message").remove();
  jQuery.ajax( {
    url: ajaxurl,
    type: "POST",
    data: {
      action: "settings_tkf",
      task: "tkf_refresh_token",
      open_id: open_id,
      tkf_ajax_nonce:  tkf_obj.tkf_ajax_nonce,
    },
    success: function ( result ) {
      var e = jQuery('<p class="tkf-message"></p>');
      if( result['success'] ) {
          current_account.find(".tkf-account-access-token-input").val(result['data']['access_token']);
          e.addClass("tkf-message-success");
          e.text(tkf_obj.refresh_success);
      } else {
          e.addClass("tkf-message-error");
          e.text(tkf_obj.something_wrong);
      }
      current_account.find(".tkf-refresh-token-button").after(e);
    },
    error: function ( xhr ) {
      var e = jQuery('<p></p>');
      e.text(tkf_obj.something_wrong);
      current_account.find(".tkf-refresh-token-button").after(e);
    },
    complete: function () {
      jQuery(".tkf-refresh-token-button").text('Refresh').removeClass('tkf_loading');
      setTimeout(() => { current_account.find(".tkf-message").remove(); }, 3000);

    }
  });

}