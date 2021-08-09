<?php

/**
 * 
 * get coins_id by coin symbol
 * 
 * This is just a function wrapper
 * @param string|array $symbol Symbol can be a single coin symbol or an array of symbols
 * 
 * @return string|array a coin id is return in case of string or array if an array of symbols is passed
 */

 function ccpwp_get_coin_ids($symbol){
	$DB = new ccpwp_database;
	return $DB->get_coin_id_by_symbol($symbol);
 }


/*
|-------------------------------------------------------------------------
|	Fetch complete coin list and create a transient 
|-------------------------------------------------------------------------
*/
function ccpwp_get_allCoins(){
	
	$cache_name = 'ccpw-all-gecko-coins';
	$api_url = "http://apiv2.coinexchangeprice.com/v2/coinlist?max=2500";

	$cache = get_transient( $cache_name );

	if( $cache!='' || !empty( $cache ) ){
		return $cache;
	}

	$request = wp_remote_get($api_url, array('timeout' => 120, 'sslverify' => false));
    if (is_wp_error($request)) {
        return false; // Bail early
    }
    $body = wp_remote_retrieve_body($request);
	$response = json_decode($body);
	
	if( !empty( $response->data ) ){
		$coins = ccpwp_objectToArray($response->data);
		$coin_list = array();
		$datalist = null;
		foreach( $coins as $coin_id =>$coin ){
			$coin_list[ $coin_id ] = $coin['name'];
			
			$coindata = array(); 
			$coindata['coin_id'] = $coin_id;
			$coindata['name'] = $coin['name'];
			$coindata['symbol'] = $coin['symbol'];
			$datalist[] = $coindata;
		}
		$DB = new ccpwp_database();
		$DB->ccpw_insert( $datalist );
		set_transient( $cache_name, $coin_list , 0 );
		return $coin_list;
	}

}

/*
|---------------------------------------------------------------------------|
|	Return stats for specific coin					                        |
|---------------------------------------------------------------------------|
*/
function ccpwp_get_coin_stats( $coin_name ){
	$coin_name = trim( strtolower( $coin_name ) );
	if( !isset( $coin_name ) || empty( $coin_name )){
		return __('You must provide a coin name','cmc');
	}

	$cache_name = 'ccpwp_coin_'.$coin_name.'_stats-';

	if( false === ($cache = get_transient( $cache_name ) ) ) {
		$api_url = CCPWP_API."coins/". $coin_name ."?ath=true&weekly=true";
		$request = wp_remote_get($api_url,array('timeout' => 120,'sslverify' => false));

		if (is_wp_error($request)) {
			return false; // Bail early
		}
		$body = wp_remote_retrieve_body($request);
		$response = json_decode($body);
		$coin_list=array();

		if( !isset($response->data)  ){
			return __('Invalid coin id','cmc');
		}

		$coin_data 	= $response->data;
		$market_cap = ccpwp_get_global_data();
		$btc_data 	= ccpwp_coins_data( 'bitcoin' );
		$btc_price 	= $btc_data[0]['price'];
		
		$coin = array();
		$coin['id']					=	$coin_data->coin_id;
		$coin['symbol']				=	$coin_data->symbol;
		$coin['name']				=	$coin_data->name;
		//$coin['rank']				=	$coin_data->rank;
		$coin['price']				=	$coin_data->price;
		$coin['market_cap']			=	$coin_data->market_cap;
		$coin['total_volume']		=	$coin_data->total_volume;
		$coin['low_24h']			=	$coin_data->low_24h;
		$coin['high_24h']			=	$coin_data->high_24h;
		$coin['low_7d']				=	$coin_data->weekly_price_data==null?__('N/A','cmc'):min($coin_data->weekly_price_data);
		$coin['high_7d']			=	$coin_data->weekly_price_data==null?__('N/A','cmc'):max($coin_data->weekly_price_data);
		$coin['market_dominance']   =   $coin['market_cap'] / $market_cap->total_market_cap * 100;
		$coin['ath']				=	$coin_data->ath;
		$coin['since_ath']			=	$coin_data->ath_change_percentage;
		$coin['ath_date']			=	$coin_data->ath_date;

		if( $coin['id'] == 'bitcoin' ){
			$coin['btc_ratio']			=	'1.0';	
		}else if( !is_numeric( $coin['price'] ) || $coin['price'] < 1 ){
			$coin['btc_ratio'] = 'N/A';
		}else{
			$coin['btc_ratio']			=	$btc_price / $coin['price'];
		}

		set_transient( $cache_name, $coin, 12 * HOUR_IN_SECONDS);
		}else{
			return $coin = get_transient( $cache_name );
		}

	return $coin;
}

