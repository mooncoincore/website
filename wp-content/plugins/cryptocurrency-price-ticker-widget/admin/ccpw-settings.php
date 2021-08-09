<?php

    /**
     * Initiate the metabox
     */  

     $currencies_arr = array(
     'USD'   => 'USD', 
     'GBP'   => 'GBP',
     'EUR'   => 'EUR',
     'INR'   => 'INR',
     'JPY'   => 'JPY',
     'CNY'   => 'CNY',
     'ILS'   => 'ILS',
     'KRW'   => 'KRW',
     'RUB'   => 'RUB',  
    'DKK'   => 'DKK',
    'PLN'   => 'PLN',
    'AUD'   => 'AUD',
    'BRL'   => 'BRL',
    'MXN'   => 'MXN',
    'SEK'   => 'SEK',
    'CAD'   => 'CAD',
    'HKD'   => 'HKD',
    'MYR'   => 'MYR',
    'SGD'   => 'SGD',
    'CHF'   => 'CHF',
    'HUF'   => 'HUF',
    'NOK'   => 'NOK',
    'THB'   => 'THB',
    'CLP'   => 'CLP',
    'IDR'   => 'IDR',
    'NZD'   => 'NZD',
    'TRY'   => 'TRY',
    'PHP'   => 'PHP',
    'TWD'   => 'TWD',
    'CZK'   => 'CZK',
    'PKR'   => 'PKR',
    'ZAR'   => 'ZAR',
    'NGN'=>'NGN',
    'JMD'=>'JMD'
  );

  $crypto_arr=ccpwp_coin_arr($coin_id="",$type='list');
  
    $cmb = new_cmb2_box( array(
        'id'            => 'generate_shortcode',
        'title'         => __( 'Generate Shortcode', 'cmb2' ),
        'object_types'  => array( 'ccpw'), // Post type
        'context'       => 'normal',
        'priority'      => 'low',
        'show_names'    => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // Keep the metabox closed by default
    ) );

    $cmb2 = new_cmb2_box( array(
      'id'            => 'live_preview',
      'title'         => __( 'Crypto Widget Live Preview', 'cmb2' ),
      'object_types'  => array( 'ccpw'), // Post type
      'context'       => 'normal',
      'priority'      => 'high',
      'show_names'    => true, // Show field names on the left
      // 'cmb_styles' => false, // false to disable the CMB stylesheet
      // 'closed'     => true, // Keep the metabox closed by default
  ) );
    $cmb->add_field( array(
    'name'    => 'Type<span style="color:red;">*</span>',
    'id'      => 'type',
    'type'    => 'select',
    'options' => array(
      'binance-live-widget' => __('Binance Live Widget', 'cmb2'),
      'ticker' => __('Ticker', 'cmb2'),
      'accordion-block' => __('Accordion Block', 'cmb2'),
      'price-block' => __('Price Block', 'cmb2'),
      'price-card'   => __( 'Price Card', 'cmb2' ),
      'price-label'   => __( 'Price Label', 'cmb2' ),
      'list-widget' => __('List Widget', 'cmb2'),
      'slider-widget' => __('Slider Widget', 'cmb2'),
      'table-widget' => __('Advanced Table', 'cmb2'),
      'multi-currency-tab'   => __( 'Multi Currency Tabs', 'cmb2' ),
      'chart' => __('Chart', 'cmb2'),
      'calculator' => __('Currency Convertor Calculator', 'cmb2'),
      'changelly-widget' => 'Changelly Exchange Widget',
      'rss-feed' => __('News Feed', 'cmb2'),
      'technical-analysis' => __('Technical Analysis','cmb2'),
      // 'quick-stats' => __('Quick Stats','cmb2'),
    //  'price-button' => __('Price Button Block', 'cmb2'),
    ),
    'default' => 'ticker',
    ) );

    $cmb->add_field( array(
      'name'    => 'Designs',
      'desc'    => 'Select designs for "Binance Live Widget"',
      'id'      => 'design_binance_live_widget',
      'type'    => 'select',    
      'options' => array(
          'style-1'   => __( 'Style 1', 'cmb2' ),	
          'style-2'   => __( 'Style 2 (With Chart)', 'cmb2' ),
      ),
      'default' => 'style-1',
      'attributes' => array(
              'required' => true,
              'data-conditional-id'    =>'type',
              'data-conditional-value' =>'binance-live-widget',
        )
    ) 
  );

    $cmb->add_field(array(
      'name'=>'Add currency pair',
      'id'=>'currency_pairs',
      'type'=>'textarea',
      'default'=>'BTC/USDT,ETH/USDT,XRP/USDT',
      'desc'=>'Make sure you keep the format Base/Quote with comma seprated. Ex: <code>BTC/USDT,ETH/USDT</code><br/>Check coin pairs availaibility on <a href="https://www.binance.com/en/markets" target="_new">Binance.com</a>',
      'attributes'=>array(
        'required' =>true,        
        'data-conditional-id' =>'type',
        'data-conditional-value' =>'binance-live-widget',          
      )
    )
    );

    $cmb->add_field(
      array(
          'name' => 'Show Coins <span style="color:red;">*</span>',
          'id' => 'show-coins',
          'type' => 'select',
          'default'=>'5',
          'options' => array(
              'custom' => 'Custom List',
               5 => 'Top 5',
               10 => 'Top 10',
               20 => 'Top 20',
               30 => 'Top 30',
               50 => 'Top 50',
               100 => 'Top 100',
          ),
          'attributes' => array(
            'required' => true,
            'data-conditional-id' => 'type',
            'data-conditional-value' => json_encode(
              array('price-block',
              'price-card', 
              'price-label', 
              'list-widget',
               'ticker',
               'chart', 
               'multi-currency-tab',
               'slider-widget',
               'accordion-block',
               'price-button',
               ))
          )
      ));
 
      $cmb->add_field(array(
        'name' => 'Select CryptoCurrencies<span style="color:red;">*</span>',
        'id' => 'display_currencies',
        'desc' => 'Select CryptoCurrencies',
        'type' => 'pw_multiselect',
        'options' =>$crypto_arr,
        'attributes' => array(
          'required' =>false,        
          'data-conditional-id' =>'show-coins',
          'data-conditional-value' =>'custom',
        )
      )); 



    $cmb->add_field( array(
      'name'=> 'Currency',
      'desc'=> '',
      'id'=>'quick_stats_currency',
      'type'=>'select',
      'default'=>'en',
      'options'=> $crypto_arr,
      'attributes' => array(
        'required' => false,        
        'data-conditional-id'    =>'type',
        'data-conditional-value' =>json_encode(array('quick-stats')),
      )
      )
    );
    $cmb->add_field( array(
      'name'=> 'Language',
      'desc'=> '',
      'id'=>'tw_locale',
      'type'=>'select',
      'default'=>'en',
      'options'=> array(
        'en'=>__('English','cmb2'),
        'uk'=>__('English (UK)','cmb2'),
        'in'=>__('English (IN)','cmb2'),
        'de_DE'=>__('Deutsch','cmb2'),
        'fr'=>__('Français','cmb2'),
        '1D'=>__('Español','cmb2'),
        'it'=>__('Italiano','cmb2'),
        'pl'=>__('Polski','cmb2'),
        'sv_SE'=>__('Svenska','cmb2'),
        'tr'=>__('Türkçe','cmb2'),
        'ru'=>__('Русский','cmb2'),
        'br'=>__('Português','cmb2'),
        'id'=>__('Bahasa Indonesia','cmb2'),
        'ms_MY'=>__('Bahasa Melayu','cmb2'),
        'th_TH'=>__('ภาษาไทย','cmb2'),
        'vi_VN'=>__('Tiếng Việt','cmb2'),
        'ja'=>__('日本語','cmb2'),
        'kr'=>__('한국어','cmb2'),
        'zh_CN'=>__('简体中文','cmb2'),
        'zh_TW'=>__('繁體中文','cmb2'),
        'ar_AE'=>__('العربية','cmb2'),
        'he_IL'=>__('עברית','cmb2'),
      ),
      'attributes'=>array(
        'data-conditional-id'=>'type',
        'data-conditional-value'=>'technical-analysis'
      )
    )
  );
  $cmb->add_field( array(
    'name'=> 'Currency',
    'desc'=> 'Select cryptocurrency to view technical analysis data.',
    'id'=>'tw_symbol',
    'type'=>'select',
    'default'=>'BITSTAMP:BTCUSD',
    'options'=> array(
      'BITFINEX:BTCUSD'=>__('Bitcoin / Dollar','cmb2'),
      'BITFINEX:XRPUSD'=>__('XRP / Dollar','cmb2'),
      'BITFINEX:ETHUSD'=>__('Ethereum / Dollar','cmb2'),
      'BITFINEX:LTCUSD'=>__('Litecoin / Dollar','cmb2'),
      'BITFINEX:EOSUSD'=>__('EOS / Dollar','cmb2'),
      'BITFINEX:BTCUSDSHORTS'=>__('BTCUSD Shorts','cmb2'),
      'BITFINEX:NEOUSD'=>__('NEO / Dollar','cmb2'),
      'BITFINEX:BTCUSDLONGS'=>__('BTCUSD Longs','cmb2'),
      'BITFINEX:IOTUSD'=>__('IOTA / Dollar','cmb2'),
      'BITFINEX:ETHBTC'=>__('Ethereum / Bitcoin','cmb2'),
      'BITFINEX:BSVUSD'=>__('BSV / Dollar','cmb2'),
      'BITFINEX:ZECUSD'=>__('Zcash / Dollar','cmb2'),
      'BITFINEX:XMRUSD'=>__('Monero / Dollar','cmb2'),
      'BITFINEX:XRPBTC'=>__('XRP / Bitcoin','cmb2'),
      'BITFINEX:TRXUSD'=>__('TRON / Dollar','cmb2'),
      'BITFINEX:OMGUSD'=>__('OmiseGo / Dollar','cmb2'),
      'BITFINEX:LTCBTC'=>__('Litecoin / Bitcoin','cmb2'),
      'BITFINEX:ETCUSD'=>__('Ethereum Classic / Dollar','cmb2'),
      'BITFINEX:XLMUSD'=>__('XLM / Dollar','cmb2'),
      'BITFINEX:BABUSD'=>__('BAB / Dollar','cmb2'),
      'BITFINEX:LEOUSD'=>__('LEO / Dollar','cmb2'),
      'BITFINEX:EOSBTC'=>__('EOS / Bitcoin','cmb2'),
      'BITFINEX:BTGUSD'=>__('Bitcoin Gold / Dollar','cmb2'),
      'BITFINEX:DSHUSD'=>__('Dashcoin / Dollar','cmb2'),
      'BITFINEX:ZRXUSD'=>__('0x / Dollar','cmb2'),
      'BITFINEX:ETPUSD'=>__('ETP / Dollar','cmb2'),
      'BITFINEX:IOTBTC'=>__('IOTA / Bitcoin','cmb2'),
      'BITFINEX:XTZBTC'=>__('XTZ / Bitcoin','cmb2'),
      'BITFINEX:XTZUSD'=>__('XTZ / Dollar','cmb2'),
      'BITFINEX:BTCEUR'=>__('Bitcoin / EUR','cmb2'),
      'BITFINEX:BSVBTC'=>__('BSV / Bitcoin','cmb2'),
      'BITFINEX:BTTUSD'=>__('BTT / Dollar','cmb2'),
      'BITFINEX:TRXBTC'=>__('TRON / Bitcoin','cmb2'),
      'BITFINEX:BATUSD'=>__('Basic Attention Token / Dollar','cmb2'),
      'BITFINEX:BTCTRY'=>__('Bitcoin / Turkish Lira','cmb2'),
      'BITFINEX:NEOBTC'=>__('NEO / Bitcoin','cmb2'),
      'BITFINEX:BTTBTC'=>__('BTT / Bitcoin','cmb2'),
      'BITFINEX:ZECBTC'=>__('Zcash / Bitcoin','cmb2'),
      'BITFINEX:VETUSD'=>__('VET / Dollar','cmb2'),
      'BITFINEX:LEOBTC'=>__('LEO / Bitcoin','cmb2'),
      'BITFINEX:BTCTHB'=>__('Bitcoin / Thai Baht','cmb2'),
      'BITFINEX:ETHUSDSHORTS'=>__('ETHUSD Shorts','cmb2'),
      'BITFINEX:QTMUSD'=>__('Qtum / Dollar','cmb2'),
      'BITFINEX:BTCJPY'=>__('Bitcoin / JPY','cmb2'),
      'BITFINEX:XMRBTC'=>__('Monero / Bitcoin','cmb2'),
      'BITFINEX:XLMBTC'=>__('XLM / Bitcoin','cmb2'),
      'BITFINEX:VETBTC'=>__('VET / Bitcoin','cmb2'),
      'BITFINEX:ETCBTC'=>__('Ethereum Classic / Bitcoin','cmb2'),
      'BITFINEX:XVGUSD'=>__('XVG / Dollar','cmb2'),
      'BITFINEX:ETHUSDLONGS'=>__('ETHUSD Longs','cmb2'),
    ),
    'attributes'=>array(
      'data-conditional-id'=>'type',
      'data-conditional-value'=>'technical-analysis'
    )
  )
);
    $cmb->add_field( array(
        'name'=> 'Interval',
        'desc'=> 'Time interval to find coin buy/sell ratio (Fear & Greed Index)',
        'id'=>'tw_interval_time',
        'type'=>'select',
        'options'=> array(
          '1m'=>__('1 Minute','cmb2'),
          '5m'=>__('5 Minutes','cmb2'),
          '15m'=>__('15 Minutes','cmb2'),
          '1h'=>__('1 Hour','cmb2'),
          '4h'=>__('4 Hour','cmb2'),
          '1D'=>__('1 Day','cmb2'),
          '1W'=>__('1 Week','cmb2'),
          '1M'=>__('1 Month','cmb2'),
        ),
        'default'=>'1h',
        'attributes'=>array(
          'data-conditional-id'=>'type',
          'data-conditional-value'=>'technical-analysis'
        )
    )
  );
  $cmb->add_field( array(
    'name'=> 'Hide Interval tab',
    'desc'=> 'Hide/remove the interval tab from the top of the widget.',
    'id'=>'tw_hide_interval_tab',
    'type'=>'checkbox',
    'attributes'=>array(
      'data-conditional-id'=>'type',
      'data-conditional-value'=>'technical-analysis'
    )
)
);
      $cmb->add_field( array(
        'name'=> 'Color Theme',
        'desc'=> 'Color Theme',
        'id'=>'tw_color_theme',
        'type'=>'select',
        'options'=> array(
          'light'=>__('Light','cmb2'),
          'dark'=>__('Dark','cmb2'),
        ),
        'default'=>'light',
        'attributes'=>array(
          'data-conditional-id'=>'type',
          'data-conditional-value'=>'technical-analysis'
        )
    )
    );
    $cmb->add_field( array(
      'name'=> 'Transparent Background',
      'desc'=> 'Transparent Background',
      'id'=>'tw_transparent_bg',
      'type'=>'checkbox',
      'attributes'=>array(
        'data-conditional-id'=>'type',
        'data-conditional-value'=>'technical-analysis'
      )
  )
  );
  $cmb->add_field( array(
    'name'=> 'Autosize',
    'desc'=> 'Check if you want to auto adjust the widget according to availabel size',
    'id'=>'tw_auto',
    'default'=>ccpwp_set_checkbox_default_for_new_post('on'),
    'type'=>'checkbox',
    'attributes'=>array(
      'data-conditional-id'=>'type',
      'data-conditional-value'=>'technical-analysis'
    )
)
);
  $cmb->add_field( array(
    'name'=> 'Width',
    'desc'=> 'Enter the width for the widget. If "Autosize" is enabled, this value will be ignored.',
    'id'=>'tw_width',
    'type'=>'text',
    'default'=>'425',
    'attributes'=>array(
      'type' => 'number',
      'data-conditional-id'=>'type',
      'data-conditional-value'=>'technical-analysis'
    )
)
);
$cmb->add_field( array(
  'name'=> 'Height',
  'desc'=> 'Enter the height for the widget. If "Autosize" is enabled, this value will be ignored.',
  'id'=>'tw_height',
  'type'=>'text',
  'default'=>'450',
  'attributes'=>array(
    'type' => 'number',
    'data-conditional-id'=>'type',
    'data-conditional-value'=>'technical-analysis'
  )
)
);
  $cmb->add_field( array(
      'name'    => 'Designs',
      'desc'    => 'Select designs for "Price Block"',
      'id'      => 'design_block',
      'type'    => 'select',
    
      'options' => array(
          'style-1'   => __( 'Style 1 (Accordion)', 'cmb2' ),	
          'style-2'   => __( 'Style 2 (Rank Block)', 'cmb2' ), //   With coin logo
          'style-3'   => __( 'Style 3 (Clean Block)', 'cmb2' ), //   Without coin logo
          'style-4'   => __( 'Style 4 (Simple Block)', 'cmb2' ),
          'style-5' => __( 'Style 5 (Big Block)', 'cmb2' )
      ),
      'default' => 'style-1',
      'attributes' => array(
              'required' => true,
              'data-conditional-id'    =>'type',
              'data-conditional-value' =>'price-block',
        )
    ) 
  );

  $cmb->add_field( array(
    'name'    => 'Designs',
     'desc'    => 'Select designs for "Ticker"',
    'id'      => 'design_ticker',
    'type'    => 'select',
    'default' => 'style-4',
  
    'options' => array(
        'style-1'   => __( 'Style 1 (With Tool-Tip)', 'cmb2' ),			//  With Tooltip
        'style-2'   => __( 'Style 2 (Without Tool-Tip)', 'cmb2' ), 			//   Without tooltip
         'style-3' => __( 'Style 3 (With 7D Chart)', 'cmb2' ), 			//   with chart
       'style-4' => __( 'Style 4 (Large Ticker)', 'cmb2' ),
       'style-5' => __( 'Style 5 (Large Ticker + 7D Chart)', 'cmb2' ),
    ),
    'default' => 'style-1',
   'attributes' => array(
           'required' => true,        
            'data-conditional-id'    =>'type',
            'data-conditional-value' =>json_encode(array('ticker')),
     )
  ) );

     $cmb->add_field( array(
   'name'    => 'Designs',
    'desc'    => 'Select designs for"List Widget" , "Price Label"',
   'id'      => 'design',
   'type'    => 'select',
   'default' => 'style-4',
 
   'options' => array(
       'style-1' => __( 'Style 1', 'cmb2' ),          	 
       'style-2' => __( 'Style 2', 'cmb2' ),			
       'style-3' => __( 'Style 3', 'cmb2' ),			   
       'style-4' => __( 'Style 4(Live Changes)', 'cmb2' ),
	   'style-5' => __( 'Style 5(Multicurrency)', 'cmb2' ),	
   ),
   'default' => 'style-1',
  'attributes' => array(
          'required' => true,        
           'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode(array('price-label','list-widget')),
    )
   ) );
   
     $cmb->add_field( array(
   'name'    => 'Designs',
    'desc'    => 'Select designs for "Price Card"',
   'id'      => 'design_card',
   'type'    => 'select',
    'show_option_none' => false,
    'options' => array(
      'style-1' => __( 'Style 1 (Simple Card)', 'cmb2' ),
      'style-2' => __( 'Style 2 (Detail Card)', 'cmb2' ),
      'style-3' => __( 'Style 3 (% Changes Card)', 'cmb2' ),
      'style-4' => __( 'Style 4 (Live Changes)', 'cmb2' ),
      // 'style-5' => __( 'Style 5(Slider view)', 'cmb2' ),
      'style-6' => __( 'Style 5 (Chart Card)', 'cmb2' ),
     'style-7' => __( 'Style 6 (Multicurrency)', 'cmb2' ),
  ),
   'default' => 'style-4',
  'attributes' => array(
          'required' => true,        
           'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode(array('price-card')),
    )
   ) );

   $cmb->add_field( array(
    'name'    => 'Designs',
     'desc'    => 'Select designs for "Slider Widget"',
    'id'      => 'design_slider',
    'type'    => 'select',
    'default' => 'style-1',
     'show_option_none' => false,
    'options' => array(
        'style-1' => __( 'Style 1 (Simple Slider)', 'cmb2' ),
        'style-2' => __( 'Style 2 (No-space Slider)', 'cmb2' ),
        'style-3' => __( 'Style 3 (Chart Slider)', 'cmb2' ),
    ),
   'attributes' => array(
           'required' => true,        
            'data-conditional-id'    =>'type',
            'data-conditional-value' =>json_encode(array('slider-widget')),
     )
    ) );

    $cmb->add_field( array(
      'name'    => 'Accordion Block Designs',
       'desc'    => 'Select Accordion Block Designs"',
      'id'      => 'accordion-block-design',
      'type'    => 'select',
       'show_option_none' => false,
      'options' => array(
          'style-1' => __( 'Style 1(Default)', 'cmb2' ),
          'style-2' => __( 'Style 2(Live Changes)', 'cmb2' ),
          'style-3' => __( 'Style 3(All Info)', 'cmb2' ),
      ),
      'default' => 'style-1',
     'attributes' => array(
             'required' => true,        
              'data-conditional-id'    =>'type',
              'data-conditional-value' =>json_encode(array('accordion-block','price-button')),
       )
      ) );


	
     $cmb->add_field( array(
   'name'    => 'Where Do You Want to Display Ticker?',
    'desc'    => '</br></br>Select the option where you want to display ticker (No need to add shortcode on pages for Header/Footer)',
   'id'      => 'ticker_position',
   'type'    => 'radio_inline',
   
   'options' => array(
       'header'   => __( 'Header', 'cmb2' ),
       'footer'   => __( 'Footer', 'cmb2' ),
        'shortcode' => __( 'Anywhere', 'cmb2' ),
   ),
   'default' => 'no',
   'attributes' => array(
          'required' => true,        
           'data-conditional-id'    =>'type',
           'data-conditional-value' =>'ticker',
    )
   ) );
	
