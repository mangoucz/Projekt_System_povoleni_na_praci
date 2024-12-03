<?php
    $connOptions = array("Database" => "Permit", "UID" => "WebUser", "PWD" => "admin", "CharacterSet"=>"UTF-8");
    $conn = sqlsrv_connect("MSI\SQLEXPRESS", $connOptions);
    if ($conn == false)
        die(print_r(sqlsrv_errors(), true));

    register_shutdown_function(function () use ($conn) {
        if ($conn) {
            sqlsrv_close($conn);
        }
    });
?>