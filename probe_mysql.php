<?php
$hosts = ['127.0.0.1', 'localhost'];
$ports = [3306, 3307];

foreach ($hosts as $host) {
    foreach ($ports as $port) {
        $conn = @fsockopen($host, $port, $errno, $errstr, 2);
        if ($conn) {
            echo "Port $port is OPEN on $host\n";
            fclose($conn);
        } else {
            echo "Port $port is CLOSED on $host ($errstr)\n";
        }
    }
}