$cmb->add_field(array(
  'name' => 'Display Charts?<br> (only for style-1)',
  'desc' => 'Select if you want to display 7 days price charts( only for style 1)',
  'id' => 'display_list_price_chart',
  'type' => 'checkbox',
  'attributes' => array(
    'data-conditional-id' => 'type',
    'data-conditional-value' => json_encode(array('list-widget')),
  )
));

   $cmb->add_field( array(
    'name'             => 'Select Column',
    'desc'             => '',
    'id'               => 'column',
    'type'             => 'select',
    'show_option_none' => false,
    'options'          =>array(
	   "col-md-12"=>"1 Column",
       "col-md-6"=>"2 Column",
       "col-md-4"=>"3 Column",
       "col-md-3"=>"4 Column",
       "col-md-2"=>"6 Column",
     ),
       'attributes' => array(
          'required' => true,        
           'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode( array( 'price-card','slider-widget') ),
    ),
     'default' => 'col-md-4'
    ) );
   
    // Column settings for price-block style only
    $cmb->add_field( array(
      'name'             => 'Select Column',
      'desc'             => '',
      'id'               => 'block_column',
      'type'             => 'select',
      'show_option_none' => false,
      'options'          =>array(
       "col-md-12"=>"1 Column",
         "col-md-6"=>"2 Column",
         "col-md-4"=>"3 Column",
         "col-md-3"=>"4 Column",
         "col-md-2"=>"6 Column",
       ),
         'attributes' => array(
            'required' => true,
             'data-conditional-id'    =>'design_block',
             'data-conditional-value' =>json_encode( array( 'style-2', 'style-3', 'style-4' ) ),
      ),
       'default' => 'col-md-4'
      ) );

  $cmb->add_field( array(
   'name'    => 'Speed of Ticker',
    'desc'    => 'Low value = high speed. (Best between 10 - 60)',
   'id'      => 'ticker_speed',
   'type'    => 'text',
   'default' => '20',
   'attributes' => array(
      'required' => true,        
      'data-conditional-id'    =>'type',
      'data-conditional-value' =>json_encode(array('ticker')),
    )
   ) );
   $cmb->add_field( array(
    'name'    =>"Disable from Pages (Page id's)",
    'desc'    => 'Enter page id where you don\'t want to display ticker',
    'id'      => 'disable_from_pages',
    'type'    => 'text',
    'attributes' => array(
      'required' => false,        
      'data-conditional-id'    =>'type',
      'data-conditional-value' =>json_encode(array('ticker','rss-feed')),
    )
) ); 
 

