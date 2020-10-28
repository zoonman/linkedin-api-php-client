<?php


namespace Pricat\Services\Csv;


use Exception;
use Pricat\Utils\Ftp;

class Download
{
    /**
     * @throws Exception
     */
    public function run()
    {
        if (!Ftp::get('/pricat_ctop3.csv', PATH_CSV_FILE)) {
            throw new Exception('Cannot download CSV file from FTP');
        }
    }
}
