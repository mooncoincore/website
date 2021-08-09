<?php
namespace CryptocurrencyWidgetsProREG;
class CCPWP_ApiConf{
    const PLUGIN_NAME = 'Cryptocurrency Widgets PRO';
    const PLUGIN_VERSION = CCPWP_VERSION;
    const PLUGIN_PREFIX = 'ccpw';
    const PLUGIN_AUTH_PAGE = 'ccpw_registration';
    const PLUGIN_URL = CCPWP_URL;
}

    require_once 'class.settings-api.php';
    require_once 'CryptocurrencyWidgetsProBase.php';
    require_once 'api-auth-settings.php';

	new CCPWP_Settings();