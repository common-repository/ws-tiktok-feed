<?php
class TKFViewThumbnails extends TKFViewSite {

  public function display( $params = array(), $tkf = 0 ) {
    $image_rows = $params['medias'];
    $inline_style = $this->inline_styles($tkf, $params);
    echo wp_kses('<style id="tkf-style-' . esc_attr($tkf) . '">' . $inline_style . '</style>', array('style' => array('id' => true)));
    ob_start();
    ?>
    <div data-max-count="<?php echo esc_attr($params['feed_data']['columns_number']); ?>"
         data-thumbnail-width="<?php echo esc_attr($params['feed_data']['thumb_width']); ?>"
         data-tkf="<?php echo esc_attr($tkf); ?>"
         data-feed-id="<?php echo esc_attr($params['feed_data']['id']); ?>"
         id="tkf_<?php echo esc_attr($params['feed_data']['feed_type'].'_'.$tkf) ?>"
         class="tkf-container-<?php echo esc_attr($tkf); ?> tkf-thumbnails tkf-standard-thumbnails tkf-container tkf-border-box">
      <?php
      foreach ($image_rows as $image_row) {
	  		$tkf_thumb_url = $image_row['cover_image_url'];

        $href = "";
        if( $params['feed_data']['action_on_click'] == 'redirect_tiktok' ) {
          $href = 'href="'.esc_url($image_row['embed_link']).'"';
        }

        $action_popup_class = "";
        if ( $params['feed_data']['action_on_click'] == 'open_popup' ) {
          $action_popup_class = " tkf-open-popup";
        }
        $image_thumb_width = $image_row['width'];
        $image_thumb_height = $image_row['height'];
        ?>
        <div class="tkf-item">
        <a class="tkf-a<?php echo esc_attr($action_popup_class); ?>" <?php echo $href; ?> target="_blank"
        data-embed-link="<?php echo esc_url($image_row['embed_link']) ?>">
        <div class="tkf-item0">
          <div class="tkf-item1">
            <?php if ( $params['feed_data']['video_icon'] ) { ?>
            <div class="tkf_play"></div>
            <div class="tkf_logo"></div>
            <?php } ?>
            <div class="tkf-item2">
              <img class="skip-lazy tkf_standart_thumb_img_<?php echo esc_attr($tkf); ?> tkf_thumb_img"
                   data-id="<?php echo esc_attr($image_row['id']); ?>"
                   data-width="<?php echo esc_attr($image_thumb_width); ?>"
                   data-height="<?php echo esc_attr($image_thumb_height); ?>"
                   data-src="<?php echo esc_url($tkf_thumb_url); ?>"
                   src="<?php echo esc_url($tkf_thumb_url); ?>"
                   title="<?php echo esc_attr($image_row['title']); ?>" />
            </div>
          </div>
          <?php if( $params['feed_data']['user_avatar'] ||
                    $params['feed_data']['video_title'] ||
                    $params['feed_data']['video_description'] ||
                    $params['feed_data']['like_count'] ||
                    $params['feed_data']['comment_count'] ||
                    $params['feed_data']['share_count'] ||
                    $params['feed_data']['view_count'] ||
                    $params['feed_data']['duration']) { ?>
          <div class="tkf-info-cont">
            <?php if ( $params['feed_data']['user_avatar'] ) { ?>
            <div class="tkf-info-cont-img">
              <img src="<?php echo esc_url($params['feed_data']['avatar']); ?>">
            </div>
            <?php } ?>
            <div class="tkf-info-cont-text">
              <?php if ( $params['feed_data']['video_title'] ) { ?>
              <p class="tkf-info-cont-text-title"><?php echo esc_html($image_row['title']); ?></p>
              <?php } ?>
              <?php if ( $params['feed_data']['video_description'] ) { ?>
              <p class="tkf-info-cont-text-descr"><?php echo esc_html($image_row['video_description']); ?></p>
              <?php } ?>
            </div>
            <?php
            if ( $params['feed_data']['like_count'] || $params['feed_data']['comment_count'] || $params['feed_data']['share_count'] || $params['feed_data']['view_count'] || $params['feed_data']['duration'] ) {
              ?>
              <div class="tkf_icons">
                <?php if($params['feed_data']['comment_count']) { ?>
                  <div class="tkf_comments_count tkf_icons_item"><span><?php echo esc_html($image_row['comment_count']) ?></span></div>
                <?php } ?>
                <?php if($params['feed_data']['like_count']) { ?>
                  <div class="tkf_likes_count tkf_icons_item"><span><?php echo esc_html($image_row['like_count']) ?></span></div>
                <?php } ?>
                <?php if($params['feed_data']['share_count']) { ?>
                  <div class="tkf_shares_count tkf_icons_item"><span><?php echo esc_html($image_row['share_count']) ?></span></div>
                <?php } ?>
                <?php if($params['feed_data']['view_count']) { ?>
                  <div class="tkf_view_count tkf_icons_item"><span><?php echo esc_html($image_row['view_count']) ?></span></div>
                <?php } ?>
                <?php if($params['feed_data']['video_duration']) { ?>
                  <div class="tkf_video_duration tkf_icons_item"><span><?php echo esc_html($image_row['duration']) ?> <?php _e('sec', 'tkf'); ?></span></div>
                <?php } ?>
              </div>
            <?php } ?>

          </div>
          <?php } ?>

        </div>
        </a>
      </div>
      <?php
      }
      ?>
    </div>
    <?php

    $content = ob_get_clean();
    parent::container($params, $tkf, $content);
    if( TKFLibrary::get('tkf_ajax_action') == 'tkf_page_change') {
      echo wp_kses($content, TKFLibrary::two_kses_allowed_html());
      die;
    }
  }

  public function inline_styles($tkf, $params) {
    ob_start();
    ?>
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf-container-<?php echo esc_attr($tkf); ?>.tkf-standard-thumbnails {
      width: <?php echo esc_html($params['feed_data']['columns_number'] * $params['feed_data']['thumb_width']) ?>px;
      justify-content: center;
      margin:0 auto !important;
      margin-right: -5px;
      max-width: calc(100% + 5px);
    }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf-container-<?php echo esc_attr($tkf); ?>.tkf-standard-thumbnails .tkf-item {
      justify-content: flex-start;
      max-width: <?php echo esc_html($params['feed_data']['thumb_width']) ?>px;
    }

    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf-container-<?php echo esc_attr($tkf); ?>.tkf-standard-thumbnails .tkf-item0 {
      padding: 5px;
    }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf-container-<?php echo esc_attr($tkf); ?>.tkf-standard-thumbnails .tkf-item1 img {
      max-height: none;
      max-width: none;
      padding: 0 !important;
    }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf-container-<?php echo esc_attr($tkf); ?>.tkf-standard-thumbnails .tkf-item1 {
    padding-top: <?php echo esc_html($params['feed_data']['thumb_height'] / $params['feed_data']['thumb_width'] * 100); ?>%;
    }

    @media only screen and (min-width: 480px) {
    #bwg_container1_<?php echo esc_attr($tkf); ?> #bwg_container2_<?php echo esc_attr($tkf); ?> .bwg-container-<?php echo esc_attr($tkf); ?>.bwg-standard-thumbnails .bwg-item0 {
    <?php echo 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;'; ?>
    }

    }


    <?php
    return ob_get_clean();
  }
}
