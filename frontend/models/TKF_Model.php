<?php
class Model_tkf {

  public function get_feed_medias( $query_params ) {
    global $wpdb;
    $prepareArgs = array($query_params['user_id']);
    $query = 'SELECT * FROM ' . $wpdb->prefix . 'tkf_medias WHERE user_id=%d';
    $orderby = $query_params["order_by"];
    $order = $query_params["order"];
    $query .= ' ORDER BY `' . $orderby . '` ' . $order;
    $query .= ' LIMIT %d, %d';
    $prepareArgs[] = ($query_params["page"]-1)*$query_params["limit"];
    $prepareArgs[] = $query_params["limit"];

    $result =  $wpdb->get_results($wpdb->prepare( $query, $prepareArgs ), ARRAY_A); //db call ok; no-cache ok
    return $result;
  }

  public function get_feed_medias_total( $query_params ) {
    global $wpdb;
    $prepareArgs = array($query_params['user_id']);
    $query = 'SELECT count(id) FROM ' . $wpdb->prefix . 'tkf_medias WHERE user_id=%d';
    $count =  $wpdb->get_var($wpdb->prepare( $query, $prepareArgs )); //db call ok; no-cache ok
    return $count;
  }

  public function get_feed_data( $feed_id ) {
    global $wpdb;

    $query = 'SELECT tb1.*, tb2.avatar_url_100 as avatar,  tb2.display_name FROM '. $wpdb->prefix . 'tkf_feeds tb1 ';
    $query .= 'LEFT JOIN '.$wpdb->prefix . 'tkf_users tb2 ';
    $query .= 'ON tb1.user_id = tb2.id WHERE tb1.id=%d';
    $result = $wpdb->get_row( $wpdb->prepare($query, $feed_id), ARRAY_A ); //db call ok; no-cache ok
    return $result;
  }
}
