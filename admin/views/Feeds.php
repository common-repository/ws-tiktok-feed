<?php

class FeedsView_tkf {

  public function __construct() {
    wp_enqueue_style(TKF()->prefix . '_feeds');
    wp_enqueue_script(TKF()->prefix . '_feeds');
  }

  public function display( $params = array() ) {
  ?>
    <div class="wrap">
      <div class="tkf-page-title tkf-header">
        <div class="tkf-heading-inline"><?php _e('Feeds', 'tkf'); ?></div>
        <a href="<?php echo esc_url(get_admin_url().'admin.php?page=feeds_tkf&task=edit'); ?>" class="add-new-h2"><?php _e('Add new', 'tkf'); ?></a>
      </div>

      <table class="wp-list-table widefat fixed pages media">
        <thead>
          <tr>
              <th class="column-primary table_large_col manage-column column-avatar">
                  <span><?php _e('Avatar', 'tkf'); ?></span>
              </th>
              <th class="column-primary table_large_col manage-column column-title">
                  <span><?php _e('Title', 'tkf'); ?></span>
              </th>
              <th class="table_big_col"><?php _e('Shortcode', 'tkf'); ?></th>
          </tr>
        </thead>
        <tbody id="tbody_arr">
        <?php foreach ($params as $param) {
        $display_name = isset($param['display_nmae']) ? $param['display_nmae'] : __('Unknown', 'tkf');
        $avatar = !empty($param['avatar']) ? $param['avatar'] : TKF()->plugin_url.'/assets/images/empty_avatar.png';
          ?>
        <tr id="tr_11">
          <td>
            <span class="media-icon image-icon">
              <img src="<?php echo esc_url($avatar); ?>" title="<?php echo esc_attr($display_name); ?>" style="border: 1px solid #CCCCCC; max-width: 70px; max-height: 50px;" src="#">
            </span>
          </td>
          <td class="column-primary column-title" data-colname="Name">
            <strong>
              <a href="?page=feeds_tkf&task=edit&feed_id=<?php echo intval($param['id']); ?>" title="Edit">
                <?php echo esc_html($param['feed_title']); ?>
              </a>
            </strong>
            <div class="row-actions">
              <span>
                <a href="?page=feeds_tkf&task=edit&feed_id=<?php echo intval($param['id']); ?>" title="Edit"><?php _e('Edit', 'tkf'); ?></a>
                |
              </span>
              <span class="trash">
                <a onclick="if (!confirm('Do you want to delete selected items?')){return false;}" href="?page=feeds_tkf&task=tkf_delete&current_id=<?php echo intval($param['id']); ?>"><?php _e('Delete', 'tkf'); ?></a>
              </span>
            </div>
          </td>
          <td class="table_big_col" data-colname="Shortcode">
            <input class="tkf-shortcode-input" type="text" value='[tkf_feed id="<?php echo intval($param['id']); ?>"]' onclick="wdi_spider_select_value(this)" size="12" readonly="readonly" style="padding-left: 1px; padding-right: 1px; text-align: center;">
          </td>
        </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  <?php
  }

  public function edit( $params ) {
  ?>
    <div class="wrap">
      <form method="post" id="tkf_save_feed">
        <input type="hidden" name="task" value="tkf_save_feed">
        <input type="hidden" name="action" value="feeds_tkf">
        <input type="hidden" name="feed_id" value="<?php echo intval($params['feed_id']); ?>">
        <?php $nonce = wp_create_nonce( 'tkf-admin-nonce' ); ?>
        <input type="hidden" name="tkf_ajax_nonce" value="<?php echo esc_attr($nonce) ?>" />
        <div class="tkf-page-header">
          <div class="tkf-heading-inline"><?php _e('Feed Title', 'tkf'); ?></div>
          <input id="tkf_feed_name" class="tkf_title_input" name="tkf_feed_title" type="text" value="<?php echo esc_attr($params['feed_title']); ?>">
          <div class="tkf-empty"></div>
          <div class="tkf_buttons">
            <div id="tkf_save_feed_apply" class="button button-primary">
              <?php if($params['feed_id'] == 0){
                _e('Publish', 'tkf');
              } else {
                _e('Update', 'tkf');
              } ?>
            </div>
            <a href="<?php echo esc_url($params['preview_link']); ?>" target="_blank" class="button preview-button button-large"><?php _e('Preview', 'tkf'); ?></a>
          </div>
        </div>

        <?php $this->layout_section( $params ); ?>
      </form>
    </div>
  <?php
  }

  public function layout_section( $params ) {
  ?>
    <div id="tkf_layout_section" class="tkf_layout_section">
      <h3 data-section_name="tkf_layout_section" class="tkf_display_content"><?php _e('Select Account and View Type', 'tkf'); ?></h3>
      <div class="tkf_account_view_section">
        <div class="tkf_account_section">
          <label><?php _e('TikTok Accounts', 'tkf'); ?></label>
          <select name="feed_user_account" class="tkf_feed_user_account_select">
            <?php foreach ($params['accounts'] as $account ) { ?>
              <option value="<?php echo intval($account['id']); ?>" <?php if($params['user_id'] == $account['id']) echo 'selected'; ?>><?php echo esc_html($account['display_name']); ?></option>
            <?php } ?>
          </select>
        </div>
        <div data-display="table" class="tkf_display_type_container tkf_feed_section_content">
        <div class="tkf_display_type" tab="feed_settings">
          <div>
            <input type="radio" id="thumbnails" name="feed_type" value="thumbnails" <?php if($params['feed_type'] == 'thumbnails' || empty($params['feed_type'])) { echo 'checked'; } ?>>
            <label for="thumbnails"><?php _e('Thumbnails', 'tkf'); ?></label>
          </div>
          <label for="thumbnails">
            <img src="<?php echo esc_url(TKF()->plugin_url . '/assets/images/thumb.jpg'); ?>">
          </label>
        </div>

        <div class="tkf_display_type " tab="feed_settings">
          <div>
            <input type="radio" id="blog_style" name="feed_type" value="blog_style" <?php if( $params['feed_type'] == 'blog_style' ) { echo 'checked'; } ?>>
            <label for="blog_style"><?php _e('Blog Style', 'tkf'); ?></label>
          </div>
          <label for="blog_style">
            <img src="<?php echo esc_url(TKF()->plugin_url . '/assets/images/blog_style.jpg'); ?>">
          </label>
        </div>
        <?php if ( !TKF()->is_pro ) { ?>
        <div class="tkf_pro_notice">
        <?php } ?>
        <div class="tkf_display_type " tab="feed_settings">
          <div>
            <input type="radio" <?php echo !TKF()->is_pro ? 'disabled':'' ?> id="masonry" name="<?php echo !TKF()->is_pro ? 'feed_type':'' ?>" value="<?php echo !TKF()->is_pro ? 'masonry':'' ?>" <?php if($params['feed_type'] == 'masonry') { echo 'checked'; } ?>>
            <label for="masonry"><?php _e('Masonry', 'tkf'); ?></label>
          </div>
          <label for="masonry">
            <img src="<?php echo esc_url(TKF()->plugin_url . '/assets/images/masonry.jpg'); ?>">
          </label>
        </div>
        <?php if ( !TKF()->is_pro ) { ?>
        </div>
        <?php } ?>

        <?php if ( !TKF()->is_pro ) { ?>
        <div class="tkf_pro_notice">
        <?php } ?>
        <div class="tkf_display_type" tab="feed_settings">
          <div>
            <input type="radio" <?php echo !TKF()->is_pro ? 'disabled':'' ?> id="slideshow" name="<?php echo !TKF()->is_pro ? 'feed_type':'' ?>" value="<?php echo !TKF()->is_pro ? 'slideshow':'' ?>" <?php if($params['feed_type'] == 'slideshow') { echo 'checked'; } ?>>
            <label for="slideshow"><?php _e('Slideshow', 'tkf'); ?></label>
          </div>
          <label for="image_browser">
            <img src="<?php echo esc_url(TKF()->plugin_url . '/assets/images/slideshow.jpg'); ?>">
          </label>
        </div>
        <?php if ( !TKF()->is_pro ) { ?>
        </div>
        <?php } ?>

      </div>
      </div>
    </div>

    <div id="tkf_settings_section" class="tkf_feed_section">
      <h3 data-section_name="tkf_advanced_section" class="tkf_display_content"><?php _e('View Settings', 'tkf'); ?></h3>
      <div class="tkf_feed_section_content tkf_feed_section_content_advanced">
        <div>
          <?php $this->slideshow_settings($params); ?>
          <div class="tkf_global_setting tkf_thumb_style_setting <?php echo ($params['feed_type'] != 'thumbnails') ? 'tkf-hidden' : '' ?>">
            <label><?php _e('Thumbnail dimensions', 'tkf'); ?></label>
            <div class="tkf_feed_thumb_dimansions">
              <input type="number" name="thumb_width" class="tkf_feed_user_account_select" value="<?php echo isset($params['thumb_width']) ? intval($params['thumb_width']) : 350; ?>">
                <span>x</span>
              <input type="number" name="thumb_height" class="tkf_feed_user_account_select" value="<?php echo isset($params['thumb_height']) ? intval($params['thumb_height']) : 350; ?>">
                <span>px</span>
            </div>
            <p class="description"><?php _e('The default dimensions of thumbnails which will display on published galleries.', 'tkf'); ?></p>
          </div>
          <div class="tkf_global_setting tkf_masonry_style_setting <?php echo ($params['feed_type'] != 'masonry') ? 'tkf-hidden' : '' ?>">
            <label><?php _e('Thumbnail width', 'tkf'); ?></label>
              <input type="number" name="masonry_thumb_width" class="tkf_feed_user_account_select" value="<?php echo isset($params['masonry_thumb_width']) ? intval($params['masonry_thumb_width']) : 300; ?>">
                <span>px</span>
            <p class="description"><?php _e('The default size of thumbnails which will display feed.', 'tkf'); ?></p>
          </div>

          <div class="tkf_global_setting tkf_blog_style_setting <?php echo ($params['feed_type'] != 'blog_style') ? 'tkf-hidden' : '' ?>">
            <label><?php _e('Image Width', 'tkf'); ?></label>
            <div class="tkf_feed_thumb_dimansions">
              <input type="number" name="blog_image_width" class="tkf_feed_user_account_select" value="<?php echo isset($params['blog_image_width']) ? intval($params['blog_image_width']) : 500; ?>">
            </div>
            <p class="description"><?php _e('Specify the default width of images in Blog Style view.', 'tkf'); ?></p>
          </div>

          <div class="tkf_global_setting tkf_thumb_style_setting <?php echo ($params['feed_type'] != 'thumbnails') ? 'tkf-hidden' : '' ?>">
            <label><?php _e('Number of Columns', 'tkf'); ?></label>
            <input type="number" name="columns_number" class="tkf_feed_user_account_select" value="<?php echo esc_attr($params['columns_number']); ?>">
            <p class="description"><?php _e('Set the maximum number of image columns in galleries.', 'tkf'); ?></p>
          </div>

          <div class="tkf_global_setting tkf_thumb_style_setting tkf_blog_style_setting tkf_masonry_style_setting <?php echo ($params['feed_type'] == 'slideshow') ? 'tkf-hidden' : '' ?>">
            <label><?php _e('Pagination Type', 'tkf'); ?></label>
            <div class="tkf_settings_pagination">
              <input type="radio" name="pagination_type" value="none" <?php if($params['pagination_type'] == 'none') echo 'checked'; ?>>
              <span class="tkf_mini_label"><?php _e('None', 'tkf'); ?></span>
              <input type="radio" name="pagination_type" value="pagination" <?php if($params['pagination_type'] == 'pagination') echo 'checked'; ?>>
              <span class="tkf_mini_label"><?php _e('Simple', 'tkf'); ?></span>
              <?php if ( !TKF()->is_pro ) { ?>
              <div class="tkf_pro_notice">
              <?php } ?>
              <input type="radio" <?php echo !TKF()->is_pro ? 'disabled':'' ?> name="pagination_type" value="<?php echo !TKF()->is_pro ? '':'loadmore' ?>" <?php if($params['pagination_type'] == 'loadmore') echo 'checked'; ?>>
              <span class="tkf_mini_label"><?php _e('Load more', 'tkf'); ?></span>
              <?php if ( !TKF()->is_pro ) { ?>
              </div>
              <?php } ?>
            </div>
            <p class="description"><?php _e('Activating pagination type.', 'tkf'); ?></p>


            <label><?php _e('Number of Medias Per Page', 'tkf'); ?></label>
            <input type="number" name="per_page_number" class="tkf_feed_user_account_select" value="<?php echo intval($params['per_page_number']); ?>">
            <p class="description"><?php _e('Specify the number of images to display per page on feed.', 'tkf'); ?></p>
          </div>
          <label><?php _e('Order By', 'tkf'); ?></label>
          <div class="tkf_settings_orderby">
            <select name="sort_media_by" class="tkf_feed_user_account_select">
              <option value="create_time" <?php if($params['sort_media_by'] == 'create_time') echo 'selected'; ?>><?php _e('Date', 'tkf'); ?></option>
              <option value="like_count" <?php if($params['sort_media_by'] == 'like_count') echo 'selected'; ?>><?php _e('Likes', 'tkf'); ?></option>
              <option value="comment_count" <?php if($params['sort_media_by'] == 'comment_count') echo 'selected'; ?>><?php _e('Comments', 'tkf'); ?></option>
              <option value="share_count" <?php if($params['sort_media_by'] == 'share_count') echo 'selected'; ?>><?php _e('Shares', 'tkf'); ?></option>
            </select>
            <select name="sort_media_order" class="tkf_feed_user_account_select">
              <option value="asc" <?php if($params['sort_media_order'] == 'asc') echo 'selected'; ?>><?php _e('Ascending', 'tkf'); ?></option>
              <option value="desc" <?php if($params['sort_media_order'] == 'desc') echo 'selected'; ?>><?php _e('Descending', 'tkf'); ?></option>
            </select>
          </div>
          <p class="description"><?php _e('Select the parameter and order direction to sort the feed medias with. E.g. Date and Ascending.', 'tkf'); ?></p>


        </div>
        <div>
          <label><?php _e('Action OnClick', 'tkf'); ?></label>
          <select name="action_on_click" class="tkf_feed_user_account_select">
            <option value="nothing" <?php if($params['action_on_click'] == 'nothing') echo 'selected'; ?>><?php _e('Do Nothing', 'tkf'); ?></option>
            <option value="redirect_tiktok" <?php if($params['action_on_click'] == 'redirect_tiktok') echo 'selected'; ?>><?php _e('Redirect to TikTok', 'tkf'); ?></option>
          </select>
          <p class="description"><?php _e('Specify the action on media thumbnail click.', 'tkf'); ?></p>

          <label><?php _e('Show User Avatar', 'tkf'); ?></label>
          <input type="radio" value="1" name="user_avatar" value="1" <?php echo !empty($params['user_avatar']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="user_avatar" value="0" <?php echo empty($params['user_avatar']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show user TikTok avatar and Display name.', 'tkf'); ?></p>

          <label><?php _e('Show Video Title', 'tkf'); ?></label>
          <input type="radio" value="1" name="video_title" <?php echo ($params['video_title'] == 1) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="video_title" <?php echo ($params['video_title'] == 0) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show video title if it is available in the TikTok.', 'tkf'); ?></p>

          <div class="tkf_global_setting tkf_blog_style_setting">
            <label><?php _e('Show Video Description', 'tkf'); ?></label>
            <input type="radio" value="1" name="video_description" <?php echo !empty($params['video_description']) ? 'checked' : ''; ?>>
            <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
            <input type="radio" value="0" name="video_description" <?php echo empty($params['video_description']) ? 'checked' : ''; ?>>
            <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
            <p class="description"><?php _e('Show video description if it is available in the TikTok.', 'tkf'); ?></p>
          </div>

          <label><?php _e('Show Video Duration', 'tkf'); ?></label>
          <input type="radio" value="1" name="video_duration" <?php echo !empty($params['video_duration']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="video_duration" <?php echo empty($params['video_duration']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show video duration on hover.', 'tkf'); ?></p>
        </div>
        <div>
          <label><?php _e('Show Likes Count', 'tkf'); ?></label>
          <input type="radio" value="1" name="like_count" <?php echo !empty($params['like_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="like_count" <?php echo empty($params['like_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show video likes count on hover.', 'tkf'); ?></p>

          <label><?php _e('Show Comments Count', 'tkf'); ?></label>
          <input type="radio" value="1" name="comment_count" <?php echo !empty($params['comment_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="comment_count" <?php echo empty($params['comment_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show video comments count on hover.', 'tkf'); ?></p>

          <label><?php _e('Show Shares Count', 'tkf'); ?></label>
          <input type="radio" value="1" name="share_count" <?php echo !empty($params['share_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="share_count" <?php echo empty($params['share_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show video shares count on hover.', 'tkf'); ?></p>

          <label><?php _e('Show Views Count', 'tkf'); ?></label>
          <input type="radio" value="1" name="view_count" <?php echo !empty($params['view_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="view_count" <?php echo empty($params['view_count']) ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show video views count on hover.', 'tkf'); ?></p>

          <label><?php _e('Show Play Icon', 'tkf'); ?></label>
          <input type="radio" value="1" name="video_icon" <?php echo $params['video_icon'] ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('Yes', 'tkf'); ?></span>
          <input type="radio" value="0" name="video_icon" <?php echo !$params['video_icon'] ? 'checked' : ''; ?>>
          <span class="tkf_mini_label"><?php _e('No', 'tkf'); ?></span>
          <p class="description"><?php _e('Show video icon on thumbnails.', 'tkf'); ?></p>
        </div>
      </div>
    </div>

    <?php
  }

  function slideshow_settings( $params ) {
    ?>
    <div class="tkf_global_setting tkf_slideshow_style_setting <?php echo ($params['feed_type'] != 'slideshow') ? 'tkf-hidden' : '' ?>">
      <label><?php _e('Slideshow dimensions', 'tkf'); ?></label>
      <div class="tkf_feed_thumb_dimansions">
        <input type="number" name="slideshow_width" class="tkf_feed_user_account_select" value="<?php echo isset($params['slideshow_width']) ? intval($params['slideshow_width']) : 700; ?>">
        <span>x</span>
        <input type="number" name="slideshow_height" class="tkf_feed_user_account_select" value="<?php echo isset($params['slideshow_height']) ? intval($params['slideshow_height']) : 400; ?>">
        <span>px</span>
      </div>
      <p class="description"><?php _e('Set the default dimensions of your slideshow galleries.', 'tkf'); ?></p>


      <label><?php _e('Slideshow filmstrip size', 'tkf'); ?></label>
      <input type="number" name="slideshow_filmstrip_size" class="tkf_feed_user_account_select" value="<?php echo isset($params['slideshow_filmstrip_size']) ? intval($params['slideshow_filmstrip_size']) : 90; ?>">
      <p class="description"><?php _e('Set the size of your filmstrip. If the filmstrip is horizontal, this indicates its height, whereas for vertical filmstrips it sets the width.', 'tkf'); ?></p>

      <label><?php _e('Effect duration', 'tkf'); ?></label>
      <input type="number" name="slideshow_effect_dur" class="tkf_feed_user_account_select" value="<?php echo isset($params['slideshow_effect_dur']) ? floatval($params['slideshow_effect_dur']) : 0.1; ?>">
      <p class="description"><?php _e('Set the duration of your slideshow animation effect.', 'tkf'); ?></p>
    </div>
    <?php
  }

}