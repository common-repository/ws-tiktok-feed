<?php
class Controller_tkf {

  /**
   * @var $model
   */
  private $model;

  private $feed_id;

  private $feed_view_settings = array();

  public function __construct( $params ) {

    require_once(TKF()->plugin_dir . "/frontend/models/TKF_Model.php");
    $this->model = new Model_tkf();
    require_once TKF()->plugin_dir . "/frontend/views/TKF_View.php";
    $this->feed_id = $params['id'];
  }

  public function feed_view_default_settings() {
    $this->feed_view_settings = array(
      'thumbnails' => array(
        'thumb_width' => '350',
        'thumb_height' => '250',
      )
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
    $params['feed_data'] = $this->model->get_feed_data($this->feed_id);
    if( empty($params['feed_data']) ) {
      echo "Feed doesn't exists";
      return;
    }
    $feed_settings = json_decode($params['feed_data']['feed_settings'], 1);
    unset($params['feed_data']['feed_settings']);
    $params['feed_data'] = array_merge($params['feed_data'], $feed_settings);

    $user_id = $params['feed_data']['user_id'];
    $params['page'] = TKFLibrary::get('tkf_page', 1, 'intval');
    $query_params = array(
      'user_id' => $user_id,
      'order_by' => $params['feed_data']['sort_media_by'],
      'order' => $params['feed_data']['sort_media_order'],
      'limit' => $params['feed_data']['per_page_number'],
      'page' => $params['page'],
    );

    $params['medias'] = $this->model->get_feed_medias($query_params);
    $params['total'] = $this->model->get_feed_medias_total($query_params);

    $view_name = "TKFView" . ucfirst($params['feed_data']['feed_type']);
    require_once(TKF()->plugin_dir . "/frontend/views/".$view_name.".php");
    $view = new $view_name();
    wp_enqueue_style(TKF()->prefix . '_frontend');
    if ( $params['feed_data']['feed_type'] == 'slideshow' ) {
      wp_enqueue_script(TKF()->prefix . '_slideshow');
    }
    wp_enqueue_script(TKF()->prefix . '_frontend');

    $view->display( $params );

  }
}
