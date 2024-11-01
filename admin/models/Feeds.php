<?php

class FeedsModel_tkf {
  /**
   * Getting all fields from all accounts.
   *
   * @return array
   */
  public function get_all_accounts() {
    global $wpdb;
    $result = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "tkf_users", ARRAY_A); //db call ok; no-cache ok
    return $result;
  }

  /**
   * Getting all fields from all accounts.
   *
   * @return array
   */
  public function get_accounts_ids() {
    global $wpdb;
    $result = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "tkf_users", ARRAY_A); //db call ok; no-cache ok
    return $result;
  }

  /**
   * Insert Feed data.
   *
   * @param array $format
   * @param array $data
   *
   * @return bool
  */
  public function tkf_insert_feed( $data, $format ) {
    global $wpdb;
    $result = $wpdb->insert($wpdb->prefix . 'tkf_feeds', $data, $format); //db call ok
    if( $result ) {
      $lastid = $wpdb->insert_id;
      return $lastid;
    }
    return $result;
  }

  /**
   * Update Feed data.
   *
   * @param array $format
   * @param array $data
   * @param array $where
   * @param array $whereformat
   *
   * @return bool
  */
  public function tkf_update_feed( $data, $format, $where, $whereformat ) {
    global $wpdb;
    $result = $wpdb->update($wpdb->prefix . 'tkf_feeds', $data, $where, $format, $whereformat); //db call ok; no-cache ok

    return $result;
  }

  /**
   * Get feed data by feed id.
   *
   * @param integer $feed_id
   *
   * @return array
  */
  public function tkf_get_feed( $feed_id ) {
    global $wpdb;
    $result = $wpdb->get_row($wpdb->prepare('SELECT * FROM '. $wpdb->prefix . 'tkf_feeds WHERE id=%d', $feed_id), ARRAY_A); //db call ok; no-cache ok

    return $result;
  }

  /**
   * Get all Feeds data.
   *
   * @return array
   */
  public function tkf_get_all_feeds() {
    global $wpdb;
    $result = $wpdb->get_results('SELECT tb1.*, tb2.avatar_url_100 as avatar,  tb2.display_name FROM '. $wpdb->prefix . 'tkf_feeds tb1 LEFT JOIN '.$wpdb->prefix . 'tkf_users tb2 ON tb1.user_id = tb2.id', ARRAY_A); //db call ok; no-cache ok

    return $result;
  }

  /**
   * Getting access token and open_id by unique open_id.
   *
   * @param integer $user_id
   *
   * @return array
   */
  public function get_open_id_token( $user_id ) {
    global $wpdb;
    $result = $wpdb->get_row($wpdb->prepare("SELECT access_token, open_id FROM " . $wpdb->prefix . "tkf_users WHERE id = '%d'", $user_id), ARRAY_A); //db call ok; no-cache ok
    return $result;
  }

  /**
   * Insert Feed data.
   *
   * @param array $format
   * @param array $data
   *
   * @return bool
  */
  public function tkf_insert_video( $data, $format) {
    global $wpdb;
    $id = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".$wpdb->prefix ."tkf_medias WHERE user_id=%d AND video_id=%d", $data['user_id'], $data['video_id'])); //db call ok; no-cache ok
    if( $id ) {
      $result = $wpdb->update( $wpdb->prefix . 'tkf_medias', $data, array('id' => $id), $format, array('%d') ); //db call ok; no-cache ok
    } else {
      $result = $wpdb->insert($wpdb->prefix . 'tkf_medias', $data, $format); //db call ok
    }
    TKFLibrary::tkf_settings_update( $data['user_id'] );
    return $result;
  }

  /**
   * Remove Feed by id.
   *
   * @param int $id
   *
   * @return bool
  */
  public function tkf_delete_feed( $id ) {
    global $wpdb;
    $delete = $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'tkf_feeds` WHERE id=%d', $id)); //db call ok; no-cache ok
    return $delete;
  }
}