$cmb->add_field(
  array(
    'name'    =>  'Show Coins<span style="color:red;">*</span>',
    'id'      =>  'display_currencies_for_table',
    'type'    =>  'select',
    'options' =>  array(
      'top-10'    =>'Top 10',
      'top-20'    =>'Top 20',
      'top-50'    =>'Top 50',
      'top-100'   =>'Top 100',
      'top-200'   =>'Top 200',
      'all'       =>'All'
    )
  ,
  'attributes' => array(
    'data-conditional-id' => 'type',
    'data-conditional-value' =>'table-widget',
  )
));

$cmb->add_field(
  array(
    'name'    =>  'Records Per Page',
    'id'      =>  'pagination_for_table',
    'type'    =>  'select',
    'options' =>  array(
          '10'   =>'10',
          '25'   =>'25',
          '50'   =>'50',
          '100'  =>'100'
    )
  ,
  'attributes' => array(
    'data-conditional-id' => 'type',
    'data-conditional-value' => json_encode(array('table-widget')),
  )
));

 //select currency
    $cmb->add_field( array(
	'name'             => 'Select Currency',
	'id'               => 'currency',
	'type'             => 'select',
	'show_option_none' => false,
  'options'          => $currencies_arr,
  'default' => 'USD',
	  'attributes' => array(
      'required' => true,
      'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode(
             array('price-card',
             'price-label',
             'list-widget',
             'ticker',
             'table-widget',
             'accordion-block',
             'price-button'
             ))
    )
  ) );
  
   //select currency
   $cmb->add_field( array(
    'name'             => 'Select Currency',
    'desc'             => '',
    'id'               => 'slider_widget_currency',
    'type'             => 'select',
    'show_option_none' => false,
    'options'          => $currencies_arr,
    'default' => 'USD',
      'attributes' => array(
        'required' => true,
        'data-conditional-id'    =>'type',
             'data-conditional-value' =>json_encode(array('slider-widget'))
      )
    ) );

 //select currency
