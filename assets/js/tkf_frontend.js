jQuery(document).ready(function () {

  jQuery(document).on("click", ".tkf_pagination li", function () {
    tkf_page_change(jQuery(this));
  });

  jQuery(document).on("click", ".tkf-layout", function (e) {
    e.preventDefault();
    jQuery(".tkf-embed-popup, .tkf-layout").addClass("tkf-hidden");
  });
});

function tkf_page_change(that) {
  var tkf_feed_id = that.closest('.tkf_container').data('feed-id');
  var tkf = that.closest('.tkf_container').data('tkf');

  var main_cont = jQuery(document).find("#tkf_container1_"+tkf);
  var tkf_page = parseInt(that.data('page'));
  var pagination_type = main_cont.data("pagination-type");
  if( pagination_type === 'loadmore' ) {
    main_cont.find(".tkf_pagination li").text(tkf_obj.loading);
  }
  main_cont.find('.tkf_ajax_loading').removeClass('tkf-hidden');
  jQuery('#tkf_ajax_loading_' + tkf).removeClass('tkf-hidden');
  jQuery.ajax( {
    url: tkf_obj.ajaxurl,
    type: "POST",
    dataType: "html",
    data: {
      action: "frontend_tkf",
      tkf_page: tkf_page,
      tkf_feed_id: tkf_feed_id,
      tkf_ajax_action: 'tkf_page_change',
      tkf_ajax_nonce:  tkf_obj.tkf_ajax_nonce,
    },
    success: function ( result ) {
      // var cont = jQuery('.tkf-container-'+tkf).html(result);
      var cont = '';

      if( pagination_type === 'pagination' ) {
          cont = jQuery(jQuery.parseHTML(result)).find("#tkf_container2_0").html();
          main_cont.find('#tkf_container2_' + tkf).empty();
          main_cont.find("#tkf_container2_" + tkf).html(cont);
          main_cont.find(".tkf_currentpage").addClass('tkf_page').removeClass('tkf_currentpage');
          main_cont.find(".tkf_page[data-page='"+tkf_page+"']").addClass('tkf_currentpage');
      } else if( pagination_type === 'loadmore' ) {
          cont = jQuery(jQuery.parseHTML(result)).find(".tkf-container-"+tkf).html();
          jQuery(document).find(".tkf-container-"+tkf+" .tkf-empty-item").remove();
          jQuery(document).find(".tkf-container-"+tkf).append(cont);
          var loaded_count = jQuery(document).find('.tkf-a').length;
          if( main_cont.find(".tkf_pagination").data('total') === loaded_count ) {
              main_cont.find(".tkf_pagination").remove();
          } else {
              main_cont.find(".tkf_pagination li").remove();
              var e = jQuery('<li></li>');
              e.attr("data-page", parseInt(tkf_page + 1));
              e.text(tkf_obj.loadmore);
              main_cont.find(".tkf_pagination").append(e);
          }
      }
      tkf_main_ready(main_cont);
    },
    error: function ( xhr ) {
    },
    complete: function () {
      main_cont.find('.tkf_ajax_loading').addClass('tkf-hidden');
    }
  });

}

/**
 * @param {object} container feed main container.
 */
function tkf_main_ready(container) {
  var tkf = container.data('tkf');

  /* If there is error (empty gallery).*/
  if ( container.find(".wd_error").length > 0 ) {
    tkf_container_loaded(tkf);
  }

  var last_container = container.find(".tkf-container");

  /* Go back from gallery to album.*/
  if ( last_container.hasClass('tkf-thumbnails') ) {
    var gallery_type = 'thumbnails';
  }
  else {
    var gallery_type = container.data("gallery-type");
  }
  switch ( gallery_type) {
    case "thumbnails":
    case "masonry":
      tkf_all_thumnails_loaded(last_container);
      break;
    case "slideshow":
      tkf_slideshow_ready(tkf);
      break;
  }
}


