<?php
/**
 * This file is responsible for all database realted functionality.
 */
class ccpwp_database
{

    /**
     * Get things started
     *
     * @access  public
     * @since   1.0
     */
    public function __construct()
    {

        global $wpdb;

        $this->table_name = $wpdb->base_prefix . 'cmc_coins_v2';
        $this->primary_key = 'id';
        $this->version = '1.0';
        $this->ccpw_refresh_database();
    }

    /**
     * Get columns and formats
     *
     * @access  public
     * @since   1.0
     */
    public function get_columns()
    {
        return array(
            'id' => '%d',
            'coin_id' => '%s',
            'name' => '%s',
            'symbol' => '%s',
            'price' => '%f',
            'percent_change_24h' => '%f',
            'market_cap' => '%f',
            'total_volume' => '%f',
            'weekly_price_data' => '%s',
            'extra_info' => '%s',
            'circulating_supply' => '%d',
        );
    }

    /*
    |-----------------------------------------------------------------------
    |    Call this function to insert/update data for single or multiple coin
    |-----------------------------------------------------------------------
     */
    public function ccpw_insert($coins_data)
    {
        if (is_array($coins_data) && count($coins_data) > 0) {
            return $this->wp_insert_rows($coins_data, $this->table_name, true, 'coin_id');
        }
    }

    /*
    |-------------------------------------------------------------------------------
    |    This function is used to insert/update weekly_data field in database table
    |-------------------------------------------------------------------------------
     */
    public function ccpw_insert_coin_weekly_data($coin_id, $data)
    {

        if (!empty($coin_id) && !empty($data)) {
            $raw_data['coin_id'] = $coin_id;
            $raw_data['weekly_price_data'] = empty($data) ? null : $data ;
            return $this->ccpw_insert(array($raw_data));
        }
    }

