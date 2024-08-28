<?php
function mage_get_isset($parameter) {
    return isset($_GET[$parameter]) ? strip_tags($_GET[$parameter]) : '';
}

function mage_qty_box($price,$name, $return) {
    $date = $return ? mage_get_isset('r_date') : mage_get_isset('j_date');
    $available_seat = wbbm_intermidiate_available_seat($_GET['bus_start_route'], $_GET['bus_end_route'], wbbm_convert_date_to_php($date));
    if ($available_seat > 0) {

        if($name == 'child_quantity') {
            $ticket_title = wbbm_get_option('wbbm_child_text', 'wbbm_label_setting_sec', __('Child', 'bus-booking-manager'));
            $ticket_type = 'child';
        } elseif($name == 'infant_quantity') {
            $ticket_title = wbbm_get_option('wbbm_infant_text', 'wbbm_label_setting_sec', __('Infant', 'bus-booking-manager'));
            $ticket_type = 'infant';
        } else {
            $ticket_title = wbbm_get_option('wbbm_adult_text', 'wbbm_label_setting_sec', __('Adult', 'bus-booking-manager'));
            $ticket_type = 'adult';
        }
        ?>
        <div class="mage_form_group">
            <div class="mage_flex mage_qty_dec"><span class="fa fa-minus"></span></div>
            <input type="text"
                   class="mage_form"
                   data-ticket-title="<?php echo $ticket_title.' '.__('Passenger info', 'bus-booking-manager'); ?>"
                   data-ticket-type="<?php echo $ticket_type; ?>"
                   data-price="<?php echo $price; ?>"
                   name="<?php echo $name; ?>"
                   value="<?php echo cart_qty($name); ?>"
                   min="0"
                   max="<?php echo $available_seat; ?>"
                   required
            />
            <div class="mage_flex mage_qty_inc"><span class="fa fa-plus"></span></div>
        </div>
        <?php
    }
}

//print hidden input field
function hidden_input_field($name, $value) {
    echo '<input type="hidden" name="' . $name . '" value="' . $value . '"/>';
}

//return cart qty
function cart_qty($name) {
    $qty_type = ($name == 'adult_quantity') ? 'wbbm_total_adult_qt' : 'wbbm_total_child_qt';
    $product_id = get_the_id();
    $cart = WC()->cart->get_cart();
    foreach ($cart as $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            return $cart_item[$qty_type] > 0 ? $cart_item[$qty_type] : 0;
        }
    }
    return 0;
}

//get route list
function mage_route_list($drop = false) {
    $routes = get_terms(array(
        'taxonomy' => 'wbbm_bus_stops',
        'hide_empty' => false,
    ));

    $search_form_dropdown_b_color = wbbm_get_option('wbbm_search_form_dropdown_b_color', 'wbbm_style_setting_sec');

    $search_form_dropdown_text_color = wbbm_get_option('wbbm_search_form_dropdown_t_color', 'wbbm_style_setting_sec');
    $search_form_dropdown_text_color = $search_form_dropdown_text_color ? $search_form_dropdown_text_color : '#727272';

    echo '<div class="mage_input_select_list"'.($drop ? "id=wbtm_dropping_point_list" : "").' ><ul style="background-color:'.($search_form_dropdown_b_color != '' ? $search_form_dropdown_b_color : '#dfdfdf').'">';
    foreach ($routes as $route) {
        echo '<li style="color:'.$search_form_dropdown_text_color.'" data-route="' . $route->name . '"><span class="fa fa-map-marker"></span>' . $route->name . '</li>';
    }
    echo '</ul></div>';
}