$cmb->add_field(array(
  'name' => 'Select Currency',
  'desc' => '<span style="color:red"><b>(This option is not for Price Block - Style 5 (Big Block))</b></span>',
  'id' => 'price_block_currency',
  'type' => 'select',
  'show_option_none' => false,
  'options' => $currencies_arr,
  'default' => 'USD',
  'attributes' => array(
    'required' => true,
    'data-conditional-id' => 'type',
    'data-conditional-value' => json_encode(array( 'price-block'))
  )
));
  

  $cmb->add_field( array(
    'name'    => 'Enable Live Changes',
    'desc'    => 'Enable Live Changes',
    'id'      => 'live_changes',
    'type'    => 'checkbox',
      'attributes' => array(      
        'data-conditional-id'    =>'type',
        'data-conditional-value' =>json_encode(
          array('ticker','table-widget'))
      )
   ) );
   
    $cmb->add_field( array(
    'name'    => 'Disable Ticker in Mobile',
    'desc'    => 'Select if you do not want to Display Ticker in Mobile',
    'id'      => 'ticker_in_mobile',
	'default' => false,
    'type'    => 'checkbox',
      'attributes' => array(      
        'data-conditional-id'    =>'type',
        'data-conditional-value' =>json_encode(array('ticker')),
      )
   ) );

   $cmb->add_field( array(
    'name'    => 'Disable Ticker in Mobile',
    'desc'    => 'Select if you do not want to Display News Ticker in Mobile',
    'id'      => 'rss_ticker_in_mobile',
	'default' => false,
    'type'    => 'checkbox',
      'attributes' => array(      
        'data-conditional-id'    =>'rss_style',
        'data-conditional-value' =>json_encode(array('ticker-rss')),
      )
   ) );

    // Hourly changes settings for price-label and list-widget
    $cmb->add_field( array(
    'name' => 'Display changes? (Optional)',
    'desc' => 'Select if you want to display 24 Hour <b>%</b> changes in price?<br/><span style="color:red"><b>(This option is not for Price Label: design - Style 5 and List Widget: design - Style 4)</b></span>',
    'id'   => 'label_list_display_changes',
    'type' => 'checkbox',
    'default' => ccpwp_set_checkbox_default_for_new_post( true ),
    'attributes' => array(
      'data-conditional-id'    =>'type',
            'data-conditional-value' =>json_encode(array('price-label','list-widget')),
    )
    ) );

   $cmb->add_field( array(
    'name' => 'Display changes? (Optional)',
    'desc' => 'Select if you want to display 24 Hour <b>%</b> changes in price?',
    'id'   => 'card_display_changes',
    'type' => 'checkbox',
    'default' => ccpwp_set_checkbox_default_for_new_post( true ),
    'attributes' => array(  
      'data-conditional-id'    =>'design_card',
      'data-conditional-value' =>json_encode( array('style-1','style-2','style-3','style-6',) ),
    )
    ) );

   $cmb->add_field( array(
    'name' => 'Display changes? (Optional)',
    'desc' => 'Select if you want to display 24 Hour <b>%</b> changes in price.<br/>Only for slider widget: style-1',
    'id'   => 'slider_display_changes',
    'type' => 'checkbox',
    'default' => ccpwp_set_checkbox_default_for_new_post( true ),
    'attributes' => array(  
      'data-conditional-id'    =>'type',
      'data-conditional-value' =>json_encode( array('design_slider') )
    )
    ) );

    $cmb->add_field( array(
    'name' => 'Display changes? (Optional)',
    'desc' => 'Select if you want to display 24 Hour <b>%</b> changes in price.',
    'id'   => 'block_display_changes',
    'type' => 'checkbox',
    'default' => ccpwp_set_checkbox_default_for_new_post( true ),
    'attributes' => array(
      'data-conditional-id'    =>'design_block',
      'data-conditional-value' =>json_encode( array( 'style-2', 'style-3', 'style-4' ) ),
    )
    ) );

    // Hourly changes settings
    $cmb->add_field( array(
      'name' => 'Display changes? (Optional)',
      'desc' => 'Select if you want to display 24 Hour <b>%</b> changes in price?',
      'id'   => 'display_changes',
      'type' => 'checkbox',
      'default' => ccpwp_set_checkbox_default_for_new_post( true ),
      'attributes' => array(
        'data-conditional-id'    =>'type',
             'data-conditional-value' =>json_encode(array('ticker','multi-currency-tab')),
      )
      ) );

	$cmb->add_field( array(
    'name' => 'Enable Number Formatting? (Optional)',
    'desc' => 'Select if you want to enable number formatting (Million/Billion)',
    'id'   => 'display_format',
    'type' => 'checkbox',
    'attributes' => array(
      'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode(array(
             'table-widget',
             'price-block',
             'price-card',
             'price-label',
             'list-widget',
             'ticker',
             'slider-widget',
             'accordion-block',
             'price-button'
            )),
    )
        ) );
	

  $cmb->add_field( array(
   'name'    => 'Background Color',
   'desc'    => 'Select background color',
   'id'      => 'back_color',
   'type'    => 'colorpicker',
   'default' => '#eee',
   'attributes' => array(
      'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode(array('calculator',
           'binance-live-widget',
           'price-block','price-card','price-label',
           'list-widget','ticker','rss-feed',
           'multi-currency-tab',
           'slider-widget',
           'accordion-block',
             'price-button'   
          
          )),
    )
   ) );
  
    $cmb->add_field( array(
   'name'    => 'Font Color',
   'desc'    => 'Select font color',
   'id'      => 'font_color',
   'type'    => 'colorpicker',
   'default' => '#000',
   'attributes' => array(
      'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode(array('calculator',
           'binance-live-widget',
           'price-block','price-card','price-label',
           'list-widget','ticker',
           'rss-feed','multi-currency-tab',
           'slider-widget',
           'accordion-block',
           'price-button'  
          )),
    )
   ) );

   $cmb->add_field(array(
    'name' => 'Chart Color',
    'desc' => 'chart color for binance live widget',
    'id' => 'binance_chart_color',
    'type' => 'colorpicker',
    'default' => '#a8e1ee',
    'attributes' => array(
      'data-conditional-id' => 'design_binance_live_widget',
      'data-conditional-value' => 'style-2',
    )
  ));
 
 $both_currencies=array_merge($currencies_arr,$crypto_arr);
