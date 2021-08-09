<?php

$all_data_coin = str_replace(' ','', get_post_meta( $post_id, 'currency_pairs' , true ) );
$design = get_post_meta( $post_id, 'design_binance_live_widget' , true );
$isChart = $design == 'style-1' ? 'false' : 'true';
$class = "ccpwp-binance-live-widget board " . $design;
$all_data_coin = explode("," , $all_data_coin ) ;
$dataApi = array();
foreach( $all_data_coin as $index=>$value ){
    $coin_pairs = explode("/", $value );
    $coin_id = ccpwp_get_coin_ids( $coin_pairs[0] );
    $dataApi[] =  array(
                    "name"=> ucwords( $coin_id),
                    "symbol"=>$coin_pairs[0].$coin_pairs[1],
                    "quote"=>$coin_pairs[1],
                    "base"=>$coin_pairs[0],
                    "logo"=>CCPWP_URL . 'assets/coin-logos/'
                ) ;
}

$vuiWidgetId = "ccpwp_vui_widget_". $post_id;
wp_localize_script( 'ccpw-binance-live-widget' , $vuiWidgetId ,  $dataApi );

ob_start();

?>

<!---------- CCPWP Version:-'. CCPWP_VERSION  .' By Cool Plugins Team -------------->

    <div class='<?php echo $class; ?>' id="<?php echo $vuiWidgetId; ?>" data-logo-url="<?php echo CCPWP_URL . 'assets/coin-logos/'; ?>">
    <div class='card-block' v-for="(value,index) in coins">
    <ccpw-vueWidget v-bind:ischart="<?php echo $isChart; ?>" v-bind:ticker="tickers[value.symbol] || {}" v-bind:info='value'></ccpw-vueWidget>
    </div>
    </div>

<?php
$crypto_html = ob_get_clean();