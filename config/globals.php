<?php

define('DEBUG_MODE', false);
define('EMAIL_ADMIN', 'alfonso@missconversion.es');
define("GESTIONCSV_HASH_VERSION", "20180912");
define('SHOP_NAME', strtolower(Configuration::get('PS_SHOP_NAME')));
define("SHOP_LANG", 1);
define('LOG_FILE', PS_ROOT . '/integraciones/Pricat/logs/GestionCSV_new_' . date('YmdHis') . '.log');