function tkf_all_thumnails_loaded(that) {
  var thumbnails_count = 0;
  var thumbnails_loaded = jQuery(that).find("img").length;
  if (0 == thumbnails_loaded) {
    tkf_all_thumbnails_loaded_callback(that);
  }
  else {
    jQuery( that ).find( "img" ).each( function () {
      var fakeSrc = jQuery( this ).attr( "src" );
      jQuery( "<img/>" ).attr( "src", fakeSrc ).on( "load error", function () {
        if ( ++thumbnails_count >= thumbnails_loaded ) {
          tkf_all_thumbnails_loaded_callback( that );
        }
      });
    });
  }

  /* If there is error (empty gallery, "No Images found."). Show Load Icon before load gallery. */
  jQuery(".tkf_container").each(function () {
    var tkf = jQuery(this).data('tkf');
    if ( jQuery(this).find(".wd_error").length > 0 ) {
      tkf_container_loaded(tkf);
    }
  });
  return thumbnails_loaded == 0;
}

function tkf_all_thumbnails_loaded_callback(that) {
  if (jQuery(that).hasClass('tkf-thumbnails') && !jQuery(that).hasClass('tkf-masonry-thumbnails')) {
    tkf_thumbnail( that );
  }
  if (jQuery(that).hasClass('tkf-masonry-thumbnails')) {
    tkf_masonry( that );
  }
}

function tkf_masonry(that) {
  return false;
}


function tkf_thumbnail(that) {
  var container_width = jQuery(that).width();
  var thumb_width = jQuery(that).data("thumbnail-width");
  var max_count = jQuery(that).data("max-count");
  var column_count = parseInt(container_width / thumb_width) + 1;
  if (column_count > max_count) {
    column_count = max_count;
  }
  /*var flex = 1 / column_count;*/
  var min_width = 100 / column_count;
  var tkf_item = jQuery(that).find(".tkf-item");
  tkf_item.css({
    /*flexGrow: flex,*/
    width: min_width + "%"
  });
  jQuery(that).children(".tkf-item").each(function () {
    var t = this;
/*
    var source = jQuery(this).find("img");
    source.attr({
      src: source.attr('data-src')
    }).removeAttr('data-src');
*/
    var image = jQuery(this).find(".tkf_thumb_img");
    var item2 = jQuery(this).find(".tkf-item2");
    var item1 = jQuery(this).find(".tkf-item1");
    var container_width = item2.width() > 0 ? item2.width() : item1.width();
    var container_height = item2.height() > 0 ? item2.height() : item1.height();
    var image_width = image.data('width');
    var image_height = image.data('height');
    if(image_width == '' || image_height == '' || typeof image_width === 'undefined' || typeof image_height === 'undefined') {
      image_width = image.width();
      image_height = image.height();
    }
    var scale = image_width/image_height;
    image.removeAttr("style");
    if ( (container_width / container_height) > scale ) {
      if ( container_width > image_width ) {
        image.css({width: "100%", height: container_width/scale});
      }
      else {
        /* Math.ceil image width in some cases less from the container with due to rounded */
        image.css({maxWidth: "100%", height: Math.ceil(container_width/scale)});
      }
      image_width = container_width;
      image_height = container_width/scale;
    }
    else {
      if ( container_height >= image.height() ) {
        image.css({height : "100%", width : container_height*scale, maxWidth : 'initial'});
      }
      else {
        image.css({maxHeight: "100%", width: container_height*scale, maxWidth:'initial'});
      }
      image_height = container_height;
      image_width = container_height*scale;
    }

    jQuery(this).find(".tkf-item2").css({
      marginLeft: (container_width - image_width) / 2,
      marginTop: (container_height - image_height) / 2
    });
  });
  tkf_container_loaded(jQuery(that).data('tkf'));
}

function tkf_container_loaded(tkf) {
  jQuery('#tkf_ajax_loading_' + tkf).addClass('tkf-hidden');
  jQuery(".tkf_container img").removeAttr('width').removeAttr('height');
}






