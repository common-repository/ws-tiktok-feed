<?php

class SettingsView_tkf {

  public function display( $params = array() ) {
    wp_enqueue_style(TKF()->prefix . '_settings');
    ?>
    <div id="tkf-settings-page">
      <h2><?php _e('Settings', 'tkf'); ?></h2>
      <span onclick="login_tiktok('<?php echo esc_url($params['url']); ?>')" class="tkf-connect-button"><?php _e('Login to TikTok Account', 'tkf'); ?></span>
      <p class="tkf-description">
        <?php _e('You need Access Token for using TikTok API. Click sign in with TikTok button above to get yours.', 'tkf'); ?>
        <br>
        <?php _e('This will not show your TikTok media. After that you may create your feed.', 'tkf'); ?>
      </p>

      <?php if( !empty($params['all_accounts']) ) {  ?>
        <h2><?php _e('Accounts', 'tkf'); ?></h2>
        <p class="tkf-hidden tkf-message"></p>
        <?php
        foreach ( $params['all_accounts'] as $account ) {
          $avatar = !empty($account['avatar_url_100']) ? $account['avatar_url_100'] : TKF()->plugin_url . '/assets/images/empty_avatar.png';
          $display_name = isset($account['display_name']) ? $account['display_name'] : __('Unknown', 'tkf');
           ?>
          <div class="tkf-account-row" data-user-id="<?php echo esc_attr($account['id']); ?>">
            <div class="tkf-account-header">
              <div class="tkf-account-avatar"><img src="<?php echo esc_url($avatar); ?>" alt="<?php _e('Avatar', 'tkf'); ?>"></div>
              <div class="tkf-account-display-name"><?php echo esc_html($display_name); ?></div>
              <div class="tkf-account-openfull"><span class="tkf-account-openfull-icon"><i class="dashicons dashicons-arrow-down-alt2"></i></span></div>
              <div class="tkf-account-remove"><button onclick="tkf_account_remove('<?php echo esc_html($account['open_id']); ?>', '<?php echo intval($account['id']); ?>')"><?php _e('Remove', 'tkf'); ?></button></div>
            </div>
            <div class="tkf-account-row-content tkf-hidden">
              <label><?php _e('Access Token', 'tkf'); ?></label>
              <input type="text" class="tkf-account-access-token-input" value="<?php echo esc_attr($account['access_token']); ?>">
              <label><?php _e('Refresh Access Token', 'tkf'); ?></label>
              <button class="tkf-refresh-token-button" onclick="tkf_refresh_token('<?php echo esc_html($account['open_id']); ?>', '<?php echo intval($account['id']); ?>')"><?php _e('Refresh', 'tkf'); ?></button>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>

    <?php
  }
}