// Bus route list Ajax
add_action('wp_ajax_wbtm_load_dropping_point', 'wbtm_load_dropping_point');
add_action('wp_ajax_nopriv_wbtm_load_dropping_point', 'wbtm_load_dropping_point');
function wbtm_load_dropping_point()
{
    $boardingPoint = strip_tags($_POST['boarding_point']);
    $category = get_term_by('name', $boardingPoint, 'wbbm_bus_stops');
    $allStopArr = get_terms(array(
        'taxonomy' => 'wbbm_bus_stops',
        'hide_empty' => false
    ));
    $dropingarray = get_term_meta($category->term_id, 'wbbm_bus_routes_name_list', true) ? maybe_unserialize(get_term_meta($category->term_id, 'wbbm_bus_routes_name_list', true)) : array();

    if (sizeof($dropingarray) > 0) {
        foreach ($dropingarray as $dp) {
            $name = $dp['wbbm_bus_routes_name'];
            echo "<li data-route='$name'><span class='fa fa-map-marker'></span>$name</li>";
        }
    } else {
        foreach ($allStopArr as $dp) {
            $name = $dp->name;
            echo "<li data-route='$name'><span class='fa fa-map-marker'></span>$name</li>";
        }
    }
    die();
}

//query for bus list
function mage_bus_list_query($start, $end) {
    $start = mage_get_isset($start);
    $end = mage_get_isset($end);
    return array(
        'post_type' => array('wbbm_bus'),
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'meta_value',
        'meta_key' => 'wbbm_bus_start_time',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'wbbm_bus_bp_stops',
                'value' => $start,
                'compare' => 'LIKE',
            ),

            array(
                'key' => 'wbbm_bus_next_stops',
                'value' => $end,
                'compare' => 'LIKE',
            ),
        )
    );
}

//odd range check
function mage_odd_list_check($return) {
    $start_date = strtotime(get_post_meta(get_the_id(), 'wbtm_od_start', true));
    $end_date = strtotime(get_post_meta(get_the_id(), 'wbtm_od_end', true));
    $date = strtotime(wbbm_convert_date_to_php(mage_get_isset($return ? 'r_date' : 'j_date')));

    return (($start_date <=$date ) && ($end_date>=$date) ) ? false : true;
}

//off day check
function mage_off_day_check($return) {
    $current_day = 'od_' . date('D', strtotime($return ? wbbm_convert_date_to_php(mage_get_isset('r_date')) : wbbm_convert_date_to_php(mage_get_isset('j_date'))));
    return get_post_meta(get_the_id(), $current_day, true) == 'yes' ? false : true;
}

//check already in cart
function mage_find_product_in_cart() {
    if( ! is_admin() ) { 
        $product_id = get_the_id();
        $cart = WC()->cart->get_cart();
        foreach ($cart as $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                return true;
            }
        }
    return false;
    }
}

// get available seat
function mage_available_seat($date) {
    $values = get_post_custom(get_the_id());
    $total_seat = $values['wbbm_total_seat'][0];
    $sold_seat = wbbm_get_available_seat(get_the_id(), $date);
    return ($total_seat - $sold_seat) > 0 ? ($total_seat - $sold_seat) : 0;
}

// Get intermidiate available seat
function wbbm_intermidiate_available_seat($start, $end, $date): int
{
    $values = get_post_custom(get_the_id());
    $total_seat = $values['wbbm_total_seat'][0];
    $sold_seat = wbbm_get_available_seat_new(get_the_id(), $start, $end, $date);
    return ($total_seat - $sold_seat) > 0 ? ($total_seat - $sold_seat) : 0;
}

function boarding_dropping_time($drop_time = '', $return = '') {
    $boarding = mage_get_isset('bus_start_route');
    $dropping = mage_get_isset('bus_end_route');
    $boarding_time = get_post_meta(get_the_id(), 'wbbm_bus_bp_stops', true);
    $dropping_time = get_post_meta(get_the_id(), 'wbbm_bus_next_stops', true);
    $start = $return ? $dropping : $boarding;
    $end = $return ? $boarding : $dropping;

    foreach ($boarding_time as $boarding) {
        if ($boarding['wbbm_bus_bp_stops_name'] == $start) {
            foreach ($dropping_time as $dropping) {
                if ($dropping['wbbm_bus_next_stops_name'] == $end) {
                    $start_time = $boarding['wbbm_bus_bp_start_time'];
                    $end_time = $dropping['wbbm_bus_next_end_time'];
                    return $drop_time ? $end_time : $start_time;
                }
            }
        }
    }
    return false;
}