/*
|--------------------------------------------------------------------------
| coin market global data
|--------------------------------------------------------------------------
*/
function ccpwp_get_global_data(){

	if (false === ($cache = get_transient('cmc-global-data'))) {
//		$request = wp_remote_get( CCPWP_API.'global-data' );
		$request = wp_remote_get( 'https://api.coingecko.com/api/v3/global', array('sslverify' => false) );
		if( is_wp_error( $request ) ) {
			return false; // Bail early
		}
		$body = wp_remote_retrieve_body( $request );
		$api_response = json_decode( $body );
		if( ! empty( $api_response ) ) {

			$global_data = new stdClass();
			foreach( $api_response->data as $key=>$value){
                switch($key){
                    case 'active_cryptocurrencies':
                        $global_data->active_cryptocurrencies  =   $value;
                    break;
                    case 'markets':
                        $global_data->markets    =   $value;
                    break;
                    case 'total_market_cap':
                        $global_data->total_market_cap   =   $value->usd;
                    break;
                    case 'total_volume':
                        $global_data->total_volume   =   $value->usd;
                    break;
                    case 'market_cap_percentage':
                        $global_data->market_cap_percentage  =   array('btc'=> number_format($value->btc,'2','.',''),
																		'eth'=> number_format($value->eth,'2','.','') );
					}
            }

		 set_transient('cmc-global-data', $global_data, 15 * MINUTE_IN_SECONDS);
		 }
	 }else{
		$global_data = get_transient('cmc-global-data');
	 }
		return $global_data;
}

/*
|--------------------------------------------------------------------------
| gathering coin(s) details from database.
| $coin_id can be single coin's id or an array of coins's id
|--------------------------------------------------------------------------
*/
function ccpwp_coins_data($coin_id)
{
	 $DB = new ccpwp_database;

	// create a table and gather all data in case the table is removed somehow
	if( false === $DB->is_table_exists() ){
		ccpwp_get_api_data();
	}
	 $limit = 1;
	 if( is_array($coin_id) ){
		 	$limit = count($coin_id);
	 }
	$coin_data =$DB->get_coins(array('coin_id'=> $coin_id,'number'=> $limit ));
	if(is_array($coin_data)&& isset($coin_data)){
	  $coin_data= ccpwp_objectToArray($coin_data);  
		return $coin_data;
	}else{
		  return false;
	}

}

/*
|--------------------------------------------------------------------------
| creating coins array for settings
|--------------------------------------------------------------------------
*/
function ccpwp_coin_arr($ids = '' , $type = 'all' ){
	$c_list=array();
	$coins_data=array();
	$all_coins = array();
	$limits = 2500;
	if( isset( $GLOBALS['CoinMarketCap'] ) ){
		$limits =3000;
	
	}
	$DB = new ccpwp_database();
	
	// create a table and gather all data in case the table is removed somehow
	if( false === $DB->is_table_exists() ){
		ccpwp_get_api_data();
	}

	switch($type){
		case 'all':
			$all_coins=ccpwp_coins_data($ids);
			if (!empty($all_coins) && is_array($all_coins)) {
				$coins_data=array();
				foreach( $all_coins as $coin){
					$coins_data[$coin['coin_id']] = $coin;
				}
				return $coins_data;
			}
	break;
		case 'options':
		$all_coins =$DB->get_coins( array('number'=> $limits ) );
		return ccpwp_objectToArray($all_coins);
	break;
		case 'list':
		/** This is responsible for returning CryptoCurrencies for settings panel */
	  	$all_coins =$DB->get_coins( array('number'=> $limits,'market_cap'=>1 ) );
			  foreach($all_coins as $coin) {
				  if($coin->coin_id){
				  $c_list[$coin->coin_id] = $coin->name;
				  }
				} 
			return $c_list;
		break;
		case "top":
			$order_col_name = 'market_cap';
			$order_type ='DESC';
			$coin_rs= $DB->get_coins( array("number"=>$ids,'offset'=> 0,'orderby' => $order_col_name,
			'order' => $order_type
			));
			$coin_data=array();
				if(is_array($coin_rs)&& isset($coin_rs)){
				$coin_data= ccpwp_objectToArray($coin_rs);  
				return $coin_data;
				}else{
					return false;
				}
		break;
		default:
			$all_coins =$DB->get_coins( array('number'=>$limits) );
				foreach($all_coins as $coin) {
					$c_list[$coin->coin_id] = $coin->name;
				} 
				return $c_list;
		break;
	}

}

/*
|--------------------------------------------------------------------
|	This function will update unavailable coins in db
|--------------------------------------------------------------------
|	This must be called for specific post-type only
|--------------------------------------------------------------------
*/
function ccpwp_update_ua_coins_on_save_post( $post_id ){

	$available_coins = get_option('ccpwp-available-coins');
	$curr = get_post_meta( $post_id, 'display_currencies', true);
	$curr = $curr == '' ? get_post_meta( $post_id, 'display_currencies_for_table', true) : $curr;
	if( !isset($curr) || !is_array( $curr ) || count( $curr )<0 
	|| !isset($available_coins) || !is_array( $available_coins ) || count( $available_coins )<0
	) return;
	$unavailable_coins = array_diff( $curr, $available_coins);

	if( is_array($unavailable_coins) && !empty( $unavailable_coins ) ){
		$saved_ua_coins = get_option( 'ccpwp-unavailable-coins' );
		$unavailable_coins = $saved_ua_coins==''?$unavailable_coins:array_diff( $unavailable_coins, $saved_ua_coins );
		ccpwp_update_unavailable_coins_data( $unavailable_coins );
	}

}

