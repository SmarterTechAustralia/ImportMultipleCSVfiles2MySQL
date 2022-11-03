<?php
/* This script has been created by Kevin Jamali
to import multible CSV files to MySQL
Strongly recomanded to create a new Database for this script(see config)  */
include('config.php');
function csv2mysql($csvfile, $conn)
{
    $tableCreated = 0;
    $whatIWant = substr($csvfile, strpos($csvfile, "/") + 1);
    $csvfileNameArray = explode(".", $whatIWant);
    $tableName = strtolower($csvfileNameArray[0]);
    $tableName = str_replace(" ", "", $tableName);

    // get structure from csv and insert db
    ini_set('auto_detect_line_endings', TRUE);
    $handle = fopen($csvfile, 'r');
    // first row, structure
    if (($data = fgetcsv($handle)) === FALSE) {
        echo "Cannot read from csv $csvfile";
        die();
    }

    //create fields list
    $fields = array();
    $field_count = 0;
    for ($i = 0; $i < count($data); $i++) {
        $f = strtolower(trim($data[$i]));
        if ($f) {
            // normalize the field name, strip to 1000 chars if too long
            $f = substr(preg_replace('/[^A-Za-z0-9]/', '', $f), 0, 1000);
            $field_count++;
            $fields[] = '`' . $f . '` LONGTEXT';
        }
    }

    // remove if the table exist
    $sql = "DROP TABLE IF EXISTS $tableName";
    if ($conn->query($sql) === TRUE) {
        //echo "Table Deleted If exist<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // create a new table
    $sql = "CREATE TABLE $tableName (" . implode(', ', $fields) . ') ENGINE=InnoDB';
    if ($conn->query($sql) === TRUE) {
        $tableCreated = 1;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error . "<hr>";
    }
    if ($tableCreated == 1) {
        while (($data = fgetcsv($handle)) !== FALSE) {
            $fields = array();
            for ($i = 0; $i < $field_count; $i++) {
                $fields[] = '\'' . addslashes($data[$i]) . '\'';
                // trying to cleanup
                $fields = preg_replace('/[\x00-\x1F\x7F]/u', '', $fields);
            }
            $sql = "Insert into $tableName values(" . str_replace(",", "_", implode(', ', $fields)) . ')';

            if ($conn->query($sql) === TRUE) {
                //echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error . "<hr>";
            }
        }
    }
    //closing the CSV file
    fclose($handle);
    ini_set('auto_detect_line_endings', FALSE);

    if ($tableCreated == 1) {
        //adding tempId for faster Index
        $sql3 = "ALTER TABLE $tableName ADD tempId INT PRIMARY KEY AUTO_INCREMENT";
        $result3 = $conn->query($sql3);

        // find number of rows 
        $sql3 = " SELECT count(*) FROM $tableName";
        $result3 = $conn->query($sql3);
        $row_cnt = $result3->num_rows;
        if ($result3) {
            // it return number of rows in the table.
            $row = mysqli_num_rows($result3);

            if ($row) {
                //  printf("Number of row in the table : " . $row);
            } else {
                $row = 0;
            }
            // close the result.
            mysqli_free_result($result3);
        }
    }

    $datareport = "<div class='alert alert-success'>CSV imported to<strong> $tableName </strong>table<strong>: $row </strong>records</div>";
    echo $datareport;
}
