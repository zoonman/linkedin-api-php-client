<?php

namespace Pricat\Utils;

class Ftp
{
    private static $conn;

    public static function getConnection()
    {
        return self::$conn;
    }


    public static function open($hostname, $port, $username, $password)
    {
        self::$conn = ftp_connect($hostname, $port);
        if (!self::$conn || !@ftp_login(self::$conn, $username, $password)) {
            return false;
        }

        ftp_pasv(self::$conn, true);

        return true;
    }


    public static function get($orig, $dest, $mode = FTP_ASCII)
    {
        return ftp_get(self::$conn, $dest, $orig, $mode);
    }


    public static function put($orig, $dest, $mode = FTP_ASCII)
    {
        return ftp_put(self::$conn, $dest, $orig, $mode);
    }


    public static function close()
    {
        if (self::$conn !== null) {
            ftp_close(self::$conn);
        }
    }
}