/*
|-----------------------------------------------------------
| Fetching data through CoinGecko API and save in database
|-----------------------------------------------------------
*/
function ccpwp_get_api_data( $page = 1)
{
	$data_cache_name	= 'cmc-saved-coindata-' . $page;
	$ua_coins_name		= 'ccpwp-unavailable-coins';
	
	$ua_refresh_time	= get_transient('ccpwp-ua-coins-refresh');	
    $ua_coins_cache		= get_option($ua_coins_name);
    $cache 				= get_transient($data_cache_name);

	// Avoid updating database if cache exist or CMC is active
    if ( false != $cache || get_option('cmc-dynamic-links') != false ) {
        return;
	}

	$coins = array();
	// $coin_list = ccpwp_get_allCoins();
	$api_url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=250&page='.$page.'&sparkline=true&price_change_percentage=24h%2C7d%2C30d%2C1y';
    $request = wp_remote_get($api_url, array('timeout' => 300, 'sslverify' => false));
    if (is_wp_error($request)) {
        return false; // Bail early
    }
    $body = wp_remote_retrieve_body($request);
    $coins = json_decode($body);
    $response = array();
    $coin_data = array();
	$db_coins = array();
	$coinid_list = array();
    if (isset($coins) && $coins != "" && is_array($coins)) {
        foreach ($coins as $coin) {
            $response['coin_id'] = $coin->id;
        //    $response['rank'] = $coin->market_cap_rank;
            $response['name'] = $coin->name;
            $response['symbol'] = strtoupper($coin->symbol);
            $response['price'] = ccpwp_set_default_if_empty($coin->current_price, 0.00);
            $response['percent_change_24h'] = ccpwp_set_default_if_empty($coin->price_change_percentage_24h, 0);
            $response['percent_change_1y'] = ccpwp_set_default_if_empty($coin->price_change_percentage_1y_in_currency, 0);
            $response['percent_change_30d'] = ccpwp_set_default_if_empty($coin->price_change_percentage_30d_in_currency, 0);
            $response['percent_change_7d'] = ccpwp_set_default_if_empty($coin->price_change_percentage_7d_in_currency, 0);
            $response['market_cap'] = ccpwp_set_default_if_empty($coin->market_cap, 0);
            $response['total_volume'] = ccpwp_set_default_if_empty($coin->total_volume);
			$response['circulating_supply'] = ccpwp_set_default_if_empty($coin->circulating_supply);

			$charts = array();
			if( !empty($coin->sparkline_in_7d->price) ){
			$x =0;
				foreach ($coin->sparkline_in_7d->price as $chart) {
					if ($x % 6 === 0) {
						if ($chart > 0.50) {
							$charts[] = number_format((float) $chart, 4, '.', '') ;
						} else {
							$charts[] = number_format((float) $chart, 6, '.', '') ;
						}
					}
					$x++;
				}
			}
			$response['weekly_price_data'] = !empty( $charts ) ? serialize( $charts ): null;

            $response['logo'] =	$coin->image;
			$coin_data[] = $response;
			$db_coins[] = $coin->id;
			
			$coinid_list[] = $coin->id;
        }
        $DB = new ccpwp_database();
        $DB->create_table();
		$DB->ccpw_insert($coin_data);
		update_option('ccpwp-available-coins', $db_coins, true );
		// save all the coin id's udated through API by default
		//update_option( 'ccpw-default-coins', $default_coins , $coinid_list );
        set_transient($data_cache_name, date('H:s:i'), 5 * MINUTE_IN_SECONDS );

	}

	// fetch other than 250 default coins
	if( false===( $value = $ua_refresh_time ) ){
		 ccpwp_update_unavailable_coins_data( $ua_coins_cache );
	}

}	// end of ccpwp_get_coin_gecko_data()


