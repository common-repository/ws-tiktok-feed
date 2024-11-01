<?php
/**
* Plugin Name: WebSync TikTok Feed
* Plugin URI: https://websync.tech/
* Description: TikTok Feed is the ultimate plugin to display custom videos of TikTok on your website
* Version: 1.0.4
* Author: WebSync Team
* License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

final class TKF {
  /**
   * The single instance of the class.
   */
  protected static $_instance = null;

  /**
   * Plugin version.
   */
  public $plugin_version = '';

  public $prefix = '';
  public $nicename = '';
  public $plugin_dir = '';
  public $plugin_url = '';
  public $is_pro = FALSE;

  /**
   * Main TKF Instance.
   *
   * Ensures only one instance is loaded or can be loaded.
   *
   * @static
   * @return TKF - Main instance.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * TKF Constructor.
   */
  public function __construct() {

    $this->plugin_version = '1.0.4';
    $this->prefix = 'tkf';
    $this->nicename = __('TikTok Gallery', 'ifg');
    $this->plugin_dir = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));
    $this->plugin_url = plugins_url(plugin_basename(dirname(__FILE__)));
    require_once($this->plugin_dir . '/library/TKFLibrary.php');

    $this->add_actions();
  }

  /**
   * All actions.
  */
  public function add_actions() {

    add_action('init', array( $this, 'tkf_check_tokens_expire' ));
    add_action('admin_init', array( $this, 'tkf_activate' ));
    add_action('admin_menu', array( $this, 'admin_menu' ) );


    add_action('wp_ajax_settings_' . $this->prefix, array( $this, 'admin_ajax' ));
    add_action('wp_ajax_feeds_' . $this->prefix, array( $this, 'admin_ajax' ));

    add_action('wp_ajax_frontend_' . $this->prefix, array( $this, 'tkf_frontend' ));
    add_action('wp_ajax_nopriv_frontend_' . $this->prefix, array( $this, 'tkf_frontend' ));

    add_shortcode('tkf_feed', array( $this, 'tkf_shortcode' ));

    add_action('wp_enqueue_scripts', array($this, 'register_frontend_scripts'));
    add_action('admin_enqueue_scripts', array( $this, 'register_admin_scripts' ));

  }


  public function tkf_check_tokens_expire() {
    $this->tkf_register_tkF_preview_cpt();
    require_once( $this->plugin_dir . '/admin/controllers/Settings.php' );
    $ob = new SettingsController_tkf();
    $ob->tkf_check_tokens_expire();
  }

  /**
   * Activate plugin.
   */
  public function tkf_activate() {
    $this->check_update_expire_thumbs();
    require_once $this->plugin_dir . "/tkf_insert.php";
    TKFInsert::tkf_insert();
  }

  public function admin_ajax() {
    $tkf_ajax_nonce = TKFLibrary::get('tkf_ajax_nonce');
    if( !wp_verify_nonce( $tkf_ajax_nonce, 'tkf-admin-nonce') ) {
       die;
    }
    $page = TKFLibrary::get('action');
    if ( !empty($page) ) {
      $page = TKFLibrary::clean_page_prefix($page);
      $controller_page = $this->plugin_dir . '/admin/controllers/' . $page . '.php';
      $model_page = $this->plugin_dir . '/admin/models/' . $page . '.php';
      $view_page = $this->plugin_dir . '/admin/views/' . $page . '.php';
      // Load page file.
      require_once($controller_page);
      require_once($model_page);
      require_once($view_page);
      $controller_class = $page . 'Controller_' . $this->prefix;
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  /* Plugin admin menus */
  public function admin_menu() {
    $parent_slug = 'feeds_' . $this->prefix;
    add_menu_page($this->nicename, $this->nicename, 'manage_options', 'feeds_' . $this->prefix, array($this , 'admin_pages'), $this->plugin_url . '/assets/images/menu_icon.png');
    add_submenu_page($parent_slug, __('All Feeds', 'tkf'), __('All Feeds', 'tkf'), 'manage_options', 'feeds_' . $this->prefix, array($this , 'admin_pages'));
    add_submenu_page($parent_slug, __('Global Settings', 'tkf'), __('Global Settings', 'tkf'), 'manage_options', 'settings_' . $this->prefix, array($this , 'admin_pages'));
  }

  /**
   * Register admin pages scripts/styles.
   */
  public function register_admin_scripts() {
    wp_register_script($this->prefix . '_admin', $this->plugin_url . '/assets/js/'.$this->prefix.'_admin.js', array('jquery'), $this->plugin_version);
    wp_localize_script($this->prefix . '_admin', 'tkf_obj', array(
      'account_remove_success' => __('Account successfully removed.', 'tkf'),
      'something_wrong' => __('Something went wrong, please try again.', 'tkf'),
      'refresh_success' => __('Token successfully refreshed.', 'tkf'),
      'tkf_ajax_nonce' =>  wp_create_nonce( 'tkf-admin-nonce' ),
    ));
    wp_register_script($this->prefix . '_feeds', $this->plugin_url . '/assets/js/'.$this->prefix.'_feeds.js', array('jquery'), $this->plugin_version);
    wp_register_style($this->prefix . '_admin', $this->plugin_url . '/assets/css/'.$this->prefix.'_admin.css', array(), $this->plugin_version);
    wp_register_style($this->prefix . '_settings', $this->plugin_url . '/assets/css/'.$this->prefix.'_settings.css', array(), $this->plugin_version);
    wp_register_style($this->prefix . '_feeds', $this->plugin_url . '/assets/css/'.$this->prefix.'_feeds.css', array(), $this->plugin_version);
  }

  public function register_frontend_scripts() {
    wp_register_style($this->prefix . '_frontend', $this->plugin_url . '/assets/css/' . $this->prefix . '_frontend.css', array(), $this->plugin_version);
    if ( $this->is_pro ) {
      wp_register_script($this->prefix . '_slideshow', $this->plugin_url . '/assets/js/' . $this->prefix . '_slideshow.js', array( 'jquery' ), $this->plugin_version);
    }
    wp_register_script($this->prefix . '_frontend', $this->plugin_url . '/assets/js/'.$this->prefix.'_frontend.js', array('jquery'), $this->plugin_version);
    wp_localize_script($this->prefix . '_frontend', 'tkf_obj', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'loadmore' =>  __('Load More', 'tkf'),
      'loading' =>  __('Loading...', 'tkf'),
      'tkf_ajax_nonce' =>  wp_create_nonce( 'tkf-frontend-nonce' ),
    ));
  }

  /**
   * Admin pages.
   */
  public function admin_pages() {
    wp_enqueue_script($this->prefix . '_admin');
    $allowed_pages = array(
      'feeds_' . $this->prefix,
      'settings_' . $this->prefix,
    );
    $page = TKFLibrary::get('page');
    if ( !empty($page) && in_array($page, $allowed_pages) ) {
      $page = TKFLibrary::clean_page_prefix($page);

      $controller_page = $this->plugin_dir . '/admin/controllers/' . $page . '.php';
      $model_page = $this->plugin_dir . '/admin/models/' . $page . '.php';
      $view_page = $this->plugin_dir . '/admin/views/' . $page . '.php';

      if ( !is_file($controller_page) ) {
        echo wp_sprintf(esc_html__('The controller %s file not exist.', 'tkf'), '"<b>' . esc_html($page) . '</b>"');
        return FALSE;
      }
      if ( !is_file($view_page) ) {
        echo wp_sprintf(esc_html__('The view %s file not exist.', 'tkf'), '"<b>' . esc_html($page) . '</b>"');
        return FALSE;
      }
      // Load page file.
      require_once($controller_page);
      if ( is_file($model_page) ) {
        require_once($model_page);
      }
      require_once($view_page);
      $controller_class = $page . 'Controller_' . $this->prefix;
      $model_class = $page . 'Model_' . $this->prefix;
      $view_class = $page . 'View_' . $this->prefix;
      // Checking page class.
      if ( !class_exists($controller_class) ) {
        echo wp_sprintf(esc_html__('The %s class not exist.', 'tkf'), '"<b>' . esc_html($controller_class) . '</b>"');
        return FALSE;
      }
      if ( !class_exists($view_class) ) {
        echo wp_sprintf(esc_html__('The %s class not exist.', 'tkf'), '"<b>' . esc_html($view_class) . '</b>"');
        return FALSE;
      }
      if ( !class_exists($model_class) ) {
        echo wp_sprintf(esc_html__('The %s class not exist.', 'tkf'), '"<b>' . esc_html($view_class) . '</b>"');
        return FALSE;
      }
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  public function check_update_expire_thumbs( $user_id = 0 ) {
    $data = TKFLibrary::tkf_check_thumb_expired( $user_id );
    TKFLibrary::tkf_update_expired_thumbs( $data );
  }

  public function tkf_frontend() {

      $tkf_ajax_nonce = TKFLibrary::get('tkf_ajax_nonce');
      if( !wp_verify_nonce( $tkf_ajax_nonce, 'tkf-frontend-nonce') ) {
        die;
      }
      $params['id'] = TKFLibrary::get('tkf_feed_id', 1, 'intval');
      $this->tkf_shortcode($params);
  }

  public function tkf_shortcode( $params ) {
    $this->check_update_expire_thumbs( $params['id'] );
    require_once($this->plugin_dir . '/frontend/controllers/TKF_Controller.php');
    $controller = new Controller_tkf( $params );
    $controller->execute();
    return str_replace(array( "\r\n", "\n", "\r" ), '', ob_get_clean());
  }

  /**
   * Register feed preview custom post type.
   */
  public function tkf_register_tkF_preview_cpt() {
    $this->tkf_register_preview_cpt();

    $feed_id = TKFLibrary::get('feed_id');
    if ( TKFLibrary::get('task') == 'edit' && TKFLibrary::get('page') == 'feeds_tkf' && $feed_id != '' ) {
      $this->tkf_get_feed_preview_post( $feed_id );
    }

  }

  /**
   * Register form preview custom post type.
   */
  public function tkf_register_preview_cpt() {
    $args = array(
      'label' => 'TKF Preview',
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => true,
      'show_in_menu' => false,
      'show_in_nav_menus' => false,
      'create_posts' => 'do_not_allow',
      'capabilities' => array(
        'create_posts' => FALSE,
        'edit_post' => 'edit_posts',
        'read_post' => 'edit_posts',
        'delete_posts' => FALSE,
      )
    );

    register_post_type('feeds_tkf', $args);
    flush_rewrite_rules();
  }

  /**
   * Create Preview Form post.
   *
   * @return string $guid
   */
  public function tkf_get_feed_preview_post( $feed_id ) {
    $post_type = 'feeds_tkf';
    $row = get_posts(array( 'post_type' => $post_type ));

    if ( !empty($row[0]) ) {
      $id = $row[0]->ID;
      $post_params = array(
        'ID' => $id,
        'post_content' => '[tkf_feed id="' . $feed_id . '"]',
      );
      wp_update_post($post_params);
    }
    else {
      $post_params = array(
        'post_author' => 1,
        'post_status' => 'publish',
        'post_content' => '[tkf_feed id="' . $feed_id . '"]',
        'post_title' => 'Preview',
        'post_type' => $post_type,
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_parent' => 0,
        'menu_order' => 0,
        'import_id' => 0,
      );
      // Create new post by fmformpreview type.
      $insert_id = wp_insert_post($post_params);
      if ( !is_wp_error($insert_id) ) {
        $permalink = get_post_permalink($insert_id);
        update_option('tkf_preview_permalink', $permalink);
      }

    }
  }

}

/**
 * Main instance of TKF.
 *
 * @return TKF The main instance to prevent the need to use globals.
 */
function TKF() {
  return TKF::instance();
}

TKF();
