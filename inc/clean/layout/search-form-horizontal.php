<?php
function mage_search_form_horizontal($single_bus,$target='')
{
    $search_form_b_color = wbbm_get_option('wbbm_search_form_b_color', 'wbbm_style_setting_sec');
    
    ?>
    <div class="mage_container">
        <div class="search_form_horizontal" style="background-color:<?php echo ($search_form_b_color != '' ? $search_form_b_color : '#fff'); ?>">
            <h2><?php echo wbbm_get_option('wbbm_buy_ticket_text', 'wbbm_label_setting_sec',__('BUY TICKET', 'bus-booking-manager')); ?></h2>
            <?php search_from_only($single_bus,$target); ?>
        </div>
    </div>
<?php } ?>