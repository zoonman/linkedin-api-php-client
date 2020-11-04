<?php

error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('Europe/Madrid');
ini_set('memory_limit', '1024M');
ini_set('display_errors', '1');
ini_set('log_errors', '1');

$_SERVER['REQUEST_METHOD'] = 'POST';

define('PS_ROOT', realpath(__DIR__ . '/../..'));
define('PRICAT_ROOT', realpath(__DIR__));

ini_set('error_log', PRICAT_ROOT . '/logs/error.log');

require_once 'vendor/autoload.php';

require PS_ROOT . '/config/config.inc.php';
require PS_ROOT . '/init.php';
require PS_ROOT . '/images.inc.php';
//require PS_ROOT . '/Encoding.php'; //Este fichero falta

require __DIR__ . "/config/globals.php";

if (!is_dir(PRICAT_ROOT . "/csv")) {
    mkdir(PRICAT_ROOT . "/csv", 0775);
}

if (!is_dir(PRICAT_ROOT . "/logs")) {
    mkdir(PRICAT_ROOT . "/logs", 0775);
}

if (!is_dir(PRICAT_ROOT . "/photos")) {
    mkdir(PRICAT_ROOT . "/photos", 0775);
}

if (isset($_GET['s']) && $_GET['s'] == 'jfdisuf9234tgjv0sdg34gtdfh') {
    define('USERMODE', true);
    ini_set('max_execution_time', 8000000);
} elseif (PHP_SAPI != 'cli') {
    exit();
} else {
    define('USERMODE', false);
}

gc_collect_cycles();
gc_disable();

