<?php

class TKFLibrary {


  public static function two_kses_allowed_html() {
    $allowed_attr = array(
      'title' => 1,
      'src'	=> 1,
      'alt'	=> 1,
      'class'	=> 1,
      'id'	=> 1,
      'data-tkf' => 1,
      'data-thumbnail-width' => 1,
      'data-thumbnail-height' => 1,
      'data-feed-id' => 1,
      'data-max-count' => 1,
      'data-id' => 1,
      'data-width' => 1,
      'data-height' => 1,
      'data-src' => 1,
      'href'=> 1,
      'style'=> 1,
      'target' => 1
    );

    $allowed_html = array(
      'div' => $allowed_attr,
      'p' => $allowed_attr,
      'span' => $allowed_attr,
      'ul' => $allowed_attr,
      'li' => $allowed_attr,
      'a' => $allowed_attr,
      'img' => $allowed_attr,
      'h1' => $allowed_attr,
      'h2' => $allowed_attr,
      'h3' => $allowed_attr,
      'h4' => $allowed_attr,
      'h5' => $allowed_attr,
      'br' => array(),
    );

    return $allowed_html;
  }

  /**
   * Clean page prefix.
   *
   * @param  string $str
   * @return string $str
   */
  public static function clean_page_prefix($str = '') {
    $str = str_replace('_' . TKF()->prefix, '', $str);
    $str = ucfirst($str);

    return $str;
  }

  /**
   * Get request value.
   *
   * @param $key
   * @param $default_value
   * @param $callback
   * @param $type
   *
   * @return array|bool|mixed|string|null
   */
  public static function get($key, $default_value = '', $callback = 'sanitize_text_field', $type = 'DEFAULT') {
    switch ($type) {
      case 'REQUEST' :
        if (isset($_REQUEST[$key])) {
          if ( is_bool($_REQUEST[$key]) ) {
            return rest_sanitize_boolean($_REQUEST[$key]);
          }
          elseif (is_array($_REQUEST[$key])) {
            $value = array();
            foreach ($_REQUEST[$key] as $valKey => $val) {
              $value[$valKey] = self::validate_data($val, $callback);
            }
          }
          else {
            $value = self::validate_data($_REQUEST[$key], $callback);
          }
        }
        break;
      case 'DEFAULT' :
      case 'POST' :
        if ( isset($_POST[$key]) ) {
          if ( is_bool($_POST[$key]) ) {
            return rest_sanitize_boolean($_POST[$key]);
          }
          elseif ( is_array($_POST[$key]) ) {
            $value = array();
            foreach ( $_POST[$key] as $valKey => $val ) {
              $value[$valKey] = self::validate_data($val, $callback);
            }
          }
          else {
            $value = self::validate_data($_POST[$key], $callback);
          }
        }
        if ( 'POST' === $type ) break;
      case 'GET' :
        if (isset($_GET[$key])) {
          if ( is_bool($_GET[$key]) ) {
            return rest_sanitize_boolean($_GET[$key]);
          }
          elseif ( is_array($_GET[$key]) ) {
            $value = array();
            foreach ( $_GET[$key] as $valKey => $val ) {
              $value[$valKey] = self::validate_data($val, $callback);
            }
          }
          else {
            $value = self::validate_data($_GET[$key], $callback);
          }
        }
        break;
    }

    if ( !isset($value) ) {
      if ( $default_value === NULL ) {
        return NULL;
      } else {
        $value = $default_value;
      }
    }

    return $value;
  }

  /**
   * @param $value
   * @param $callback
   *
   * @return mixed|string
   */
  private static function validate_data($value, $callback) {
    $value = stripslashes($value);
    if ( $callback && function_exists($callback) ) {
      $value = $callback($value);
    }

    return $value;
  }

  /**
   * Get user id from feed_id
   *
   * @param $feed_id int
   *
   * @return int
  */
  private static function get_user_id( $feed_id ) {
    global $wpdb;
    $user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM " . $wpdb->prefix . "tkf_feeds WHERE id=%d", $feed_id)); //db call ok; no-cache ok
    return $user_id;
  }

