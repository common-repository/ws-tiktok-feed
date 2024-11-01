<?php

class SettingsController_tkf {

  /**
   * @var $model
   */
  private $model;
  /**
   * @var $view
   */
  private $view;

  public function __construct() {
    if( !class_exists('SettingsModel_tkf') ) {
      require_once( TKF()->plugin_dir . '/admin/models/Settings.php' );
      require_once( TKF()->plugin_dir . '/admin/views/Settings.php' );
    }
    $this->model = new SettingsModel_tkf();
    $this->view = new SettingsView_tkf();
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

  /* Display which call default view display */
  public function display() {
    $params['page_url'] = get_admin_url().'admin.php?page=settings_tkf';
    $params['url'] = add_query_arg( array(
                            'action' => 'tkf_login',
                            'redirect_uri' => $params['page_url'],
                          ), 'https://websync.tech' );

    $params['all_accounts'] = $this->model->get_all_accounts();

    $this->view->display( $params );
  }

  /**
   * Get and Update users data avatar, name,.... in the DB token row
   *
   * @param string $open_id unique id for account
   *
  */
  public function set_user_data( $open_id ) {
    $access_token = $this->model->get_token( $open_id );

    if( !empty($access_token) ) {
      $args = array(
        'method'      => 'POST',
        'headers'   => array(
          'Content-Type' => 'application/json',
        ),
          'body' => wp_json_encode(array(
          'open_id' => $open_id,
          'access_token' => $access_token,
          'fields' => array('open_id', 'union_id', 'avatar_url', 'avatar_url_100', 'avatar_url_200', 'avatar_large_url', 'display_name'),
        )),
      );
      $url = 'https://open-api.tiktok.com/user/info/';
      $response = wp_remote_post( $url, $args );
      if ( is_wp_error( $response ) ) {
        return;
      }
      $body = json_decode($response['body'], 1);
      if( empty($body['data']) ) {
        return;
      }
      $user = $body['data']['user'];

      $data = array(
        'avatar_url' => $user['avatar_url'],
        'avatar_large_url' => $user['avatar_large_url'],
        'avatar_url_100' => $user['avatar_url_100'],
        'display_name' => $user['display_name'],
      );
      $where = array('open_id' => $open_id);
      $format = array('%s', '%s', '%s', '%s');
      $format_where = array('%s');
      $this->model->update_token_data( $data, $where, $format, $format_where );
    }
  }

  /* Set login token to DB */
  public function tkf_set_token() {
    $page_url = get_admin_url().'admin.php?page=settings_tkf';
    $status = TKFLibrary::get('status', 0);
    $access_token = TKFLibrary::get('access_token');
    $refresh_token = TKFLibrary::get('refresh_token');
    $open_id = TKFLibrary::get('open_id');
    $tkf_account_exists = $this->model->tkf_account_exists($open_id);
    if ( $status ) {
      if( $access_token == '' ||  $refresh_token == '' ) {
          wp_redirect($page_url);
          die;
      } else {

        $data = array(
          'open_id' => $open_id,
          'access_token' => $access_token,
          'expires_in' => TKFLibrary::get('expires_in'),
          'refresh_token' => $refresh_token,
          'refresh_expires_in' => TKFLibrary::get('refresh_expires_in'),
          'created_at' => current_time( 'timestamp' ),
        );

        /* Temp for testing */
        $tkf_temp_update_data = get_option("tkf_temp_update_data");
        $tkf_temp_update_data[] = $data;
        update_option("tkf_temp_update_data", $tkf_temp_update_data, 1);

        $format = array('%s', '%s', '%d', '%s', '%d');
        if( $tkf_account_exists ) {
          $this->model->update_token_data($data, array('open_id' => $open_id), $format, array('%s'));
        } else {
          $this->model->insert_token_data($data, $format);
        }
        $this->set_user_data( $open_id );
      }
    }
    wp_redirect($page_url);
    die;
  }

  /* Remove account, action is coming from ajax */
  public function tkf_account_remove() {
    $open_id = TKFLibrary::get('open_id');
    $id = $this->model->get_user_id($open_id);
    $res = $this->model->tkf_account_remove( $id );
    if( $res ) {
      wp_send_json_success();
    } else {
      wp_send_json_error();
    }
  }

  /* Refresh access token via refresh token, action is coming from ajax */
  public function tkf_refresh_token( $open_id = '' ) {
    $ajax = 0;
    if( $open_id == '' ) {
      $ajax = 1;
      $open_id = TKFLibrary::get('open_id');
    }
    $refresh_token = $this->model->get_refresh_token( $open_id );
    $args = array(
      'method'      => 'POST',
      'redirection' => 5,
      'httpversion' => '1.0',
      'body'        => array(
        'client_key' => 'aw6w62q5ldcmljfo',
        'grant_type' => 'refresh_token',
        'refresh_token' => $refresh_token
      ),
      'cookies'     => array()
    );
    $url = 'https://open-api.tiktok.com/oauth/refresh_token/';
    $response = wp_remote_post( $url, $args );
    $body = json_decode($response['body'], 1);


    if( $body['message'] == 'success' ) {

      $data = $body['data'];
      $update_data = array(
        'access_token' => $data['access_token'],
        'expires_in' => $data['expires_in'],
        'refresh_token' => $refresh_token,
        'refresh_expires_in' => $data['refresh_expires_in'],
        'created_at' => current_time( 'timestamp' ),
      );

      /* Temp for testing */
      $tkf_temp_update_data = get_option("tkf_temp_update_data");
      $tkf_temp_update_data[] = $update_data;
      update_option("tkf_temp_update_data", $tkf_temp_update_data, 1);

      $where = array('open_id' => $open_id);
      $data_format = array('%s', '%d');
      $where_format = array('%s');
      $update = $this->model->update_token_data($update_data, $where, $data_format, $where_format);
      if( $update !== false ) {
        $this->set_user_data( $open_id );
        if ( $ajax ) {
          wp_send_json_success(array( 'access_token' => $data['access_token'] ));
        }
      }
    }
    if ( $ajax ) {
      wp_send_json_error(array( 'status' => 'error' ));
    }
  }

  /* Check expired tokens and call refresh token */
  public function tkf_check_tokens_expire() {
    $accounts = $this->model->get_all_accounts();
    foreach ( $accounts as $account ) {
      if( intval($account['expires_in'] + $account['created_at']) < current_time( 'timestamp' ) ) {
        $this->tkf_refresh_token( $account['open_id'] );
      }
    }
  }
}