<?php

define("DEBUG_MODE", true);
//define('EMAIL_ADMIN', 'alfonso@missconversion.es');
define("EMAIL_ADMIN", "carlos@bokokode.com");
define("GESTIONCSV_HASH_VERSION", "20180912");
define("SHOP_NAME", strtolower(Configuration::get("PS_SHOP_NAME")));
define("SHOP_LANG", 1);
define("PATH_CSV_FILE", PRICAT_ROOT . "/csv/PRICAT_WEBTOP.CSV");
define("PATH_PHOTOS", PRICAT_ROOT . "/photos/");
define("LOG_FILE", PS_ROOT . "/integraciones/Pricat/logs/GestionCSV_new_" . date("YmdHis") . ".log");