  /**
   * Check if video thumb image expired according user_id or for all users
   *
   * @param $feed_id int
   *
   * @return array
  */
  public static function tkf_check_thumb_expired( $feed_id = 0 ) {

    global $wpdb;
    $user_id = self::get_user_id( $feed_id );
    $tkf_settings = get_option('tkf_settings');
    if( empty($tkf_settings) ) {
      $results = $wpdb->get_results("SELECT id, access_token, open_id FROM " . $wpdb->prefix . "tkf_users", ARRAY_A); //db call ok; no-cache ok
      return $results;
    }

    /* Just need to check for specific user */
    if ( $user_id != 0 ) {
        $now = current_time( 'timestamp' );
        $tkf_thumb_expired = isset($tkf_settings['thumb_expired'][$user_id]) ? $tkf_settings['thumb_expired'][$user_id] : 0;
        if ( $now >  ($tkf_thumb_expired-3600) ) {
          $results = $wpdb->get_results($wpdb->prepare("SELECT id, access_token, open_id FROM " . $wpdb->prefix . "tkf_users WHERE id=%d", $user_id), ARRAY_A); //db call ok; no-cache ok
          $key = array_search( $user_id, array_column($results, 'id') );
          if( isset($results[$key]) ) {
            return array( $results[$key] );
          }
        }
        return array();
    } else { /* Need to check for all user */
        $return = array();
        $results = $wpdb->get_results("SELECT id, access_token, open_id FROM " . $wpdb->prefix . "tkf_users", ARRAY_A); //db call ok; no-cache ok


        foreach ( $results as $result ) {
          $user_id = $result['id'];
          $tkf_thumb_expired = isset($tkf_settings['thumb_expired'][$user_id]) ? $tkf_settings['thumb_expired'][$user_id] : 0;
          $now = current_time( 'timestamp' );
          if ( $now >  ($tkf_thumb_expired-3600) ) {
            $return[] = array(
              'id' => $user_id,
              'open_id' => $result['open_id'],
              'access_token' => $result['access_token'],
            );
          }
        }
        return $return;
    }
  }

  /**
   * Call TikTok endpoint and update thumb images.
   *
   * @param $datas array
   *
  */
  public static function tkf_update_expired_thumbs( $datas = array() ) {
    if( empty($datas) ) return;

    global $wpdb;
    foreach ( $datas as $data ) {
      $access_token = $data['access_token'];
      $args = array(
        'method' => 'POST',
          'headers'   => array(
              'Content-Type' => 'application/json',
              'Authorization' => "Bearer " . $access_token,
          ),
        'body' => wp_json_encode(array('max_count' => 15,)),
      );
      $url = 'https://open.tiktokapis.com/v2/video/list/?fields=cover_image_url,id';
      $response = wp_remote_post($url, $args);
      if ( is_wp_error( $response ) ) {
        return;
      }
      $body = json_decode($response['body'], 1);
      $videos = isset($body['data']['videos']) ? $body['data']['videos'] : array();
      foreach ( $videos as $video ) {
        $update_data = array(
          'cover_image_url' => sanitize_url($video['cover_image_url']),
        );
        $format = array( '%s' );
        $where = array( 'video_id' => intval($video['id']) );
        $whereformat = array( '%d' );
        $update = $wpdb->update($wpdb->prefix . 'tkf_medias', $update_data, $where, $format, $whereformat); //db call ok; no-cache ok
        if( $update ) {
          self::tkf_settings_update( $data['id'] );
        }
      }
    }
  }

  /**
   * Update options where kept last update time for thumb images
   *
   * @param $user_id int
   *
  */
  public static function tkf_settings_update( $user_id ) {
    $tkf_settings = get_option('tkf_settings');
    $tkf_settings['thumb_expired'][$user_id] = current_time('timestamp');
    update_option('tkf_settings', $tkf_settings, 1);
  }

}