/*
|-----------------------------------------------------------
| Fetching data through CoinGecko API and save in database
|-----------------------------------------------------------
*/
function ccpwp_update_unavailable_coins_data( $coin_ids = array() )
{
	$ua_coins_name		= 'ccpwp-unavailable-coins';
	$ua_refresh_time	= 'ccpwp-ua-coins-refresh';
	$ua_coins_cache		= get_option($ua_coins_name);
	
	if( is_array($coin_ids) && empty($coin_ids) || $coin_ids == '' ){
		return false;	// terminate further exectuition if no ids has passed in the argument;
	}
	$required_coins		= urlencode( implode(',',$coin_ids) );

	$coins = array();
	$api_url = 'https://api.coingecko.com/api/v3/coins/markets?ids='.$required_coins.'&vs_currency=usd&order=market_cap_desc&sparkline=true&price_change_percentage=24h%2C7d%2C30d%2C1y';
	$request = wp_remote_get($api_url, array('timeout' => 300, 'sslverify' => false));
    if (is_wp_error($request)) {
		return false; // Bail early
    }
    $body 		= wp_remote_retrieve_body($request);
    $coins 		= json_decode($body);
    $response 	= array();
    $coin_data	= array();
	$db_coins	= array();

    if (isset($coins) && $coins != "" && is_array($coins)) {
        foreach ($coins as $coin) {
            $response['coin_id'] = $coin->id;
            //$response['rank'] = $coin->market_cap_rank;
            $response['name'] = $coin->name;
            $response['symbol'] = strtoupper($coin->symbol);
            $response['price'] = ccpwp_set_default_if_empty($coin->current_price, 0.00);
            $response['percent_change_24h'] = ccpwp_set_default_if_empty($coin->price_change_percentage_24h, 0);
            $response['market_cap'] = ccpwp_set_default_if_empty($coin->market_cap, 0);
            $response['total_volume'] = ccpwp_set_default_if_empty($coin->total_volume);
			$response['circulating_supply'] = ccpwp_set_default_if_empty($coin->circulating_supply);
			
			$charts = array();
			if( !empty($coin->sparkline_in_7d->price) ){
			$x =0;
				foreach ($coin->sparkline_in_7d->price as $chart) {
					if ($x % 6 === 0) {
						if ($chart > 0.50) {
							$charts[] = number_format((float) $chart, 4, '.', '') ;
						} else {
							$charts[] = number_format((float) $chart, 6, '.', '') ;
						}
					}
					$x++;
				}
			}
			$response['weekly_price_data'] = !empty( $charts ) ? serialize( $charts ): null;

            $response['logo'] =	$coin->image;
			$coin_data[] = $response;
			$db_coins[] = $coin->id;
        }
        $DB = new ccpwp_database();
        $DB->create_table();
		$DB->ccpw_insert($coin_data);
		if( is_array($db_coins) && !empty($db_coins) ){
			$db_coins = $ua_coins_cache=='' ? $db_coins : array_merge( $ua_coins_cache, $db_coins);
			$db_coins = array_unique( $db_coins );
			update_option(  $ua_coins_name , $db_coins, true);
			set_transient( $ua_refresh_time, date('H:s:i'), 5 * MINUTE_IN_SECONDS );
		}

    }

}	// end of ccpwp_update_unavailable_coins_data()

/*
 |--------------------------------------------------------------------------
 | server side processing ajax callback for table-widget (Datatable)
 |--------------------------------------------------------------------------
*/
function ccpwp_get_coins_list(){
	require_once( CCPWP_PATH.'includes/ccpw-serverside-processing.php' );
	ccpwp_get_ajax_data();
	wp_die();
}

/*
|-------------------------------------------------------------
| 	Check if provided $value is empty or not.
|-------------------------------------------------------------
|	Return $default if $value is empty
|-------------------------------------------------------------
*/
function ccpwp_set_default_if_empty($value,$default='N/A'){
    return $value?$value:$default;
}

function get_fiat_symbol($fiat_currency){
		$fiat_symbol='';
		 $icon_array = array(
	    'USD' => 'fa fa-usd',    
	    'GBP' => 'fa fa-gbp',
	    'EUR' => 'fa fa-eur',
	    'INR' => 'fa fa-inr',
	    'JPY' => 'fa fa-jpy',
	    'CNY' => 'fa fa-cny',   
	    'ILS' => 'fa fa-ils',
	    'KRW' => 'fa fa-krw',
	    'RUB' => 'fa fa-rub',
	    );
  
        if(isset($icon_array[$fiat_currency])){
			$icon_cls=$icon_array[$fiat_currency];
			 $fiat_symbol='<i class="'.$icon_cls.'" aria-hidden="true"></i>';
        }else{
			  $fiat_symbol='<i class="fa fa-usd" aria-hidden="true"></i>';
   			 }
   		return $fiat_symbol;	 
	}


