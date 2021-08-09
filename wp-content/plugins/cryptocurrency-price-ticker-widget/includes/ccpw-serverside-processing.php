<?php
/**
 * Create response for datatable AJAX request
 */


function ccpwp_get_ajax_data(){

        if ( !isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'ccpwf-tbl-widget' ) ){
            die ('Please refresh window and check it again');
        }

		$start_point    = $_REQUEST['start']?$_REQUEST['start']:0;
        $data_length    = $_REQUEST['length']?$_REQUEST['length']:10;
        $current_page   = (int)$_REQUEST['draw']?$_REQUEST['draw']:1;
        $requiredCurrencies = ccpwp_set_default_if_empty($_REQUEST['requiredCurrencies'],'top-10');
        $fiat_currency = $_REQUEST['currency'] ? $_REQUEST['currency'] :'USD';
        $fiat_currency_rate = $_REQUEST['currencyRate'] ? $_REQUEST['currencyRate'] : 1;
        $coin_no=$start_point+1;
        $coins_list=array();
        $order_col_name = 'market_cap';
        $order_type ='DESC';
        $DB = new ccpwp_database;
        $Total_DBRecords = '2500';
        $coins_request_count=$data_length+$start_point;


        if( $start_point > 2149 ){
            ccpwp_get_api_data(10);
        }else if( $start_point > 1999 ){
            ccpwp_get_api_data(9);
        }else if( $start_point > 1749 ){
            ccpwp_get_api_data(8);
        }else if( $start_point > 1399 ){
            ccpwp_get_api_data(7);
        }else if( $start_point > 1149 ){
            ccpwp_get_api_data(6);
        }else if( $start_point > 899 ){
            ccpwp_get_api_data(5);
        }else if( $start_point > 699 ){
            ccpwp_get_api_data(4);
        }else if( $start_point > 399 ){
            ccpwp_get_api_data(3);
        }else if( $start_point > 99 ){
            ccpwp_get_api_data( 2 );
        }
        
        switch($requiredCurrencies){
            case 'top-10':
                $requiredCurrencies='10';
            break;
            case 'top-20':
                $requiredCurrencies='20';
            break;
            case 'top-50':
                $requiredCurrencies='50';
            break;
            case 'top-100':
                $requiredCurrencies='100';
            break;
            case 'all':
                $requiredCurrencies = $Total_DBRecords;
            break;
        }

        // create a table and gather all data in case the table is removed somehow
        if( false === $DB->is_table_exists() ){
            ccpwp_get_api_data();
        }

        $coindata= $DB->get_coins( array("number"=>$data_length,'offset'=> $start_point,'orderby' => $order_col_name,
        'order' => $order_type
          ));
          $coin_ids=array();
          if($coindata){
            foreach($coindata as $coin){
                 $coin_ids[]= $coin->coin_id;
            }
        }
   
		$response = array();
        $coins = array();
        $bitcoin_price = get_transient('ccpw_btc_price');
        $coins_list=array();
       
        if($coindata){

            foreach($coindata as $coin){
                $coin = (array)$coin;
                $coins['rank'] = $coin_no;
                $coins['id']    =   $coin['coin_id'];
                $coins['logo'] = ccpwp_coin_logo_html( $coin['coin_id'], $GETHTML = false );
                $coins['symbol']= strtoupper($coin['symbol']);
                $coins['name'] = strtoupper($coin['name']);
                $coins['price'] = $coin['price'];
                if($fiat_currency=="USD"){
                    $coins['price'] = $coin['price'];
                    $coins['market_cap'] = $coin['market_cap'];
                    $coins['total_volume'] = $coin['total_volume'];
                    $c_price=$coin['price'];
                }else{
                    $coins['price'] = $coin['price']* $fiat_currency_rate;
                    $coins['market_cap'] = $coin['market_cap'] * $fiat_currency_rate;
                    $coins['total_volume'] = $coin['total_volume'] * $fiat_currency_rate;
                }
                $coins['change_percentage_24h'] = number_format($coin['percent_change_24h'],2,'.','');
                $coins['market_cap'] = $coin['market_cap'];
                $coins['total_volume'] = $coin['total_volume'];
                $coins['supply'] = $coin['circulating_supply'];

                $coin_no++;
                $coins_list[]= $coins;

            }   //end of foreach-block
        }   //end of if-block
       
		$response = array("draw"=>$current_page,"recordsTotal"=>$Total_DBRecords,"recordsFiltered"=> $requiredCurrencies,"data"=>$coins_list);
		echo json_encode( $response );
}