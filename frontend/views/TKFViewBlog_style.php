<?php
class TKFViewBlog_style extends TKFViewSite {

  public function display($params = array(), $tkf = 0, $ajax = FALSE) {
    $image_rows = $params['medias'];

    $inline_style = $this->inline_styles($tkf, $params['feed_data']);
    echo wp_kses('<style id="tkf-style-' . esc_attr($tkf) . '">' . $inline_style . '</style>', array('style' => array('id' => true)));

    ob_start();
    ?>
    <div id="tkf_blog_style_<?php echo esc_attr($tkf) ?>" class="tkf-container-<?php echo esc_attr($tkf); ?> blog_style_images_conteiner_<?php echo esc_attr($tkf); ?> tkf-container">
      <div class="blog_style_images_<?php echo esc_attr($tkf); ?>" id="blog_style_images_<?php echo esc_attr($tkf); ?>" >
        <?php
        foreach ($image_rows as $image_row) {

          $href = "";
          if( $params['feed_data']['action_on_click'] == 'redirect_tiktok' ) {
            $href = 'href="'.esc_url($image_row['embed_link']).'"';
          }

          $tkf_thumb_url = $image_row['cover_image_url'];
          ?>
          <div class="blog_style_image_buttons_conteiner blog_style_image_buttons_conteiner_<?php echo esc_attr($tkf); ?>">
            <div class="blog_style_image_buttons_<?php echo esc_attr($tkf);?>">
              <div class="tkf_blog_style_image tkf_blog_style_image_<?php echo esc_attr($tkf); ?>" >
                <a class="tkf-a" <?php echo $href; ?> target="_blank">
                  <img class="tkf_blog_style_img_<?php echo esc_attr($tkf); ?>"
                       src="<?php echo esc_url($tkf_thumb_url); ?>"
                       data-src="<?php echo esc_url($tkf_thumb_url); ?>"
                       alt="<?php echo esc_attr($image_row['title']); ?>"
                       title="<?php echo esc_attr($image_row['title']); ?>" />
                </a>
                <?php if ( $params['feed_data']['video_icon'] ) { ?>
                  <div class="tkf_play"></div>
                  <div class="tkf_logo"></div>
                <?php }
              if( $params['feed_data']['user_avatar'] ||
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
              <?php if ( $params['feed_data']['video_title'] || $params['feed_data']['video_description'] ) { ?>
              <?php } ?>

            </div>


          </div>
          <?php
        }
        ?>
      </div>
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
		.tkf-container {
				justify-content: center;
		}
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .blog_style_images_conteiner_<?php echo esc_attr($tkf); ?>{
      background-color: rgba(0, 0, 0, 0);
      text-align: center;
      width: 100%;
      position: relative;
    }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .blog_style_images_<?php echo esc_attr($tkf); ?> {
      display: inline-block;
      -moz-box-sizing: border-box;
      box-sizing: border-box;
      font-size: 0;
      text-align: center;
      max-width: 100%;
		  width: <?php echo esc_html($params['blog_image_width']); ?>px;
    }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .blog_style_image_buttons_conteiner_<?php echo esc_attr($tkf); ?> {
		  text-align: center;
      margin-bottom:20px;
    }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .blog_style_image_buttons_<?php echo esc_attr($tkf); ?> {
		  text-align: center;
     }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf_blog_style_image_<?php echo esc_attr($tkf); ?> {
        background-color: #ffffff;
        text-align: center;
        vertical-align: middle;
        margin: 5px;
        padding: 5px;
        border-radius: 5px;
        position: relative;
    }
    #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf_blog_style_img_<?php echo esc_attr($tkf); ?> {
      padding: 0 !important;
      max-width: 100% !important;
      height: inherit !important;
      width: 100%;
    }
    @media only screen and (max-width : 320px) {
      #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf_blog_style_image_contain_<?php echo esc_attr($tkf); ?>{
				position: absolute;
				text-align: center;
				vertical-align: middle;
				width: 100%;
				height: 100%;
				cursor: pointer;
      }
      #tkf_container1_<?php echo esc_attr($tkf); ?> #tkf_container2_<?php echo esc_attr($tkf); ?> .tkf_gal_title_<?php echo esc_attr($tkf); ?> {
        background-color: rgba(0, 0, 0, 0);
        color: #000000;
        display: block;
        font-family: inherit;
        font-size: 13px;
        font-weight: 400;
        padding: 5px;
        text-align: center;
      }
    <?php
    return ob_get_clean();
  }
}