// currencies symbol
	function ccpwp_get_currency_symbol($name){
		 $cc = strtoupper($name);
		    $currency = array(
		    "USD" => "&#36;" , //U.S. Dollar
		    "AUD" => "&#36;" , //Australian Dollar
		    "BRL" => "R&#36;" , //Brazilian Real
		    "CAD" => "C&#36;" , //Canadian Dollar
		    "CZK" => "K&#269;" , //Czech Koruna
		    "DKK" => "kr" , //Danish Krone
		    "EUR" => "&euro;" , //Euro
		    "HKD" => "&#36;" , //Hong Kong Dollar
		    "HUF" => "Ft" , //Hungarian Forint
		    "ILS" => "&#x20aa;" , //Israeli New Sheqel
		    "INR" => "&#8377;", //Indian Rupee
		    "JPY" => "&yen;" , //Japanese Yen 
		    "MYR" => "RM" , //Malaysian Ringgit 
		    "MXN" => "&#36;" , //Mexican Peso
		    "NOK" => "kr" , //Norwegian Krone
		    "NZD" => "&#36;" , //New Zealand Dollar
		    "PHP" => "&#x20b1;" , //Philippine Peso
		    "PLN" => "&#122;&#322;" ,//Polish Zloty
		    "GBP" => "&pound;" , //Pound Sterling
		    "SEK" => "kr" , //Swedish Krona
		    "CHF" => "Fr" , //Swiss Franc
		    "TWD" => "&#36;" , //Taiwan New Dollar 
			"THB" => "&#3647;" , //Thai Baht
			"TRY" => "&#8378;", //Turkish Lira
			"CNY" => "&#165;",    //China Yuan Renminbi
			"KRW" => "&#8361;",    //South Korean Won
			"RUB" => "&#8381;",    //Russian Ruble
			"SGD" => "S&#36;",    //Singapore Dollar
			"CLP" => "&#36;",    //Chilean peso
			"IDR" => "Rp",        //Indonesian rupiah
			"PKR" => "Rs",        //Pakistani rupee
			"ZAR" => "R",        //South African rand
			"NGN"=>"&#8358;",
			"JMD"=>"J&#36;"
	
		    );
		    
		    if(array_key_exists($cc, $currency)){
		        return $currency[$cc];
		    }
	}



function ccpwp_get_coin_logo($coin_id){
	$coinslist='';
	$coinslist= get_transient('cmc-coin-logos-ids');
	if( empty($coinslist) || $coinslist==="" ) {
		 $request = wp_remote_get( 'https://us-central1-crypto-currencies-images-ids.cloudfunctions.net/coinids',array('sslverify' => false));
	 if( is_wp_error( $request ) ) {
		 return false; // Bail early
	 }
   $body = wp_remote_retrieve_body( $request );
	 $coinslist =json_decode( $body );
	 $coinslist=(array) $coinslist;
	 if(is_array( $coinslist ) && count($coinslist)>0) {
			set_transient('cmc-coin-logos-ids', $coinslist, 24*HOUR_IN_SECONDS);
		}
	}
	if(is_array($coinslist)){
	if(isset($coinslist[$coin_id])){
		return $coinslist[$coin_id];
	}

	}
}

	// creating coins logo html
function ccpwp_coin_logo_html($coin_id, $size = 32, $GETHTML = true)
{
	$logo_html = '';
	$coin_svg = CCPWP_PATH . 'assets/coin-logos/' .$coin_id. '.svg';
	$coin_png = CCPWP_PATH . 'assets/coin-logos/' .$coin_id. '.png';
	$coin_alt = $coin_id;
	if (is_file($coin_svg)) {

		$coin_svg = CCPWP_URL . 'assets/coin-logos/'.$coin_id.'.svg';
		$logo_html = '<img style="width:' . $size . 'px;" id="' . $coin_id . '" alt="' . $coin_alt . '" src="' . $coin_svg . '">';

	} else if (is_file($coin_png)) {

		if ($size == 32) {
			$index = "32x32";
		} else {
			$index = "128x128";
		}
		$coin_icon = CCPWP_URL . 'assets/coin-logos/' . strtolower($coin_id) . '.png';
		$logo_html = '<img id="' . $coin_id . '" alt="' . $coin_alt . '" src="' . $coin_icon . '">';

	} else {
		if ($size == 32) {
			$index = "32x32";
		} else {
			$index = "128x128";
		}
		$DB = new ccpwp_database();
		$coin_icon = $DB->get_coin_logo( $coin_id );

		$logo_html = '<img id="' . $coin_id . '" alt="' . $coin_alt . '" src="' . $coin_icon . '" onerror="this.src = \'https://res.cloudinary.com/pinkborder/image/upload/coinmarketcap-coolplugins/' . $index . '/default-logo.png\';">';
	}

	return $GETHTML==true?$logo_html:$coin_icon;

}
	// small chart ajax handler
	function ccpw_small_chart_data()
	{

		if (isset($_POST['coinid'])) {
			$coin_id = $_POST['coinid'];
			$period = $_POST['period'];
			$history = ccpw_coin_period_historical($coin_id,$period);
			if (!empty($history) && count($history) > 0) {
				echo json_encode(array("type" => "success", "data" => $history));
			} else {
				echo json_encode(array("type" => "error", "data" => $history));
			}
		}
		wp_die();
	}
/*
		single page chart data array
 */