//return fare per ticket
function mage_seat_price($id,$start,$end,$seat_type, $roundtrip = false) {
    $price = get_post_meta($id, 'wbbm_bus_prices', true);
    if (is_array($price) && sizeof($price) > 0) {
        foreach ($price as $key => $val) {
            if ($val['wbbm_bus_bp_price_stop'] == $start && $val['wbbm_bus_dp_price_stop'] == $end) {
                // $ticket_type = $adult ? 'wbbm_bus_price' : 'wbbm_bus_price_child';
                if($seat_type == 'infant') {
                    $ticket_type = 'wbbm_bus_price_infant';
                } elseif($seat_type == 'child') {
                    $ticket_type = 'wbbm_bus_price_child';
                } else {
                    $ticket_type = 'wbbm_bus_price';
                }

                if($roundtrip) {
                    $r_p = $ticket_type.'_roundtrip';
                    if(array_key_exists($r_p, $val) && $val[$r_p] > 0) {
                        return $val[$r_p];
                    } else {
                        return (array_key_exists($ticket_type, $val) && $val[$ticket_type] > 0) ? $val[$ticket_type] : 0;
                    }
                } else {
                    return (array_key_exists($ticket_type, $val) && $val[$ticket_type] > 0) ? $val[$ticket_type] : 0;
                }
                
            }
        }
    }
    return false;
}

// Return Discount
function wbbm_cart_has_opposite_route($c_start, $c_stop, $c_j_date, $return = false, $current_r_date = null) {
    global $woocommerce;
    
    $items = $woocommerce->cart->get_cart();
    if(count($items) > 0) {

        $wbtm_start_stops_current   = $c_start;
        $wbtm_end_stops_current     = $c_stop;
        $journey_date_current       = mage_wp_date($c_j_date, 'Y-m-d');


        foreach( $items as $item => $value ) {
            if( ($value['is_return'] == 1) ) {
                return 0;
            }
        }

        if($journey_date_current) {
            $journey_date_current = new DateTime($journey_date_current);
        }

        if($current_r_date) {
            $current_r_date = mage_wp_date($current_r_date, 'Y-m-d');
            $current_r_date = new DateTime($current_r_date);
        }


        foreach( $items as $item => $value ) {

            if($value['wbbm_journey_date']) {
                $cart_j_date = mage_wp_date($value['wbbm_journey_date'], 'Y-m-d');
                $cart_j_date = new DateTime($cart_j_date);
            }

            if($return && $current_r_date) { // Return
                if( ($wbtm_start_stops_current == $value['wbbm_start_stops']) && ($wbtm_end_stops_current == $value['wbbm_end_stops']) ) {
                    return 1;
                } else {
                    return 0;
                }
            } else { // Not return
                if( ($wbtm_start_stops_current == $value['wbbm_end_stops']) && ($wbtm_end_stops_current == $value['wbbm_start_stops']) ) {
                    return 1;
                } else {
                    return 0;
                }
            }

        }
    }
}

// Convert 24 to 12 time
function wbbm_time_24_to_12($time) {
    $t = '';
    if($time && strpos($time, ':') !== false) {
        $t = explode(':', $time);
        $tm = ((int)$t[0] < 12) ? 'am' : 'pm';
        if($t[0] > 12) {
            $tt = (int)$t[0] - 12;
            $t = $tt.':'.$t[1].' '.$tm;
        } elseif ($t[0] == '00' || $t[0] == '24') {
            $t = '00'.':'.$t[1].' am';
        } else {
            $t = $t[0].':'.$t[1].' '.$tm;
        }
        // $t = $tm;
    }

    return $t;
}

// Convert date format according to wp date format
function mage_wp_date($date, $format = false) {
    $wp_date_format = get_option('date_format');
    if($wp_date_format == 'd/m/Y') {
        $date = str_replace('/', '-', $date);
    }

    if($date && $format) {
        $date = date($format, strtotime($date));

        return $date;
    }


    if($date && $wp_date_format) {
        $date  = date($wp_date_format, strtotime($date));
    }

    return $date;
}
