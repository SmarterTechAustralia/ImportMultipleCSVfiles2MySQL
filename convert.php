<?php
/* This script has been created by Kevin Jamali
to import multible CSV files to MySQL
Strongly recomanded to create a new Database for this script(see config)  */
include_once('includes/config.php');
include_once('includes/csvtomysql.php');
set_time_limit(1000);


$files = glob($dir . '/*csv');
foreach ($files as $file) {
    echo "filename:" . $file . "<br />";
    $theresult = csv2mysql($file, $conn);
}
