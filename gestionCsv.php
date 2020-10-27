<?php

use Pricat\Utils\Ftp;
use Pricat\Utils\Helper as Utils;

require "bootstrap.php";

$todo = true;
$arrstats = array();

Utils::$unit = Utils::UNIT_KIBIBYTES;
Utils::$timerStart = microtime(true);

if (USERMODE) {
    Utils::printInfo("Script lanzado manualmente\n");
} else {
    Utils::printInfo("Script lanzado desde cron\n");
}

Utils::printInfo(sprintf("Time start: %s\n", date('Y-m-d H:i:s')));
Utils::printInfo(sprintf("Memory limit: %s\n", ini_get('memory_limit')));
Utils::printStats('Init');


//En caso de ser necesario cerramos la conexion ftp y desbloqueamos el fichero
if (isset($lockFilename)) {
    FTP::close();
    @unlink($lockFilename);
}
Utils::printStats('End');
Utils::printInfo("Script finalizado\n");
