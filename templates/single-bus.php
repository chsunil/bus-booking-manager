<?php
get_header();
the_post();
mage_search_form_horizontal(true);
$id = get_the_id();
$return = false;
$date_format        = get_option( 'date_format' );
$boarding_var = $return ? 'bus_end_route' : 'bus_start_route';
$dropping_var = $return ? 'bus_start_route' : 'bus_end_route';
$date_var = $return ? 'r_date' : 'j_date';
$j_date = wbbm_convert_date_to_php(mage_get_isset($date_var));

$in_cart = mage_find_product_in_cart();

$term = get_the_terms($id, 'wbbm_bus_cat');
$type = $term[0]->name;

// $available_seat = mage_available_seat(mage_get_isset($date_var));
$available_seat = wbbm_intermidiate_available_seat($_GET[$boarding_var], $_GET[$dropping_var], wbbm_convert_date_to_php(mage_get_isset($date_var)));

$boarding = mage_get_isset($boarding_var);
$dropping = mage_get_isset($dropping_var);

$seat_price_adult = mage_seat_price($id, $boarding, $dropping, 'adult');
$seat_price_child = mage_seat_price($id, $boarding, $dropping, 'child');
$seat_price_infant = mage_seat_price($id, $boarding, $dropping, 'infant');

$boarding_time = get_wbbm_datetime(boarding_dropping_time(false, $return),'time');
$dropping_time = get_wbbm_datetime(boarding_dropping_time(true, $return),'time');
$odd_list = mage_odd_list_check(false);
$off_day = mage_off_day_check(false);
$is_sell_off = get_post_meta($id, 'wbbm_sell_off', true);
$seat_available = get_post_meta($id, 'wbbm_seat_available', true);

$c_time = current_time( 'timestamp' );
$is_on_date = false;
$bus_on_dates = array();
$bus_on_date = get_post_meta($id, 'wbtm_bus_on_date', true);
if( $bus_on_date != null ) {
    $bus_on_dates = explode( ', ', $bus_on_date );
    $is_on_date = true;
}