function ccpwp_full_chart_data($coin_id)
{
	$coin_d_arr = array();
	$historical_all_data = ccpw_coin_historical_data($coin_id);
	if (!empty($historical_all_data)) {
		$count = count($historical_all_data->prices);
		for ($i = 0; $i < $count; $i++) {
			$at_time = $historical_all_data->prices[$i][0];
			$coin_price = $historical_all_data->prices[$i][1];
			$coin_vol = $historical_all_data->total_volumes[$i][1];
			$coin_d_arr[] = array('date' => $at_time, 'value' => $coin_price, 'volume' => $coin_vol);
		}
		return $coin_d_arr;
	}
}

/*
|-----------------------------------------------------
|	WP-REST API function for generating chart data
|-----------------------------------------------------
*/
function ccpw_generate_chart(){

	if( !isset($_REQUEST['coin_id']) ){
		echo json_encode( array('status'=>'error','message'=>'No coin id is passed for chart data.') );
		exit();
	}
	$coin_id = $_REQUEST['coin_id'];
	$chart_data = ccpwp_full_chart_data( $coin_id );
	echo json_encode( array('status'=>'success','data'=>$chart_data) );
	exit();
}

  /*
		 Historical data for a given coin
 */

function ccpw_coin_historical_data($coin_id)
{
	$historical_coin_list = get_transient('historical-coingecko-data-' . $coin_id);
	$historical_c_list = array();
	if (empty($historical_coin_list) || $historical_coin_list === "") {
		// $request = wp_remote_get('http://coincap.io/history/365day/' . $coin_id, array('timeout' => 120));
		$request = wp_remote_get('https://api.coingecko.com/api/v3/coins/'.$coin_id.'/market_chart?vs_currency=usd&days=365', array('timeout' => 120,'sslverify' => false));
		if (is_wp_error($request)) {
			return false; // Bail early
		}
		$body = wp_remote_retrieve_body($request);
		$historical_coinsdata = json_decode($body);
		if (!empty($historical_coinsdata)) {
			set_transient('historical-coingecko-data-' . $coin_id, $historical_coinsdata, 2 * HOUR_IN_SECONDS);
			$historical_coin_list = $historical_coinsdata;

		}

	}
	if (!empty($historical_coin_list)) {
		return $historical_coin_list;
	}
}

