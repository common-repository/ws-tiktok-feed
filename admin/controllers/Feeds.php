<?php

class FeedsController_tkf {

  /**
   * @var $model
   */
  private $model;
  /**
   * @var $view
   */
  private $view;

  private $setting_defaults = array();

  public function __construct() {
    $this->model = new FeedsModel_tkf();
    $this->view = new FeedsView_tkf();
    $this->tkf_set_defaults();
  }

  public function tkf_set_defaults() {
    $this->setting_defaults = array(
      'feed_title' => '',
      'feed_type' => 'thumbnails',
      'user_id' => '',
      'thumb_width' => 250,
      'thumb_height' => 250,
      'blog_image_width' => 500,
      'masonry_thumb_width' => 300,
      'sort_media_by' => 'create_time',
      'sort_media_order' => 'desc',
      'action_on_click' => 'nothing',
      'pagination_type' => 'none',
      'columns_number' => 3,
      'per_page_number' => 10,
      'user_avatar' => 1,
      'video_title' => 1,
      'video_description' => 1,
      'video_duration' => 1,
      'like_count' => 1,
      'comment_count' => 1,
      'share_count' => 1,
      'view_count' => 1,
      'video_icon' => 1,
      'slideshow_width' => 700,
      'slideshow_height' => 400,
      'slideshow_effect_dur' => 0.1,
      'slideshow_filmstrip_size' => 90,
    );
  }

  public function execute() {
    $task = TKFLibrary::get('task');

    if ( $task != 'display' && method_exists($this, $task) ) {
        $this->$task();
    }
    else {
      $this->display();
    }
  }

  public function display() {
    $datas = $this->model->tkf_get_all_feeds();
    $params = array();
    foreach ( $datas as $data ) {
      $feed_settings = json_decode($data['feed_settings'], 1);
      unset($data['feed_settings']);
      $params[] = array_merge($data, $feed_settings);
    }

    $this->view->display( $params );
  }

  public function edit() {
    $feed_id = TKFLibrary::get('feed_id', 0,'intval');
    if ( $feed_id != 0 ) {
        $data = $this->model->tkf_get_feed( $feed_id );
        $params = json_decode($data['feed_settings'], 1);
        $params['user_id'] = $data['user_id'];
    } else {
        $params = $this->setting_defaults;
    }
    $params['feed_id'] = $feed_id;
    $params['accounts'] = $this->model->get_all_accounts();
    $params['preview_link'] = get_option('tkf_preview_permalink');
    $this->view->edit( $params );
  }