$cmb->add_field(array(
  'name' => 'Default Base CryptoCurrency',
  'id' => 'cal_base_currency',
  'desc' => 'Select CryptoCurrency',
  'type' => 'pw_select',
  'options' =>$crypto_arr,
  'default' => 'bitcoin',
  'attributes' => array(
    'required' => false,
    'data-conditional-id' => 'type',
    'data-conditional-value' => json_encode(array('calculator'))
  )

)); 
 $cmb->add_field( array(
	'name'             => 'Default Target Fiat Currency or CryptoCurrency',
	'desc'             => 'Select Fiat Currency or CryptoCurrency',
	'id'               => 'cal_target_currency',
	'type'             => 'pw_select',
	'show_option_none' => false,
  'options'          => $both_currencies,
  'default' => 'USD',
	  'attributes' => array(
      'required' => true,
      'data-conditional-id'    =>'type',
           'data-conditional-value' =>json_encode(array('calculator'))
    )
  ) );

   $cmb->add_field( array(
    'name'    => 'Chart Color',
	'desc' => 'Select chart color. This color will override dynamic chart red/green colors.<br/><span style="color:red"><b>(This option is not for Price Block - Style 5 (Big Block))</b></span>',
    'id'      => 'block_chart_color',
    'type'    => 'colorpicker',
    'default' => '',
    'attributes' => array(
        'data-conditional-id'    =>'type',
            'data-conditional-value' =>json_encode(array('price-block','accordion-block',
             )),
      )
    ) );

    $cmb->add_field( array(
    'name'    => 'Enable Chart Background Color',
    'desc' => 'Select this option to enable chart background color.',
      'id'      => 'block_chart_fill',
      'type'    => 'checkbox',
      'default' => '',
      'attributes' => array(
          'data-conditional-id'    =>'type',
              'data-conditional-value' =>json_encode(array('price-block')),
        )
      ) );

    $cmb->add_field( array(
      'name'    => 'Chart Color',
    'desc' => 'Select chart color. This color will override dynamic chart red/green colors.<br/><span style="color:red"><b>(This option is only for Slider Widget - Style 3 (Chart Slider))</b></span>',
      'id'      => 'slider_chart_color',
      'type'    => 'colorpicker',
      'default' => '',
      'attributes' => array(
          'data-conditional-id'    =>'type',
              'data-conditional-value' =>json_encode(array('slider-widget')),
        )
      ) );

   $cmb->add_field( array(
    'name'    => 'Autoplay Slider',
    'desc'    => 'Select if you want the slider to autoplay.',
    'id'      => 'slider_autoplay',
    'type'    => 'checkbox',
    'default' => ccpwp_set_checkbox_default_for_new_post( true ),
    'attributes' => array(
       'data-conditional-id'    =>'type',
            'data-conditional-value' =>json_encode(array('slider-widget')),
     )
    ) );

    $cmb->add_field(array(
      'name' => 'Chart Type',
      'id' => 'main_chart_type',
      'desc' => 'Select Chart Type',
      'type' => 'select',
      'options' =>array('default'=>'Default','cryptocompare'=>'3rd Party(CryptoCompare)','tradingview'=>'3rd Party(Trading View)'),
      'attributes' => array(
        'required' => false,
        'data-conditional-id' => 'type',
        'data-conditional-value' => json_encode(array('chart'))
      )
    
    ));

    $cmb->add_field( array(
   'name'    => 'Chart Height (in px)',
    'desc'    => 'Specify chart height in pixels',
   'id'      => 'chart_height',
   'type'    => 'text',
   'default' => '400',
   'attributes' => array(
      'data-conditional-id'    =>'type',
      'data-conditional-value' =>'chart',
     )
    ) );