    /**
     * This function will remove the coins from database if any coin found after supplied number
     *
     * @param int Rows to be removed after this number
     */
    public function ccpw_refresh_database($number_of_coins = 2500)
    {

        if( get_option('cmc-dynamic-links') != false || get_option('cmc-dynamic-links') == "" ) return;

        global $wpdb;
        $table = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->table_name));
        if ($table == $this->table_name) {
            $wpdb->query($wpdb->prepare("DELETE FROM $this->table_name WHERE id > %d ", $number_of_coins));
        }

    }

    /**
     * Get default column values
     *
     * @access  public
     * @since   1.0
     */
    public function get_column_defaults()
    {
        return array(
            'coin_id' => '',
            'name' => '',
            'symbol' => '',
            'price' => '',
            'percent_change_24h' => '',
            'percent_change_1y' => '',
            'percent_change_30d' => '',
            'percent_change_7d' => '',
            'market_cap' => '',
            'total_volume' => '',
            'circulating_supply' => '',
            'weekly_price_data' => '',
            'extra_info' => '',
            'last_updated' => date('Y-m-d H:i:s'),
        );
    }

    public function get_current_coin_price($coin_id = null)
    {

        if ($coin_id != null) {
            global $wpdb;
            $data = $wpdb->get_var($wpdb->prepare("SELECT price FROM $this->table_name WHERE coin_id = '%s'", $coin_id));
            return $data;
        }
        return false;
    }

    public function get_coin_logo($coin_id)
    {
        global $wpdb;
        if ($coin_id == null) {
            error_log('Coin id missing');
        }

        $logo = $wpdb->get_var($wpdb->prepare("SELECT logo FROM $this->table_name WHERE coin_id = '%s' LIMIT 1", $coin_id));

        return $logo;
    }

    public function get_coin_weekly_data($coin_id)
    {
        global $wpdb;
        $response = $wpdb->get_var($wpdb->prepare("SELECT weekly_price_data FROM $this->table_name WHERE coin_id = '%s'", $coin_id));
        if (empty($response) || !is_string($response)) {
            return false;
        }

        $weekly_data = unserialize( $response);
        $lastIndex = count($weekly_data);

        $currentPrice = $this->get_current_coin_price($coin_id);
        if ($weekly_data[$lastIndex - 1] != $currentPrice) {
            array_push($weekly_data, $currentPrice);
        }

        return $weekly_data;
    }

    public function get_coin_extra_info_data($coin_id)
    {
        global $wpdb;
        $response = $wpdb->get_var($wpdb->prepare("SELECT weekly_price_data FROM $this->table_name WHERE coin_id = '%s'", $coin_id));
        if (empty($response) || !is_string($response)) {
            return false;
        }

        $weekly_data = explode(',', $response);

        $total = count($weekly_data);

        $OneDay = array_slice($weekly_data, $total - 23);
        $lastIndex = count($OneDay);

        // add current price at the end of the chart
        $currentPrice = $this->get_current_coin_price($coin_id);
        if ($OneDay[$lastIndex - 1] != $currentPrice) {
            array_push($OneDay, $currentPrice);
        }

        return $OneDay;

    }

    public function coin_exists_by_id($coin_id)
    {

        global $wpdb;
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE coin_id ='%s'", $coin_id));
        if ($count == 1) {
            return true;
        } else {
            return false;
        }

    }
    /**
     * Retrieve orders from the database
     *
     * @access  public
     * @since   1.0
     * @param   array $args
     * @param   bool  $count  Return only the total number of results found (optional)
     */
    public function get_coins($args = array(), $count = false)
    {

        global $wpdb;

        $defaults = array(
            'number' => 20,
            'offset' => 0,
            'id' => '',
            'coin_id' => '',
            'name' => '',
            'status' => '',
            'email' => '',
            'orderby' => 'market_cap',
            'market_cap'=>'',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);

        if ($args['number'] < 1) {
            $args['number'] = 999999999999;
        }

        $where = '';

        // specific referrals
        if (!empty($args['id'])) {

            if (is_array($args['id'])) {
                $order_ids = implode(',', $args['id']);
            } else {
                $order_ids = intval($args['id']);
            }

            $where .= "WHERE `id` IN( {$order_ids} ) ";

        }

         // where market cap larger then zero
         if (!empty($args['market_cap'])) {
            $where .= "WHERE `market_cap` !=0";

        }

        if (!empty($args['coin_id'])) {

            if (empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }

            if (is_array($args['coin_id'])) {
                $where .= " `coin_id` IN('" . implode("','", $args['coin_id']) . "') ";
            } else {
                $where .= " `coin_id` = '" . $args['coin_id'] . "' ";
            }

        }

        $args['orderby'] = !array_key_exists($args['orderby'], $this->get_columns()) ? $this->primary_key : $args['orderby'];

        if ('total' === $args['orderby']) {
            $args['orderby'] = 'total+0';
        } else if ('subtotal' === $args['orderby']) {
            $args['orderby'] = 'subtotal+0';
        }

        $cache_key = (true === $count) ? md5('ccpw_coins_count' . serialize($args)) : md5('ccpw_coins_' . serialize($args));

        $results = wp_cache_get($cache_key, 'coins');

        if (false === $results) {

            if (true === $count) {

                $results = absint($wpdb->get_var("SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};"));

            } else {

                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
                        absint($args['offset']),
                        absint($args['number'])
                    )
                );

            }

            wp_cache_set($cache_key, $results, 'coins', 3600);

        }

        return $results;

    }

    public function get_coin_id_by_symbol($coin_symbol = '')
    {
        global $wpdb;
        if (empty($coin_symbol)) {
            $response = $wpdb->get_results('SELECT coin_id,symbol FROM ' . $this->table_name . ';');
            $list = array();
            foreach ($response as $value) {
                $list[$value->symbol] = $value->coin_id;
            }
            return ccpwp_objectToArray($list);
        } else {
            if( is_array( $coin_symbol ) ){
                $coin_symbols = "'" . implode("','", $coin_symbol ) . "'";
                $response = $wpdb->get_results("SELECT coin_id FROM $this->table_name WHERE symbol IN ($coin_symbols)");
            }else{
                $coin_symbol = strtoupper($coin_symbol);
                $response = $wpdb->get_var("SELECT coin_id FROM $this->table_name WHERE symbol = '$coin_symbol'");
            }
            return $response;
        }
    }

    public function get_coins_listdata($args = array(), $count = false)
    {

        global $wpdb;

        $defaults = array(
            'number' => 20,
            'offset' => 0,
            'coin_id' => '',
            'name' => '',
            'status' => '',
            'email' => '',
            'orderby' => 'id',
            'order' => 'ASC',
        );

        $args = wp_parse_args($args, $defaults);

        if ($args['number'] < 1) {
            $args['number'] = 999999999999;
        }

        $where = '';

        // specific referrals
        if (!empty($args['id'])) {

            if (is_array($args['id'])) {
                $order_ids = implode(',', $args['id']);
            } else {
                $order_ids = intval($args['id']);
            }

            $where .= "WHERE `id` IN( {$order_ids} ) ";

        }

        if (!empty($args['coin_id'])) {

            if (empty($where)) {
                $where .= " WHERE";
            } else {
                $where .= " AND";
            }

            if (is_array($args['coin_id'])) {
                $where .= " `coin_id` IN('" . implode("','", $args['coin_id']) . "') ";
            } else {
                $where .= " `coin_id` = '" . $args['coin_id'] . "' ";
            }

        }

        $args['orderby'] = !array_key_exists($args['orderby'], $this->get_columns()) ? $this->primary_key : $args['orderby'];

        if ('total' === $args['orderby']) {
            $args['orderby'] = 'total+0';
        } else if ('subtotal' === $args['orderby']) {
            $args['orderby'] = 'subtotal+0';
        }

        $cache_key = (true === $count) ? md5('ccpw_coins_list_count' . serialize($args)) : md5('ccpw_coins_list_' . serialize($args));

        $results = wp_cache_get($cache_key, 'coins');

        if (false === $results) {

            if (true === $count) {

                $results = absint($wpdb->get_var("SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};"));

            } else {

                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT name,price,symbol,coin_id FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
                        absint($args['offset']),
                        absint($args['number'])
                    )
                );

            }

            wp_cache_set($cache_key, $results, 'coins', 3600);

        }

        return $results;

    }

    /**
     *  A method for inserting multiple rows into the specified table
     *  Updated to include the ability to Update existing rows by primary key
     *
     *  Usage Example for insert:
     *
     *  $insert_arrays = array();
     *  foreach($assets as $asset) {
     *  $time = current_time( 'mysql' );
     *  $insert_arrays[] = array(
     *  'type' => "multiple_row_insert",
     *  'status' => 1,
     *  'name'=>$asset,
     *  'added_date' => $time,
     *  'last_update' => $time);
     *
     *  }
     *
     *
     *  wp_insert_rows($insert_arrays, $wpdb->tablename);
     *
     *  Usage Example for update:
     *
     *  wp_insert_rows($insert_arrays, $wpdb->tablename, true, "primary_column");
     *
     *
     * @param array $row_arrays
     * @param string $wp_table_name
     * @param boolean $update
     * @param string $primary_key
     * @return false|int
     *
     */
    public function wp_insert_rows($row_arrays, $wp_table_name, $update = false, $primary_key = null)
    {
        global $wpdb;
        $wp_table_name = esc_sql($wp_table_name);
        // Setup arrays for Actual Values, and Placeholders
        $values = array();
        $place_holders = array();
        $query = "";
        $query_columns = "";

        $floatCols = array('price', 'percent_change_24h', 'market_cap', 'total_volume', 'circulating_supply');
        $query .= "INSERT INTO `{$wp_table_name}` (";
        foreach ($row_arrays as $count => $row_array) {
            foreach ($row_array as $key => $value) {
                if ($count == 0) {
                    if ($query_columns) {
                        $query_columns .= ", `" . $key . "`";
                    } else {
                        $query_columns .= "`" . $key . "`";
                    }
                }

                $values[] = $value;

                $symbol = "%s";
                if (is_numeric($value)) {
                    $symbol = "%d";
                }

                if (in_array($key, $floatCols)) {
                    $symbol = "%f";
                }
                if (isset($place_holders[$count])) {
                    $place_holders[$count] .= ", '$symbol'";
                } else {
                    $place_holders[$count] = "( '$symbol'";
                }
            }
            // mind closing the GAP
            $place_holders[$count] .= ")";
        }

        $query .= " $query_columns ) VALUES ";

        $query .= implode(', ', $place_holders);

        if ($update) {
            $update = " ON DUPLICATE KEY UPDATE `$primary_key`=VALUES( `$primary_key` ),";
            $cnt = 0;
            foreach ($row_arrays[0] as $key => $value) {
                if ($cnt == 0) {
                    $update .= "`$key`=VALUES(`$key`)";
                    $cnt = 1;
                } else {
                    $update .= ", `$key`=VALUES(`$key`)";
                }
            }
            $query .= $update;
        }

        $sql = $wpdb->prepare($query, $values);

        if ($wpdb->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the number of results found for a given query
     *
     * @param  array  $args
     * @return int
     */
    public function count($args = array())
    {
        return $this->get_coins($args, true);
    }

    /**
     * Create the table
     *
     * @access  public
     * @since   1.0
     */
    public function create_table()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE " . $this->table_name . " (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`coin_id` varchar(200) NOT NULL UNIQUE,
			`name` varchar(250) NOT NULL,
			`logo` text(300),
			`symbol` varchar(100) NOT NULL,
			`price` decimal(20,6),
			`percent_change_24h` decimal(6,2),
			`percent_change_1y` decimal(6,2) ,
			`percent_change_30d` decimal(6,2) ,
			`percent_change_7d` decimal(6,2) ,
			`market_cap` decimal(24,2),
			`total_volume` decimal(24,2) ,
			`circulating_supply` varchar(250),
			`weekly_price_data` longtext NOT NULL,
 			`coin_status` varchar(20) NOT NULL DEFAULT 'enable',
			`last_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

        dbDelta($sql);

        update_option($this->table_name . '_db_version', $this->version);
    }

    /**
     * Drop database table
     */
    public function drop_table()
    {
        global $wpdb;
        delete_transient('ccpw-all-gecko-coins');
        $wpdb->query("DROP TABLE IF EXISTS " . $this->table_name);

    }

    /*-----------------------------------------------------------------------|
    |                                                                         |
    |                    Check if a database table exists or not                 |
    |                                                                         |
    |------------------------------------------------------------------------|
     */
    public function is_table_exists()
    {
        global $wpdb;
        $table = $wpdb->prepare("SHOW TABLES LIKE %s", $this->table_name);

        if ($wpdb->get_var($table) == $this->table_name) {
            return true;
        } else {
            return false;
            delete_transient('ccpw-all-gecko-coins');
        }
    }

}
