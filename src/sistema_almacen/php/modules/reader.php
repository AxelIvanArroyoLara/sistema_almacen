<?php

include_once 'conn.php';

$sql_reader = 'SELECT * FROM usuarios';

$gsent = $connection->prepare($sql_reader);
$gsent->execute();

$user_info = $gsent->fetchAll(PDO::FETCH_ASSOC);

$column_names = array_keys($user_info[0]);

echo implode("\t", $column_names) . "\n";
echo str_repeat("-", 50) . "\n";

foreach ($user_info as $row) {
    foreach ($row as $column) {
        echo $column . "\t";
    }
    echo "\n";
    //root:2C603
}

?>