$cmb->add_field(array(
  'name' => 'Chart Color',
  'desc' => '',
  'id' => 'chart_color',
  'type' => 'colorpicker',
  'default' => '#2196F3',
  'attributes' => array(
    'data-conditional-id' => 'type',
    'data-conditional-value' => json_encode( array('chart') ),
  )
));


$cmb->add_field( array(
  'name'    => 'How to create changelly widget',
  'id'      => 'create_changely_widget',
  'type'    => 'select',
  'desc'   =>'<style>
  select#create_changely_widget{
    display:none;
  }
.cmb2-wrap ul{
  list-style:disc;margin-left:15px;
}
  .cmb2-wrap ul li{
      float:none;
      width:100%;
    }</style><ul >
  <li>Visit <a href="https://changelly.com/" target="_new">changelly.com</a> and register an account or login if you already have an account.</li>
  <li>Select <strong>Our solutions</strong> from the top menu and click on <strong>Exchange widget</strong>.</li>
  <li>You can customize the widget by using settings available at the left side of the screen.</li>
  <li>Make sure you mention your domain ('.site_url().') on the textarea field below the destination address text field.</li>
  <li>Once you complete all the above steps successfully, copy the source code for widget and paste it on <strong>Changelly Widget Source Code</strong> in this settings page.</li>
  </ul>',
  'attributes' => array(
    'data-conditional-id'    =>'type',
    'data-conditional-value' =>'changelly-widget',
   )
  ) );

