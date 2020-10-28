<?php

use Pricat\Services\Csv\Management;
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

try {

    $lockFilename = sprintf("%s/%s.lock", __DIR__, basename(__FILE__));
    if (file_exists($lockFilename)) {
        throw new Exception("Locked by PID: " . file_get_contents($lockFilename));
    }
    file_put_contents($lockFilename, getmypid());

    if (!FTP::open('109.234.84.100', 21, '109', 'pjNFW20LgJNe')) {

        throw new Exception('Cannot connect to FTP');
    }

    $managementService = new Management();
    Utils::printStats('new GestionCSV');

    (new \Pricat\Services\Csv\Download())->run();
    Utils::printStats('GestionCSV download');

    $updateIds = $managementService->run($todo);
    Utils::printStats('GestionCSV run');

    //En caso de haber actualizado productos reindexamos dichos productos, si son mas de mil avisamos para realizar reindexación completa
    if (count($updateIds)) {
        Utils::printInfo("Reindexando " . count($updateIds) . " productos\n");
        foreach ($updateIds as $k) {
            Search::indexation(0, $k);
        }
    }
    Utils::printStats('Search indexation');


} catch (Exception $e) {
    $msg = $e->getMessage();
    Utils::printInfo($msg);
    mail("carlos@bokokode.com", sprintf('[%s] Error GestionCSV', SHOP_NAME), $msg);
}


//En caso de ser necesario cerramos la conexion ftp y desbloqueamos el fichero
if (isset($lockFilename)) {
    FTP::close();
    @unlink($lockFilename);
}
Utils::printStats('End');
Utils::printInfo("Script finalizado\n");
