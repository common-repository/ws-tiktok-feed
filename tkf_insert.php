<?php

/**
 * Class TKFInsert
 */
class TKFInsert {

  public static function tkf_insert() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tkf_users = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "tkf_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `open_id` varchar(100) NOT NULL,
    `access_token` varchar(255) NOT NULL,
    `expires_in` int NOT NULL,
    `refresh_token` varchar(255) NOT NULL,
    `refresh_expires_in` int(11) NOT NULL,
    `avatar_url` varchar(255) NULL,
    `avatar_large_url` varchar(255) NULL,
    `avatar_url_100` varchar(255) NULL,
    `display_name` varchar(255) NULL,
    `created_at` int NOT NULL,
    `status` tinyint(4) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($tkf_users); //db call ok; no-cache ok


    $tkf_medias = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "tkf_medias` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `feed_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `create_time` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `cover_image_url` varchar(500) NOT NULL,
    `share_url` varchar(500) NOT NULL,
    `video_description` text NOT NULL,
    `embed_html` text NOT NULL,
    `embed_link` varchar(255) NOT NULL,
    `video_id` bigint NOT NULL,
    `height` int(11) NOT NULL,
    `width` int(11) NOT NULL,
    `duration` int(11) NOT NULL,
    `like_count` int(11) NOT NULL,
    `comment_count` int(11) NOT NULL,
    `share_count` int(11) NOT NULL,
    `view_count` int(11) NOT NULL,
    `created_at` int NOT NULL,
    `status` tinyint(4) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($tkf_medias); //db call ok; no-cache ok

    $tkf_feeds = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "tkf_feeds` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `feed_settings` text NOT NULL,
    `created_at` int(11) NOT NULL,
    `updated_at` int(11) NOT NULL,
    `published` tinyint(2) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($tkf_feeds); //db call ok; no-cache ok
  }
}