$cmb->add_field( array(
   'name'    => 'Changelly Widget Source Code<span style="color:red;">*</span>',
    'desc'    => '',
   'id'      => 'changelly_widget_sourcecode',
   'type'    => 'textarea_code',
   'options' => array( 'disable_codemirror' => true ),
   'attributes' => array(
      'required' => true,
      'data-conditional-id'    =>'type',
      'data-conditional-value' =>'changelly-widget',
     )
    ) );

	      /* settings for rss feed  */
        $cmb->add_field( array(
          'name'    => 'News Feed layout',
           'desc'    => '</br></br>Select the option in which you want to display News Feed',
          'id'      => 'rss_style',
          'type'    => 'radio_inline',
          'default' => 'ticker',
          'options' => array(
              'list-rss'   => __( 'List View', 'cmb2' ),
              'ticker-rss'   => __( 'Ticker', 'cmb2' ),
              // 'shortcode' => __( 'Anywhere', 'cmb2' ),
          ),
       
          'attributes' => array(
                 'required' => true,        
                  'data-conditional-id'    =>'type',
                  'data-conditional-value' =>'rss-feed',
           )
          ) );
          
          
          
             $cmb->add_field( array(
          'name'    => 'News Feed Url (First)',
           'desc'    => 'Enter Url for News Feed,<b>eg: https://cointelegraph.com/feed/</b>',
          'id'      => 'rss_url',
          'type'    => 'text',
          //'default' => '30',
          'attributes' => array(
             'required' => true,        
             'data-conditional-id'    =>'type',
             'data-conditional-value' =>'rss-feed',
           )
          ) );
          
           $cmb->add_field( array(
          'name'    => 'News Feed Url (Second)',
           'desc'    => 'Enter Url for News Feed,<b>eg: https://news.bitcoin.com/feed/</b>',
          'id'      => 'rss_url_second',
          'type'    => 'text',
        
          'attributes' => array(
             'required' => false,        
             'data-conditional-id'    =>'type',
             'data-conditional-value' =>'rss-feed',
           )
          ) );
           $cmb->add_field( array(
          'name'    => 'Number of News',
           'desc'    => 'Enter the number of  News to display in news feed',
          'id'      => 'rss_number_of_news',
          'type'    => 'text',
          'default' => '10',
          'attributes' => array(
            // 'required' => true,        
             'data-conditional-id'    =>'type',
             'data-conditional-value' =>'rss-feed',
           )
          ) );
          
           $cmb->add_field( array(
          'name'    => 'Description Length',
           'desc'    => 'Enter the number of words to display in news description',
          'id'      => 'rss_excerpt',
          'type'    => 'text',
          //'default' => 'list-rss',
          'attributes' => array(
            // 'required' => true,        
             'data-conditional-id'    =>'rss_style',
             'data-conditional-value' =>'list-rss',
           )
          ) );
          
           $cmb->add_field( array(
          'name'    => 'Read More Text',
           'desc'    => 'Enter the text in which do you want to display for read more link',
          'id'      => 'rss_excerpt_text',
          'type'    => 'text',
          'default' => 'Read More',
          'attributes' => array(
            // 'required' => true,        
             'data-conditional-id'    =>'rss_style',
             'data-conditional-value' =>'list-rss',
           )
          ) );
          
       
         $cmb->add_field( array(
          'name'    => 'Where Do You Want to Display News Feed Ticker?',
           'desc'    => '</br></br>Select the option where you want to display News Feed Ticker',
          'id'      => 'rss_ticker_position',
          'type'    => 'radio_inline',
          'default'=>'rss-footer',
          'options' => array(
              'rss-header'   => __( 'Header', 'cmb2' ),
              'rss-footer'   => __( 'Footer', 'cmb2' ),
              'rss-shortcode' => __( 'Anywhere', 'cmb2' ),
          ),
          'default' => 'rss-footer',
          'attributes' => array(
                 'required' => true,        
                  'data-conditional-id'    =>'rss_style',
                  'data-conditional-value' =>'ticker-rss',
           )
          ) ); 
          
          
          $cmb->add_field( array(
          'name'    => 'Speed of Ticker',
           'desc'    => 'Enter the speed of ticker (best between 10 - 50)',
          'id'      => 'rss_ticker_speed',
          'type'    => 'text',
          'default' => '30',
          'attributes' => array(
            'data-conditional-id'    =>'rss_style',
             'data-conditional-value' =>'ticker-rss',
           )
          ) );
         