  public function tkf_save_feed() {
    $date = new DateTime();
    $timestamp = $date->getTimestamp();
    $user_id = TKFLibrary::get('feed_user_account', 0, 'intval', 'POST');
    $feed_id = TKFLibrary::get('feed_id', 0, 'intval', 'POST');
    if( $user_id == 0 ) {
      wp_send_json_error( array('msg' => '') );
    }
    $save_action = 'update';

    $feed_type = TKFLibrary::get('feed_type', 'thumbnails', 'sanitize_text_field', 'POST');
    if( TKF()->is_pro && $feed_type == 'masonry' || $feed_type == 'slideshow' ) {
      $feed_type = 'thumbnails';
    }
    if( $feed_id == 0 ) {
        $data_settings = array(
          'feed_title' => TKFLibrary::get('tkf_feed_title', 'TikTok Feed', 'sanitize_text_field', 'POST'),
          'feed_type' => sanitize_text_field($feed_type),
          'thumb_width' => TKFLibrary::get('thumb_width', 250, 'intval', 'POST'),
          'thumb_height' => TKFLibrary::get('thumb_height', 250, 'intval', 'POST'),
          'blog_image_width' => TKFLibrary::get('blog_image_width', 500, 'intval', 'POST'),
          'masonry_thumb_width' => TKFLibrary::get('masonry_thumb_width', 300, 'intval', 'POST'),
          'sort_media_by' => TKFLibrary::get('sort_media_by', 'create_time', 'sanitize_text_field', 'POST'),
          'sort_media_order' => TKFLibrary::get('sort_media_order', 'desc', 'sanitize_text_field', 'POST'),
          'action_on_click' => TKFLibrary::get('action_on_click', 'nothing', 'sanitize_text_field', 'POST'),
          'pagination_type' => TKFLibrary::get('pagination_type', 'pagination', 'sanitize_text_field', 'POST'),
          'columns_number' => TKFLibrary::get('columns_number', 3, 'intval', 'POST'),
          'per_page_number' => TKFLibrary::get('per_page_number', 10, 'intval', 'POST'),
          'user_avatar' => TKFLibrary::get('user_avatar', 0, 'intval', 'POST'),
          'video_title' => TKFLibrary::get('video_title', 0, 'intval', 'POST'),
          'video_description' => TKFLibrary::get('video_description', 0, 'intval', 'POST'),
          'video_duration' => TKFLibrary::get('video_duration', 0, 'intval', 'POST'),
          'like_count' => TKFLibrary::get('like_count', 0, 'intval', 'POST'),
          'comment_count' => TKFLibrary::get('comment_count', 0, 'intval', 'POST'),
          'share_count' => TKFLibrary::get('share_count', 0, 'intval', 'POST'),
          'view_count' => TKFLibrary::get('view_count', 0, 'intval', 'POST'),
          'video_icon' => TKFLibrary::get('video_icon', 0, 'intval', 'POST'),
          'slideshow_width' => TKFLibrary::get('slideshow_width', 700, 'intval', 'POST'),
          'slideshow_height' => TKFLibrary::get('slideshow_height', 400, 'intval', 'POST'),
          'slideshow_effect_dur' => TKFLibrary::get('slideshow_effect_dur', 0.1, 'floatval', 'POST'),
          'slideshow_filmstrip_size' => TKFLibrary::get('slideshow_filmstrip_size', 90, 'intval', 'POST'),
        );
        $save_array = array(
          'user_id' => $user_id,
          'feed_settings' => wp_json_encode($data_settings),
          'created_at' => $timestamp,
          'updated_at' => $timestamp,
          'published' => TKFLibrary::get('published', 1, 'intval', 'POST'),
        );
        $format = array('%d', '%s', '%d', '%d', '%d' );
        $insert = $this->model->tkf_insert_feed( $save_array, $format );
        if ( $insert ) {
          $feed_id = $insert;
          $redirect_url = admin_url( 'admin.php?page=feeds_tkf&task=edit&feed_id='.$feed_id );
          $save_action = 'insert';
        }
    } else {
        $current_data = $this->model->tkf_get_feed( $feed_id );
        $feed_settings = json_decode($current_data['feed_settings'], 1);
        unset($current_data['feed_settings']);
        $current_data = array_merge($current_data, $feed_settings);
        $data_settings = array(
          'feed_title' => TKFLibrary::get('tkf_feed_title', $current_data['feed_title'], 'sanitize_text_field', 'POST'),
          'thumb_width' => TKFLibrary::get('thumb_width', $current_data['thumb_width'], 'intval', 'POST'),
          'thumb_height' => TKFLibrary::get('thumb_height', $current_data['thumb_height'], 'intval', 'POST'),
          'blog_image_width' => TKFLibrary::get('blog_image_width', $current_data['blog_image_width'], 'intval', 'POST'),
          'masonry_thumb_width' => TKFLibrary::get('masonry_thumb_width', $current_data['masonry_thumb_width'], 'intval', 'POST'),
          'feed_type' => TKFLibrary::get('feed_type', $current_data['feed_type'], 'sanitize_text_field', 'POST'),
          'sort_media_by' => TKFLibrary::get('sort_media_by', $current_data['sort_media_by'], 'sanitize_text_field', 'POST'),
          'sort_media_order' => TKFLibrary::get('sort_media_order', $current_data['sort_media_order'], 'sanitize_text_field', 'POST'),
          'action_on_click' => TKFLibrary::get('action_on_click', $current_data['action_on_click'], 'sanitize_text_field', 'POST'),
          'pagination_type' => TKFLibrary::get('pagination_type', $current_data['pagination_type'], 'sanitize_text_field', 'POST'),
          'columns_number' => TKFLibrary::get('columns_number', $current_data['columns_number'], 'intval', 'POST'),
          'per_page_number' => TKFLibrary::get('per_page_number', $current_data['per_page_number'], 'intval', 'POST'),
          'user_avatar' => TKFLibrary::get('user_avatar', $current_data['user_avatar'], 'intval', 'POST'),
          'video_title' => TKFLibrary::get('video_title', $current_data['video_title'], 'intval', 'POST'),
          'video_description' => TKFLibrary::get('video_description', $current_data['video_description'], 'intval', 'POST'),
          'video_duration' => TKFLibrary::get('video_duration', $current_data['video_duration'], 'intval', 'POST'),
          'like_count' => TKFLibrary::get('like_count', $current_data['like_count'], 'intval', 'POST'),
          'comment_count' => TKFLibrary::get('comment_count', $current_data['comment_count'], 'intval', 'POST'),
          'share_count' => TKFLibrary::get('share_count', $current_data['share_count'], 'intval', 'POST'),
          'view_count' => TKFLibrary::get('view_count', $current_data['view_count'], 'intval', 'POST'),
          'video_icon' => TKFLibrary::get('video_icon', $current_data['video_icon'], 'intval', 'POST'),
          'slideshow_width' => TKFLibrary::get('slideshow_width', $current_data['slideshow_width'], 'intval', 'POST'),
          'slideshow_height' => TKFLibrary::get('slideshow_height', $current_data['slideshow_height'], 'intval', 'POST'),
          'slideshow_effect_dur' => TKFLibrary::get('slideshow_effect_dur', $current_data['slideshow_effect_dur'], 'floatval', 'POST'),
          'slideshow_filmstrip_size' => TKFLibrary::get('slideshow_filmstrip_size', $current_data['slideshow_filmstrip_size'], 'intval', 'POST'),
        );
        $save_array = array(
          'user_id' => $user_id,
          'feed_settings' => wp_json_encode($data_settings),
          'updated_at' => $timestamp,
          'published' => TKFLibrary::get('published', 1, 'intval', 'POST'),
        );

        $format = array('%d', '%s', '%d', '%d' );
        $where = array('id' => $feed_id);
        $whereformat = array('%d');
        $insert = $this->model->tkf_update_feed( $save_array, $format, $where, $whereformat );

        $redirect_url = admin_url( 'admin.php?page=feeds_tkf&task=edit&feed_id='.$feed_id );
    }
    if( $insert ) {
      wp_send_json_success(array('msg' => __('Feed successfully saved', 'tkf'), 'redirect_url' => $redirect_url, 'save_action' => $save_action, 'feed_id' => $feed_id, 'user_id' => $user_id));
    }
    wp_send_json_error(array('msg' =>  __('Something went wrong, please try again', 'tkf')));
  }

