<?php
    $host = '127.0.0.1';
    $port = 16000;
    $socket = fsockopen($host, $port, $errno, $errstr, 30);

    if (!$socket) {
        echo "Nelze se připojit: $errstr ($errno)";
    } 
    else {
        fwrite($socket, "SELECT * FROM Stroje");

        while (!feof($socket)) {
            echo fgets($socket, 128);
        }
        fclose($socket);
    }
?>