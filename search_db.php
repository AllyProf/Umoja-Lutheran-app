<?php
$conn = new mysqli('localhost', 'root', '', 'umoja_lutheran');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = $conn->query("SHOW TABLES");
$found = false;

while ($table = $tables->fetch_row()) {
    $tableName = $table[0];
    $cols = $conn->query("SHOW COLUMNS FROM `$tableName`");
    $searchConds = [];

    while ($col = $cols->fetch_assoc()) {
        if (strpos($col['Type'], 'varchar') !== false || strpos($col['Type'], 'text') !== false || strpos($col['Type'], 'enum') !== false) {
            $searchConds[] = "`" . $col['Field'] . "` LIKE '%Keepeng%' OR `" . $col['Field'] . "` LIKE '%Beverage - Soda%'";
        }
    }

    if ($searchConds) {
        $q = "SELECT * FROM `$tableName` WHERE " . implode(' OR ', $searchConds);
        $res = $conn->query($q);
        if ($res && $res->num_rows > 0) {
            $found = true;
            echo "========== Found in table: $tableName ==========\n";
            while ($row = $res->fetch_assoc()) {
                print_r($row);
            }
        }
    }
}

if (!$found) {
    echo "No matching records found in any table.\n";
}
$conn->close();