function ccpw_coin_period_historical($coin_id,$period)
{
	$DB = new ccpwp_database();
	$cache = 'ccpw-chart-cache-'.$coin_id.'-'.$period;
	$limit = 28;
	$aggre = 6;
	$coin_data = array();
	if($period=="7d"){
		$limit=28;
		$aggre=6;
		if( get_transient($cache) ){
			$coin_data = $DB->get_coin_weekly_data( $coin_id );
		}
	}else{
		$limit =24;
		$aggre =1;
		if( get_transient($cache) ){
			$coin_data = $DB->get_coin_extra_info_data( $coin_id );
		}
	}
	$historical_c_list = array();

	if ( (empty($coin_data) || $coin_data == "") ) {
				// create a table and gather all data in case the table is removed somehow
				if( false === $DB->is_table_exists() ){
					ccpwp_get_api_data();
				}
		
		$request = wp_remote_get('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids='.$coin_id.'&order=market_cap_desc&per_page=1&page=1&sparkline=true', array('timeout' => 300,'sslverify' => false ));
//		$request = wp_remote_get('https://min-api.cryptocompare.com/data/histohour?fsym=' . $coin_symbol . '&tsym=USD&limit='. $limit. '&aggregate='.$aggre .'&e=CCCAGG', array('timeout' => 120));

		if (is_wp_error($request)) {
			return false; // Bail early
		}
		$body = wp_remote_retrieve_body($request);
		$response = json_decode($body);
		foreach( $response as $coin){
			$charts = array();
			if( !empty($coin->sparkline_in_7d->price) ){
			$x =0;
				foreach ($coin->sparkline_in_7d->price as $chart) {
					if ($x % 6 === 0) {
						if ($chart > 0.50) {
							$charts[] = number_format((float) $chart, 4, '.', '') ;
						} else {
							$charts[] = number_format((float) $chart, 6, '.', '') ;
						}
					}
					$x++;
				}
			}
			$coin_data = !empty( $charts ) ? serialize($charts) : null;

		}
		
			// Make sure coin_data is a non-emtpy
				if( !empty($coin_data) ){
					$DB->ccpw_insert_coin_weekly_data($coin_id, $coin_data);
					set_transient( $cache , 'available' , 3 * HOUR_IN_SECONDS);
					$coin_data = $DB->get_coin_weekly_data( $coin_id );
				}
	}

	if (!empty($coin_data)) {
		return  $coin_data ;
	}
}

	function ccpw_generate_svg_chart($coin_id ,
	$coin_price,
	$small_chart_color , 
	$small_chart_bgcolor,
	$period ='7d',
	$points=0,
	$currency_symbol="$",
	$currency_price=0,
	$chart_fill="true"
	){
		
		$chart_html='';
		$chart_cache = false;//ccpw_get_chart_cache_data($coin_id, $period);
		$cachedata = '';
		$content = '';
		$no_data_lbl = __('No Graphical Data', 'cmc');
		if ($chart_cache === false) {
			$cachedata = 'false';
		} else {
			$cachedata = 'true';
			if($coin_price){
				$chart_cache[]=(float)str_replace(",","",$coin_price);
				}
			$content = json_encode($chart_cache);
		}

		if( $period == '7d' && is_array($chart_cache) ){
			$last = count($chart_cache);
			if( $chart_cache[0] > $chart_cache[$last-1]  ){
 				$small_chart_color = ccpw_hex2rgba('#ff0000',0.70);
				$small_chart_bgcolor = ccpw_hex2rgba('#ff9999', 0.30);
			}else{
				$small_chart_color = ccpw_hex2rgba('#006400',0.70);
				$small_chart_bgcolor = ccpw_hex2rgba('#90EE90',0.30);
			}
			
		}
	$chart_html .= '<div class="ccpw-chart-container">
		<canvas data-bg-color="' . $small_chart_bgcolor . '"
			data-color="' . $small_chart_color . '" 
			data-msz="' . $no_data_lbl . '"
			data-content="' . $content . '" 
			data-cache="' . $cachedata . '" 
			data-coin-id="' . $coin_id . '" 
			data-period="'. $period . '"
			data-points="'.$points. '"
			data-currency-symbol="' . $currency_symbol . '"
			data-currency-price="' . $currency_price . '"
			data-chart-fill="' . $chart_fill . '"
		  class="ccpw-sparkline-charts"></canvas>
		  </div>';
		//$chart_html .= '<img class="ccpw-small-preloader" src="' . CCPWP_URL . 'images/chart-loading.svg">';
	return $chart_html;
	}

	function ccpw_get_coin_chart($coin_id){	
	$output='';
	$chart_img='https://coolplugins.net/cryptoapi/cryptocharts/img/'. ccpwp_get_coin_logo($coin_id).'.png';
	$output='<span class="ccpw-coin-chart"><img src="'.$chart_img.'" id="'.$coin_id.'.png"></span>';
	//return $output;
	return $output;
}


 function ccpwp_format_number($n){
        
		if($n <= 0.00001  && $n > 0){    
			return $formatted = number_format($n, 8, '.', ',');
		}
		else if($n <= 0.0001  && $n > 0.00001){    
			return $formatted = number_format($n, 6, '.', ',');
		}
		else if($n <= 0.001  && $n > 0.0001){   
			return $formatted = number_format($n, 5, '.', ',');
		}
		else if($n <= 0.01  && $n > 0.001){    
			return $formatted = number_format($n, 4, '.', ',');
		}
		else if($n <= 1  && $n > 0.01){    
			return $formatted = number_format($n, 3, '.', ',');
		}
		else{
			return $formatted = number_format($n, 2, '.', ',');
		}
    }