  /**
   * Get videos from API and Insert data to DB.
   *
   * @param integer $user_id
   * @param integer $feed_id
   *
  */
  public function get_save_videos() {
    $user_id = TKFLibrary::get('user_id', 0, 'intval', 'POST');
    $feed_id = TKFLibrary::get('feed_id', 0, 'intval', 'POST');
    $redirect_url = TKFLibrary::get('redirect_url');
    $save_action = TKFLibrary::get('save_action', 'update');
    $cursor = TKFLibrary::get('cursor', 0);
    $iter = TKFLibrary::get('iter', 0, 'intval', 'POST');
    $data = $this->model->get_open_id_token( $user_id );

    if( !empty($data) ) {
      $access_token = $data['access_token'];
      $open_id = $data['open_id'];

        $fields = [
            "create_time",
            "cover_image_url",
            "share_url",
            "video_description",
            "duration",
            "height",
            "width",
            "id",
            "title",
            "embed_html",
            "embed_link",
            "like_count",
            "comment_count",
            "share_count",
            "view_count",
        ];

      $body_args = array(
        'max_count' => 20,
      );
      if( $cursor ) {
        $body_args['cursor'] = intval($cursor);
      }
      $args = array(
                'method'    => 'POST',
                'headers'   => array(
                  'Content-Type' => 'application/json',
                  'Authorization' => "Bearer " . $access_token,
                ),
                'body'      => wp_json_encode($body_args),
            );
      $url = 'https://open.tiktokapis.com/v2/video/list/?fields='.implode(",", $fields);
      $response = wp_remote_post( $url, $args );
      $body = json_decode($response['body'], 1);
      $videos = isset($body['data']) && isset($body['data']['videos']) ? $body['data']['videos'] : array();
      foreach ( $videos as $video ) {
        $data = array(
                  'user_id'           => intval($user_id),
                  'feed_id'           => intval($feed_id),
                  'create_time'       => intval($video['create_time']),
                  'title'             => sanitize_text_field($video['title']),
                  'cover_image_url'   => sanitize_url($video['cover_image_url']),
                  'share_url'         => sanitize_url($video['share_url']),
                  'video_description' => sanitize_text_field($video['video_description']),
                  'embed_html'        => $video['embed_html'],
                  'embed_link'        => sanitize_url($video['embed_link']),
                  'video_id'          => intval($video['id']),
                  'height'            => intval($video['height']),
                  'width'             => intval($video['width']),
                  'duration'          => intval($video['duration']),
                  'like_count'        => intval($video['like_count']),
                  'comment_count'     => intval($video['comment_count']),
                  'share_count'       => intval($video['share_count']),
                  'view_count'        => intval($video['view_count']),
                  'created_at'        => current_time( 'timestamp' ),
               );
        $format = array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d');
        $this->model->tkf_insert_video( $data, $format );
      }
      $has_more = false;
      $cursor = 0;
      if( isset($body['data']['has_more']) ) {
        $has_more = $body['data']['has_more'];
        $cursor = $body['data']['cursor'];
      }
      wp_send_json_success( array(
                              'msg' => __('Feed successfully saved', 'tkf'),
                              'redirect_url' => $redirect_url,
                              'save_action' => $save_action,
                              'feed_id' => $feed_id,
                              'user_id' => $user_id,
                              'has_more' => $has_more,
                              'cursor' => $cursor,
                              'videos' => $videos[0]['id'],
                              'iter' => $iter,
                            )
      );
    }
  }

  /* Remove feed from the admin list */
  public function tkf_delete() {
    $id = TKFLibrary::get('current_id', 0, 'intval');
    if( $id != 0 ) {
      $this->model->tkf_delete_feed( $id );
    }

    $this->display();
  }

}