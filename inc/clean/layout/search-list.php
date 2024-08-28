<?php
function mage_search_list(){
    $cpt_label = wbbm_get_option( 'wbbm_cpt_label', 'wbbm_general_setting_sec', 'Bus');
    $route_title_bg_color = wbbm_get_option('wbbm_search_route_title_b_color', 'wbbm_style_setting_sec');
    $route_title_bg_color = $route_title_bg_color ? $route_title_bg_color : '#727272';

    ?>
    <div class="mage_route_title" style="background-color:<?php echo $route_title_bg_color; ?>">
        <p>
            <strong><?php echo wbbm_get_option('wbbm_route_text', 'wbbm_label_setting_sec',__('Route', 'bus-booking-manager')); ?></strong>
            <?php echo mage_get_isset('bus_start_route'); ?>
            <span class="fas fa-long-arrow-alt-right"></span>
            <?php echo mage_get_isset('bus_end_route'); ?>
            <strong><?php echo wbbm_get_option('wbbm_date_text', 'wbbm_label_setting_sec',__('Date', 'bus-booking-manager')); ?></strong>
            <?php echo mage_wp_date(mage_get_isset('j_date')); ?>
        </p>
    </div>
    <div class="mage-search-res-wrapper">
        <?php do_action( 'woocommerce_before_single_product' ); ?>
        <div class="mage-search-res-header">
            <div class="mage-search-res-header--img">
                <span><?php _e('Image', 'bus-booking-manager'); ?></span>
            </div>
            <div class="mage-search-res-header--left">
                <span><?php echo $cpt_label.' '.__('Name', 'bus-booking-manager'); ?></span>
                <span><?php _e('Schedule', 'bus-booking-manager'); ?></span>
            </div>
            <div class="mage-search-res-header--right">
                <span><?php _e('Type', 'bus-booking-manager'); ?></span>
                <span><?php _e('Fare', 'bus-booking-manager'); ?></span>
                <span><?php _e('Seat Available', 'bus-booking-manager'); ?></span>
                <span><?php _e('View', 'bus-booking-manager'); ?></span>
            </div>
        </div>
        <?php mage_search_bus_list(false); ?>
        <!-- <div class="mage-search-res-wrapper--footer"></div> -->
    </div>
    <?php //mage_search_bus_list(false); ?>
    <?php if (isset($_GET['r_date']) && $_GET['r_date'] != '') { ?>
        <div class="mage_route_title return_title" style="background-color:<?php echo $route_title_bg_color; ?>">
            <p>
                <strong><?php echo wbbm_get_option('wbbm_route_text', 'wbbm_label_setting_sec',__('Route', 'bus-booking-manager')); ?></strong>
                <?php echo mage_get_isset('bus_end_route'); ?>
                <span class="fas fa-long-arrow-alt-right"></span>
                <?php echo mage_get_isset('bus_start_route'); ?>
                <strong><?php echo wbbm_get_option('wbbm_date_text', 'wbbm_label_setting_sec',__('Date', 'bus-booking-manager')); ?></strong>
                <?php echo mage_wp_date(mage_get_isset('r_date')); ?>
            </p>
        </div>
        <div class="mage-search-res-wrapper">
            <div class="mage-search-res-header">
                <div class="mage-search-res-header--img">
                    <span>Image</span>
                </div>
                <div class="mage-search-res-header--left">
                    <span><?php echo $cpt_label.' '.__('Name', 'bus-booking-manager'); ?></span>
                    <span>Schedule</span>
                </div>
                <div class="mage-search-res-header--right">
                    <span>Type</span>
                    <span>Fare</span>
                    <span>Seat Available</span>
                    <span>View</span>
                </div>
            </div>
            <?php mage_search_bus_list(true); ?>
        </div>
        <div class="mage_mini_cart mage_hidden">
            <p><?php echo wbbm_get_option('wbbm_total_text', 'wbbm_label_setting_sec',__('Total', 'bus-booking-manager')); ?></p>
            <p class="mage_total"><strong><span><?php echo wc_price(0); ?></span></strong></p>
        </div>
    <?php } 
}