function ccpw_format_coin_values($value, $precision = 2) {
        if ($value < 1000000) {
            // Anything less than a million
            $formated_str = number_format($value, $precision);
        } else if ($value < 1000000000) {
            // Anything less than a billion
           $formated_str = number_format($value / 1000000, $precision ) . 'M';
        } else {
            // At least a billion
           $formated_str= number_format($value / 1000000000, $precision) . 'B';
        }

   return $formated_str;
   }

  	/* USD conversions */

	function ccpwp_usd_conversions($currency){
		  // use common transient between cmc and ccpw
		  $conversions= get_transient('cmc_usd_conversions');
		  if( empty($conversions) || $conversions==="" ) {
			  $request = wp_remote_get('https://api-beta.coinexchangeprice.com/v1/exchange-rates', array('sslverify' => false));
			  
			  if( is_wp_error( $request ) ) {
				return false;
			  }
			  
			  $currency_ids = array("USD","AUD","BRL","CAD","CZK","DKK", "EUR","HKD","HUF","ILS","INR" ,"JPY" ,"MYR","MXN", "NOK","NZD","PHP" ,"PLN","GBP" ,"SEK","CHF","TWD","THB" ,
			  "TRY","CNY","KRW","RUB", "SGD","CLP", "IDR","PKR", "ZAR","NGN","JMD" );
			  $body = wp_remote_retrieve_body( $request );
			  $conversion_data= json_decode( $body );
			  
			  if(isset($conversion_data->rates)){
				$conversion_data=(array)$conversion_data->rates;
			  }else{
				$conversion_data=array();
			  }
	  
			  if(is_array($conversion_data) && count($conversion_data)>0) {
				foreach($conversion_data as $key=> $currency_price){
					if(in_array($key,$currency_ids)){
					  $conversions[$key]=$currency_price;
					}     
				}
			
			  uksort($conversions, function($key1, $key2) use ($currency_ids) {
				  return (array_search($key1, $currency_ids) > array_search($key2, $currency_ids)) ? 1: -1;
			  });
			
			set_transient('cmc_usd_conversions',$conversions, 12* HOUR_IN_SECONDS);
			  }
			}
	  
			if($currency=="all"){
			  
			  return $conversions;
	  
			}else{
			  if(isset($conversions[$currency])){
				return $conversions[$currency];
			  }
			}
	}


	//Function to generate RSS feed
  function ccpw_rss_feed($rss_desc_length,$rss_url,$rss_no_of_news){
  
  	 /* SimplePie RSS parsing engine */
	  include_once ABSPATH . WPINC . '/feed.php';
	 /* Build the SimplePie object */
	 $rss =fetch_feed($rss_url);
	 // $rss =fetch_feed('https://cointelegraph.com/feed/');
	 /* Check for errors in the RSS XML */
	  if (!is_wp_error($rss)) {
		 
		 $maxitems = $rss->get_item_quantity($rss_no_of_news);
	     $rss_items = $rss->get_items(0, $maxitems);
		
	    // $total_entries = count($rss_items);
		 //$i = 1;
		  $news_data=array();
		  
		foreach ($rss_items as $index=> $item) {
		//	$rss2 = simplexml_load_file($rss_url);
		 $news_data[$index]['title'] = $item->get_title();
		 $news_data[$index]['link'] = $item->get_permalink();
		 $desc = $item->get_description();
		
		 $news_data[$index]['first-image'] =firstImg($desc );
		 $news_data[$index]['date_posted'] = $item->get_date();
		$news_data[$index]['date-time'] =strtotime($item->get_date());
		 if ($enclosure = $item->get_enclosure())
		{
			$news_data[$index]['image-url']=$enclosure->get_link();
		}

		$news_data[$index]['channel']=$rss->get_title();
	
	       $desc = wp_kses(trim($desc), array());
	       $desc = strip_tags(apply_filters('the_excerpt', $desc));
	       $desc = wp_trim_words($desc,$rss_desc_length);
		   $desc = trim(preg_replace('!\s+!', ' ', $desc));		  		
		 $news_data[$index]['description']=$desc;
		}
		return $news_data;

		}
	
	
  }
 // sorting RSS feed by date

	function cccpw_date_compare($a, $b)
		{
		  
		 if ($a['date-time'] < $b['date-time']) {
	                return 1;
	        } else if ($a['date-time'] > $b['date-time']) {
	                return -1;
	        } else {
	                return 0;
	        }

		}

		/*grabing first image from RSS feed */
	function firstImg( $post_content ) {
        $matches = array();
        $output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches );
        if ( isset( $matches[1][0] ) ) {
            $first_img = $matches[1][0];
        }

        if ( empty( $first_img ) ) {
            return '';
        }
        return $first_img;
    }	



    function ccpw_sort_by_gainers($a, $b)
		{
		  
		 if ($a['percent_change_24h'] < $b['percent_change_24h']) {
	                return 1;
	        } else if ($a['percent_change_24h'] > $b['percent_change_24h']) {
	                return -1;
	        } else {
	                return 0;
	        }

		}

	function ccpw_sort_by_losers($a, $b)
		{
		
		 return $a['percent_change_24h'] - $b['percent_change_24h'];

		}	

		  function ccpwp_objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
		
        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        }
        else {
            // Return array
            return $d;
        }
	}


function ccpw_get_chart_cache_data($coin_id,$period)
{
	$DB = new ccpwp_database();

		// create a table and gather all data in case the table is removed somehow
		if( false === $DB->is_table_exists() ){
			ccpwp_get_api_data();
		}
	if($period=="7d"){
		$historical_data = get_transient('7d-historical-' . $coin_id);
	}else{
		$historical_data = get_transient('24h-historical-' . $coin_id);
	}	
	if (is_array($historical_data) && count($historical_data) > 0) {
	//	return json_decode( unserialize( $historical_data) );
	} else {
		return false;
	}

}

function ccpw_generate_column( $column ){
	if (preg_match("/12$/", $column, $match)) {
			$column2=1;
		}
	   else if (preg_match("/6$/", $column, $match)) {
			$column2=2;
	   }
	   else if (preg_match("/4$/", $column, $match)) {
			$column2=3;
	   }
	   else if (preg_match("/3$/", $column, $match)) {
			$column2=4;
	   }
	   else{
		   $column2=6;
	   }
	   return $column2;
}

/* Convert hexdec color string to rgb(a) string */
function ccpw_hex2rgba($color, $opacity = false) {
 
	$default = 'rgb(0,0,0)';
 
	//Return default if no color provided
	if(empty($color))
          return $default; 
 
	//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }
 
        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }
 
        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);
 
        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }
 
        //Return rgb(a) color string
        return $output;
}

function ccpw_HTMLpluginVersion(){
	return '<!-- Cryptocurrency Widgets PRO '.CCPWP_VERSION.'  !-->';
}