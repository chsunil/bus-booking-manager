<?php
add_action('mage_search_from_only', 'search_from_only',10,2);
function search_from_only($single_bus,$target) {
    $search_form_dropdown_b_color = wbbm_get_option('wbbm_search_form_dropdown_b_color', 'wbbm_style_setting_sec');

    $search_form_dropdown_text_color = wbbm_get_option('wbbm_search_form_dropdown_t_color', 'wbbm_style_setting_sec');
    $search_form_dropdown_text_color = $search_form_dropdown_text_color ? $search_form_dropdown_text_color : '#727272';
?>
    <form action="<?php echo $single_bus ? '' : get_site_url() .'/'. $target.'/'; ?>" method="get" class="mage_form">
        <div class="mage_form_list">
            <label for="bus_start_route">
                <span class="fa fa-map-marker"></span>
               <?php echo wbbm_get_option('wbbm_from_text', 'wbbm_label_setting_sec',__('From :', 'bus-booking-manager')); ?>
            </label>
            <div class="mage_input_select mage_bus_boarding_point">
                <div class="route-input-wrap"><input id="bus_start_route" type="text" class="mage_form_control" name="bus_start_route" value="<?php echo mage_get_isset('bus_start_route'); ?>" placeholder="<?php _e('Please Select / Type', 'bus-booking-manager'); ?>" autocomplete="off" required/></div>
                <?php
                if($single_bus){
                    $start_stops = get_post_meta(get_the_id(),'wbbm_bus_bp_stops',true);
                    echo '<div class="mage_input_select_list"><ul style="background-color:'.($search_form_dropdown_b_color != '' ? $search_form_dropdown_b_color : '#FFEB3B').'">';
                    foreach ($start_stops as $_start_stops) {
                        echo '<li style="color:'.$search_form_dropdown_text_color.'" data-route="' . $_start_stops['wbbm_bus_bp_stops_name'] . '"><span class="fa fa-map-marker"></span>' . $_start_stops['wbbm_bus_bp_stops_name'] . '</li>';
                    }
                    echo '</ul></div>';
                }else {
                    mage_route_list();
                }
                ?>
            </div>
        </div>
        <div class="mage_form_list">
            <label for="bus_end_route">
                <span class="fa fa-map-marker"></span>
                <span id="wbtm_show_msg"></span>
                <?php echo wbbm_get_option('wbbm_to_text', 'wbbm_label_setting_sec',__('To :', 'bus-booking-manager')); ?>
            </label>
            <div class="mage_input_select mage_bus_dropping_point">
                <div class="route-input-wrap"><input id="bus_end_route" type="text" class="mage_form_control" name="bus_end_route" value="<?php echo mage_get_isset('bus_end_route'); ?>" placeholder="<?php _e('Please Select / Type', 'bus-booking-manager'); ?>" autocomplete="off" required/></div>
                <?php
                if($single_bus){
                    $end_stops = get_post_meta(get_the_id(),'wbbm_bus_next_stops',true);
                    echo '<div class="mage_input_select_list_static"><ul class="">';
                    foreach ($end_stops as $_end_stops) {
                        echo '<li data-route="' . $_end_stops['wbbm_bus_next_stops_name'] . '"><span class="fa fa-map-marker"></span>' . $_end_stops['wbbm_bus_next_stops_name'] . '</li>';
                    }
                    echo '</ul></div>';
                }else {
                    mage_route_list(true);
                }
                ?>
            </div>
        </div>
        <div class="mage_form_list">
            <label for="j_date">
                <span class="fa fa-calendar"></span>
                <?php echo wbbm_get_option('wbbm_date_of_journey_text', 'wbbm_label_setting_sec',__('Date of Journey :', 'bus-booking-manager')); ?>                
            </label>
            <input type="date" class="mage_form_control" id="j_date" name="j_date" value="<?php echo mage_get_isset('j_date'); ?>" placeholder="<?php echo wbbm_convert_datepicker_dateformat(); ?>" autocomplete="off" required  min="<?php echo date("Y-m-d"); ?>" max="<?php echo date('Y-m-d', strtotime('+2 days', current_time('timestamp') ) ); ?>">
        </div>
        <?php if (!$single_bus) {
            $return = (mage_get_isset('bus-r') == 'oneway') ? false : true; ?>
            <div class="mage_form_list mage_return_date <?php echo $return ? '' : 'mage_hidden' ?>">
                <label for="r_date">
                    <span class="fa fa-calendar"></span>
                    <?php echo wbbm_get_option('wbbm_return_date_text', 'wbbm_label_setting_sec',__('Return Date (Optional):', 'bus-booking-manager')); ?>                     
                </label>
                <input type="date" class="mage_form_control" id="r_date" name="r_date" value="<?php echo mage_get_isset('r_date'); ?>" autocomplete="off" placeholder="<?php echo wbbm_convert_datepicker_dateformat(); ?>"   min="<?php echo date("Y-m-d"); ?>" max="<?php echo date('Y-m-d', strtotime('+2 days', current_time('timestamp') ) ); ?>">
            </div>
        <?php } ?>
        <div class="mage_form_list">
            <div class="mage_form_radio">
                <?php if (!$single_bus) { ?>
                    <label for="one_way">
                        <input type="radio" name="bus-r" value='oneway' id="one_way" <?php echo $return ? '' : 'checked' ?> />
                        <?php echo wbbm_get_option('wbbm_one_way_text', 'wbbm_label_setting_sec',__('One Way', 'bus-booking-manager')); ?> 
                    </label>
                    <label for="return">
                        <input type="radio" name="bus-r" value='return' id="return" <?php echo $return ? 'checked' : '' ?>/>
                        <?php echo wbbm_get_option('wbbm_return_text', 'wbbm_label_setting_sec',__('Return', 'bus-booking-manager')); ?>  
                    </label>
                <?php } else {
                    echo '<label>&nbsp;</label>';
                } ?>
            </div>
            <div class="mage_form_search">
                <button type="submit" class="mage_button">
                    <span class="fa fa-search"></span>
                    <?php echo wbbm_get_option('wbbm_search_buses_text', 'wbbm_label_setting_sec', __('Search', 'bus-booking-manager')); ?> 
                </button>
            </div>
        </div>
    </form>
    <?php
}