$currency_ids = array(
  "BTC"=>"BTC",
  "USD"=>"USD",
  "AUD"=>"AUD",
  "BRL"=>"BRL",
  "CAD"=>"CAD",
  "CZK"=>"CZK",
  "DKK"=>"DKK",
  "EUR"=>"EUR",
  "HKD"=>"HKD",
  "HUF"=>"HUF",
  "ILS"=>"ILS",
  "INR"=>"INR",
  "JPY"=>"JPY",
  "MYR"=>"MYR",
  "NOK"=>"MYR",
  "PHP"=>"PHP",
  "PLN"=>"PLN",
  "GBP" =>"GBP",
  "SEK"=>"SEK",
  "CHF"=>"SEK",
  "TWD"=>"TWD",
  "THB"=>"THB",
  "TRY"=>"TRY",

);

    $cmb->add_field( array(
    'name'    => 'Display Curencies Tab',
    'desc'    => '',
    'id'      => 'mt-currencies',
    'type'    => 'multicheck',
    'options' =>$currency_ids,
    'default'=>array('USD','EUR','GBP','AUD','JPY'),
    'attributes' => array(
         // 'required' => true,        
           'data-conditional-id'    =>'type',
           'data-conditional-value' =>'multi-currency-tab',
    )
    ) );

    $cmb->add_field( array(
      'name'    => 'Disable Bootstrap',
      'desc'    => 'Select this option if you want to disable the bootstrap from widgets.',
      'id'      => 'disable_bootstrap',
      'type'    => 'checkbox',
      'default' => ccpwp_set_checkbox_default_for_new_post( false ),
      'attributes' => array(
        // 'required' => true,        
          'data-conditional-id'    =>'type',
          'data-conditional-value' =>json_encode(array('price-block','list-widget','price-card')),
      )
    ) );

   $cmb->add_field( array(
   'name'    => 'Custom CSS',
   'desc'    => 'Enter custom CSS',
   'id'      => 'custom_css',
   'type'    => 'textarea',
   'desc'    => 'Not for Technical Analysis',
   ) );
   


$cmb2->add_field( array(
	'name' => '',
	'desc' =>ccpwp_display_live_preview(),
	'type' => 'title',
	'id'   => 'live_preview'
) );

function ccpwp_display_live_preview(){
  $output='';
  if( isset($_REQUEST['post']) && !is_array($_REQUEST['post'])){
    $id = $_REQUEST['post'];
    $type = get_post_meta($id, 'type', true);
       $output='<p><strong class="micon-info-circled"></strong>Backend preview may be a little bit different from frontend / actual view. Add this shortcode on any page for frontend view - <code>[ccpw id='.$id.']</code></p>'.do_shortcode("[ccpw id='".$id."']");
       $output.='<script type="text/javascript">
       jQuery(document).ready(function($){
         $(".ccpw-ticker-cont").fadeIn();     
       });
       </script>
       <style type="text/css">
       .ccpw-footer-ticker-fixedbar, .ccpw-header-ticker-fixedbar{
         position:relative!important;
       }
       .ccpw-container-rss-view ul li.ccpw-news {
        margin-bottom: 30px;
        float: none;
        width: auto;
    }
    .ccpw-news-ticker .tickercontainer li{
      width: auto!important;
    }
       </style>';
       return $output;
   
     }else{
    return  $output='<h4><strong class="micon-info-circled"></strong> Publish to preview the widget.</h4>';

     }
}

function ccpwp_set_checkbox_default_for_new_post( $default ) {
  return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
}
    // Add other metaboxes as needed