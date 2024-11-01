<?php
class TKFViewSite {


  public function container($params = array(), $tkf = 0, $content = '') {
    ?>
    <div id="tkf_container1_<?php echo esc_attr($tkf); ?>"
         class="tkf_container tkf_thumbnail tkf_<?php echo esc_attr($params['feed_data']['feed_type']); ?>"
         data-tkf="<?php echo esc_attr($tkf); ?>"
         data-gallery-type="<?php echo esc_attr($params['feed_data']['feed_type']); ?>"
         data-gallery-view-type="<?php echo esc_attr($params['feed_data']['feed_type']); ?>"
         data-feed-id="<?php echo esc_attr($params['feed_data']['id']); ?>"
         data-pagination-type="<?php echo esc_attr($params['feed_data']['pagination_type']); ?>">
      <div id="tkf_container2_<?php echo esc_attr($tkf); ?>"
        <div id="tkf_container3_<?php echo esc_attr($tkf); ?>" class="tkf-background tkf-background-<?php echo esc_attr($tkf); ?>">
            <?php
            if ( isset($params['medias']) && !count($params['medias']) ) {
                echo esc_html__('No Videos found.', 'tkf');
            } else {
                echo wp_kses($content, TKFLibrary::two_kses_allowed_html());
            }
            ?>
          </div>
          <?php
          if ( $params['total'] > $params['feed_data']['per_page_number'] && $params['feed_data']['feed_type'] != 'slideshow') {
            $this->tkf_pagination($params);
          }
          ?>
      </div>
    </div>

    <script>
      if (document.readyState === 'complete') {
        if( typeof tkf_main_ready == 'function' ) {
          if ( jQuery("#tkf_container1_<?php echo esc_attr($tkf); ?>").height() ) {
            tkf_main_ready(jQuery("#tkf_container1_<?php echo esc_attr($tkf); ?>"));
          }
        }
      } else {
        document.addEventListener('DOMContentLoaded', function() {
          if( typeof tkf_main_ready == 'function' ) {
            if ( jQuery("#tkf_container1_<?php echo esc_attr($tkf); ?>").height() ) {
             tkf_main_ready(jQuery("#tkf_container1_<?php echo esc_attr($tkf); ?>"));
            }
          }
        });
      }
    </script>
    <?php
  }

  public function tkf_pagination( $params ) {
    $page = $params['page'];
    if( $params['feed_data']['pagination_type'] == 'pagination' ) {
        $per_page_number = $params['feed_data']['per_page_number'];
        if ( intval(ceil($params['total'] / $per_page_number)) > 0 ): ?>
          <ul class="tkf_pagination">
            <?php if ( $page > 1 ): ?>
              <li class="tkf_prev" data-page="<?php echo intval($page - 1); ?>"><?php _e('Prev', 'tkf'); ?></li>
            <?php endif; ?>

            <?php if ( $page > 3 ): ?>
              <li class="tkf_start" data-page="1">1</li>
              <li class="tkf_dots">...</li>
            <?php endif; ?>

            <?php if ( $page - 2 > 0 ): ?>
              <li class="tkf_page"
                  data-page="<?php echo intval($page - 2); ?>"><?php echo intval($page - 2) ?></li><?php endif; ?>
            <?php if ( $page - 1 > 0 ): ?>
              <li class="tkf_page"
                  data-page="<?php echo intval($page - 1); ?>"><?php echo intval($page - 1) ?></li><?php endif; ?>

            <li class="tkf_currentpage" data-page="<?php echo intval($page); ?>"><?php echo intval($page) ?></li>

            <?php if ( $page + 1 < intval(ceil($params['total'] / $per_page_number)) + 1 ): ?>
              <li class="tkf_page"
                  data-page="<?php echo intval($page + 1); ?>"><?php echo intval($page + 1) ?></li><?php endif; ?>
            <?php if ( $page + 2 < ceil($params['total'] / $per_page_number) + 1 ): ?>
              <li class="tkf_page"
                  data-page="<?php echo intval($page + 2); ?>"><?php echo intval($page + 2) ?></li><?php endif; ?>

            <?php if ( $page < ceil($params['total'] / $per_page_number) - 2 ): ?>
              <li class="tkf_dots">...</li>
              <li class="tkf_end"
                  data-page="<?php echo intval(ceil($params['total'] / $per_page_number)); ?>"><?php echo intval(ceil($params['total'] / $per_page_number)) ?></li>
            <?php endif; ?>

            <?php if ( $page < ceil($params['total'] / $per_page_number) ): ?>
              <li class="tkf_next" data-page="<?php echo intval($page + 1); ?>"><?php _e('Next', 'tkf'); ?></li>
            <?php endif; ?>
          </ul>
        <?php endif;
    } elseif( $params['feed_data']['pagination_type'] == 'loadmore' ) {
        ?>
        <ul class="tkf_pagination" data-total="<?php echo intval($params['total']); ?>">
          <li data-page="<?php echo intval($page+1) ?>"><?php _e('Load More', 'tkf'); ?></li>
        </ul>
        <?php
    }
  }

  public function loading( $tkf = 0 ) {
     ?>
    <div class="tkf_ajax_loading tkf-hidden"></div>
    <?php
  }
}
