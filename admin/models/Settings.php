<?php

class SettingsModel_tkf {

  /**
   * Insert access token data.
   *
   * @param array $format
   * @param array $data
   *
   * @return false|int
  */
  public function insert_token_data( $data = array(), $format = array() ) {
    global $wpdb;
    $query = $wpdb->insert($wpdb->prefix . 'tkf_users', $data, $format); //db call ok

    return $query;
  }

  /**
   * Update access token data.
   *
   * @param array $where
   * @param array $data
   * @param array $format
   * @param array $format_where
   *
   * @return false|int
  */
  public function update_token_data( $data = array(), $where = array(), $format = array(), $format_where = array() ) {
    global $wpdb;
    $query = $wpdb->update($wpdb->prefix . 'tkf_users', $data, $where, $format, $format_where); //db call ok; no-cache ok

    return $query;
  }

  /**
   * Getting access token by unique open_id.
   *
   * @param string $open_id
   *
   * @return string
  */
  public function get_token( $open_id ) {
    global $wpdb;
    $result = $wpdb->get_var($wpdb->prepare("SELECT access_token FROM " . $wpdb->prefix . "tkf_users WHERE open_id = %s",$open_id)); //db call ok; no-cache ok
    return $result;
  }

  /**
   * Getting refresh token by unique open_id.
   *
   * @param string $open_id
   *
   * @return string
  */
  public function get_refresh_token( $open_id ) {
    global $wpdb;
    $result = $wpdb->get_var($wpdb->prepare("SELECT refresh_token FROM " . $wpdb->prefix . "tkf_users WHERE open_id = %s",$open_id)); //db call ok; no-cache ok
    return $result;
  }

  /**
   * Getting all fields from all accounts.
   *
   * @return array
  */
  public function get_all_accounts() {
    global $wpdb;
    $result = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "tkf_users", ARRAY_A);  //db call ok; no-cache ok
    return $result;
  }

  /**
   * Getting id field by unique open_id.
   *
   * @param string $open_id
   *
   * @return integer
  */
  public function get_user_id( $open_id ) {
    global $wpdb;
    $result = $wpdb->get_var( $wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "tkf_users WHERE open_id = %s", $open_id) );  //db call ok; no-cache ok
    return $result;
  }

  /**
   * Removing account and medias of that acount by id
   *
   * @param int $id
   *
   * @return bool
  */
  public function tkf_account_remove( $id ) {
    global $wpdb;
    $res = $wpdb->delete( $wpdb->prefix . "tkf_users", array( 'id' => $id ), array( '%d' ) );  //db call ok; no-cache ok
    if( $res !== false ) {
      $wpdb->delete($wpdb->prefix . "tkf_medias", array( 'user_id' => $id ), array( '%d' ));  //db call ok; no-cache ok
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Checking if account already exists
   *
   * @param string $open_id
   *
   * @return bool
  */
  public function tkf_account_exists( $open_id ) {
    global $wpdb;
    $rowcount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".$wpdb->prefix . "tkf_users WHERE open_id = %s", $open_id));  //db call ok; no-cache ok
    if( $rowcount > 0 ) {
      return TRUE;
    }
    return FALSE;
  }
}