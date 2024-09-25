<?php
    $connOptions = array("Database" => "", "UID" => "WebUser", "PWD" => "admin", "CharacterSet"=>"UTF-8");
    $conn = sqlsrv_connect("MSI\SQLEXPRESS", $connOptions);
    if ($conn == false)
        die(print_r(sqlsrv_errors(), true));
?>