?>
    <div class="mage_container">
        <?php do_action( 'wbbm_before_single_product' ); ?>
        <?php do_action( 'woocommerce_before_single_product' ); ?>
        <div class="mage_search_list <?php echo $in_cart ? 'booked' : ''; ?>" data-seat-available="<?php echo $available_seat; ?>">
            <form action="" method="post">
                <div class="mage_flex_equal xs_not_flex">
                    <div class="mage_thumb"><?php the_post_thumbnail('full'); ?></div>
                    <div class="mage_bus_details">
                        <div class="mage_bus_info">
                            <h3><?php the_title(); ?></h3>
                            <p>
                                <strong><?php _e('Type :', 'bus-booking-manager'); ?></strong>
                                <?php echo $type; ?>
                            </p>
                            <p>
                                <strong><?php echo wbbm_get_option('wbbm_bus_no_text', 'wbbm_label_setting_sec') ? wbbm_get_option('wbbm_bus_no_text', 'wbbm_label_setting_sec') : _e('Bus No:', 'bus-booking-manager'); ?></strong>
                                <?php echo get_post_meta(get_the_id(), 'wbbm_bus_no', true); ?>
                            </p>
                            <?php if ($seat_price_adult > 0 && $odd_list && $off_day) { ?>
                                <p>
                                    <strong><?php _e('Boarding : ', 'bus-booking-manager'); ?></strong>
                                    <?php echo $boarding; ?>
                                    <strong>(<?php echo $boarding_time; ?>)</strong>
                                </p>
                                <p>
                                    <strong><?php _e('Dropping : ', 'bus-booking-manager'); ?></strong>
                                    <?php echo $dropping; ?>
                                    <!-- <strong>(<?php echo $dropping_time; ?>)</strong> -->
                                </p>
                            <?php } ?>
                            <p>
                                <strong><?php echo wbbm_get_option('wbbm_total_seat_text', 'wbbm_label_setting_sec') ? wbbm_get_option('wbbm_total_seat_text', 'wbbm_label_setting_sec') : _e('Total Seat:', 'bus-booking-manager'); ?></strong>
                                <?php echo get_post_meta(get_the_id(), 'wbbm_total_seat', true); ?>
                            </p>
                            <?php if ($seat_price_adult > 0 && $odd_list && $off_day) { ?>
                                <?php if( $is_sell_off != 'on' ) : ?>
                                    <?php if($seat_available && $seat_available == 'on') : ?>
                                        <p>
                                            <strong><?php echo $available_seat; ?></strong>
                                            <?php _e('Seat Available', 'bus-booking-manager'); ?>
                                        </p>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($in_cart) { ?>
                                    <p class="already_cart"><span class="fa fa-cart-plus"></span><?php _e('Item has been added to cart', 'bus-booking-manager'); ?></p>
                                <?php } ?>
                                <?php 
                                $is_price_zero_allow = get_post_meta($id, 'wbbm_price_zero_allow', true);
                                ?>

                                <p><strong><?php _e('Fare : ', 'bus-booking-manager'); ?></strong></p>
                                <div class="mage_center_space mar_b">
                                    <div>
                                        <p>
                                            <?php _e('Adult : ', 'bus-booking-manager'); ?>
                                            <strong><?php echo wc_price($seat_price_adult); ?></strong>/
                                            <small><?php _e('Ticket', 'bus-booking-manager'); ?></small>
                                        </p>
                                    </div>
                                    <?php mage_qty_box($seat_price_adult, 'adult_quantity', false); ?>
                                </div>
                                
                                <?php
                                if ( ($seat_price_child > 0) || ($is_price_zero_allow == 'on') ) : ?>
                                    <div class="mage_center_space mar_b">
                                        <p>
                                            <?php _e('Child : ', 'bus-booking-manager'); ?>
                                            <strong><?php echo wc_price($seat_price_child); ?></strong>/
                                            <small><?php _e('Ticket', 'bus-booking-manager'); ?></small>
                                        </p>
                                        <?php mage_qty_box($seat_price_child, 'child_quantity', false); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ($seat_price_infant > 0) || ($is_price_zero_allow == 'on') ) : ?>
                                    <div class="mage_center_space mar_b">
                                        <p>
                                            <?php _e('Infant : ', 'bus-booking-manager'); ?>
                                            <strong><?php echo wc_price($seat_price_infant); ?></strong>/
                                            <small><?php _e('Ticket', 'bus-booking-manager'); ?></small>
                                        </p>
                                        <?php mage_qty_box($seat_price_infant, 'infant_quantity', false); ?>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>
                            <?php
                                // Pickup Point
                                $boarding_point = isset($_GET[$boarding_var]) ? strip_tags($_GET[$boarding_var]) : '';
                                $boarding_point_slug = strtolower($boarding_point);
                                $boarding_point_slug = preg_replace('/[^A-Za-z0-9-]/', '_', $boarding_point_slug);
                                $pickpoints = get_post_meta(get_the_id(), 'wbbm_selected_pickpoint_name_'.$boarding_point_slug, true);
                                if($pickpoints) {
                                    $pickpoints = unserialize($pickpoints);
                                    if(!empty($pickpoints)) { ?>
                                        <div class="mage-form-field mage-field-inline">
                                            <label for="mage_pickpoint"><?php _e('Select Pickup Area', 'bus-booking-manager'); ?></label>
                                            <select name="mage_pickpoint" class="mage_pickpoint">
                                                <option value=""><?php _e('Select your Pickup Area', 'bus-booking-manager'); ?></option>
                                                <?php 
                                                foreach($pickpoints as $pickpoint) {
                                                    echo '<option value="' . $pickpoint['pickpoint'] . '->' . $pickpoint['time']. '">'.ucfirst($pickpoint['pickpoint']).' <=> '.$pickpoint['time'].'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    <?php
                                    }
                                }
                                // Pickup Point END
                            ?>
                            <?php the_content(); ?>
                            <div class="mage_flex_equal">
                                <div>
                                    <h4 class="mar_b bor_tb">
                                        <?php echo wbbm_get_option('wbbm_boarding_points_text', 'wbbm_label_setting_sec') ? wbbm_get_option('wbbm_boarding_points_text', 'wbbm_label_setting_sec') : _e('Boarding Ponints', 'bus-booking-manager'); ?>
                                    </h4>
                                    <ul>
                                        <?php
                                        $start_stops = get_post_meta(get_the_id(), 'wbbm_bus_bp_stops', true);
                                        foreach ($start_stops as $_start_stops) {
                                            echo "<li><span class='fa fa-map-marker mar_r'></span>" . $_start_stops['wbbm_bus_bp_stops_name'] . "</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="mar_b bor_tb">
                                        <?php echo wbbm_get_option('wbbm_dropping_points_text', 'wbbm_label_setting_sec') ? wbbm_get_option('wbbm_dropping_points_text', 'wbbm_label_setting_sec') : _e('Dropping Ponints', 'bus-booking-manager'); ?>
                                    </h4>
                                    <ul>
                                        <?php
                                        $end_stops = get_post_meta(get_the_id(), 'wbbm_bus_next_stops', true);
                                        foreach ($end_stops as $_end_stops) {
                                            echo "<li><span class='fa fa-map-marker mar_r'></span>" . $_end_stops['wbbm_bus_next_stops_name'] . "</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mage_customer_info_area">
                    <?php
                    $date = isset($_GET[$date_var]) ? strip_tags($_GET[$date_var]) : date('Y-m-d');
                    $start = isset($_GET[$boarding_var]) ? strip_tags($_GET[$boarding_var]) : '';
                    $end = isset($_GET[$dropping_var]) ? strip_tags($_GET[$dropping_var]) : '';
                    hidden_input_field('bus_id', $id);
                    hidden_input_field('journey_date', $date);
                    hidden_input_field('start_stops', $start);
                    hidden_input_field('end_stops', $end);
                    hidden_input_field('user_start_time', $boarding_time);
                    hidden_input_field('bus_start_time', $dropping_time);
                    ?>
                    <div class="adult"></div>
                    <div class="child"></div>
                    <div class="infant"></div>
                </div>
                <?php
                
                // Operatinal on day off day check
                if($j_date != '' && $boarding != '' && $dropping != '') {
                    if( $is_on_date ) {
                        if( in_array( $j_date, $bus_on_dates ) ) {
                            mage_book_now_area($available_seat);
                        } else {
                            echo '<span class="mage_error" style="display: block;text-align: center;padding: 5px;margin: 10px 0 0 0;">'.date($date_format,strtotime(mage_get_isset($date_var))).' Operational Off day !'.'</span>';
                        }
                    } else {

                        // Offday schedule check
                        $bus_stops_times = get_post_meta(get_the_ID(), 'wbbm_bus_bp_stops', true);
                        $bus_offday_schedules = get_post_meta(get_the_ID(), 'wbtm_offday_schedule', true);
                        
                        $start_time = '';
                        foreach($bus_stops_times as $stop) {
                            if($stop['wbbm_bus_bp_stops_name'] == $_GET[$boarding_var]) {
                                $start_time = $stop['wbbm_bus_bp_start_time'];
                            }
                        }

                        $start_time = wbbm_time_24_to_12($start_time);

                        $offday_current_bus = false;
                        if(!empty($bus_offday_schedules)) {
                            $s_datetime = new DateTime( $j_date.' '.$start_time );

                            foreach($bus_offday_schedules as $item) {

                                $c_iterate_date_from = wbbm_convert_date_to_php($item['from_date']);
                                $c_iterate_datetime_from = new DateTime( $c_iterate_date_from.' '.$item['from_time'] );

                                $c_iterate_date_to = wbbm_convert_date_to_php($item['to_date']);
                                $c_iterate_datetime_to = new DateTime( $c_iterate_date_to.' '.$item['to_time'] );

                                if( $s_datetime >= $c_iterate_datetime_from && $s_datetime <= $c_iterate_datetime_to ) {
                                    $offday_current_bus = true;
                                    break;
                                }
                            }
                        }

                        // Check Offday and date
                        if(!$offday_current_bus && mage_off_day_check($return)) {
                            mage_book_now_area($available_seat);
                        }


                        // if (mage_odd_list_check(false) && mage_off_day_check(false) && !$return) {
                        //     $j_time = strtotime($j_date.' '. boarding_dropping_time(false,false));
                        //     if( $c_time < $j_time){
                        //         mage_book_now_area($available_seat);
                        //     }
                        // } else {
                        //     echo '<span class="mage_error" style="display: block;text-align: center;padding: 5px;margin: 10px 0 0 0;">'.date($date_format,strtotime(mage_get_isset($date_var))).' Operational Off day !'.'</span>';
                        // }


                        // if (mage_odd_list_check(true) && mage_off_day_check(true) && wbbm_convert_date_to_php($_GET['r_date']) && $return) {
                        //     $j_time = strtotime(wbbm_convert_date_to_php(mage_get_isset('j_date')).' '. boarding_dropping_time(false,false));
                        //     $r_time = strtotime(wbbm_convert_date_to_php(mage_get_isset('r_date')).' '. boarding_dropping_time(false,true));
                        //     if( $j_time < $r_time){
                        //         mage_book_now_area($available_seat);
                        //     }
                        // }
                    }
                }

                ?>
            </form>
            <?php do_action('mage_multipurpose_reg'); ?>
        </div>
        <?php do_action('after-single-bus'); ?>
    </div>
<?php get_footer(); ?>