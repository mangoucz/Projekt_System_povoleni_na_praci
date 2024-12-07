<?php
    session_start();
    if (isset($_SESSION['uziv']))
        $uziv = $_SESSION['uziv'];
    else{
        header("Location: login.html");
        exit();    
    }
    require_once 'server.php';

    $sql = "SELECT 
                CONCAT(z.jmeno, ' ', z.prijmeni) AS jmeno,
                z.funkce
            FROM Zamestnanci AS z
            WHERE uziv_jmeno = '$uziv';";
    
    $result = sqlsrv_query($conn, $sql);
    if ($result === FALSE)
        die(print_r(sqlsrv_errors(), true));

    $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($result);

    $jmeno = $zaznam['jmeno'];
    $funkce = $zaznam['funkce'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['subNove']) {
            header("Location: nove.php");
            exit();
        }
        else if ($_POST['subPrehled']) {
            header("Location: prehled.php");
            exit();
        }
        else if ($_POST['subArchiv']) {
            header("Location: archiv.php");
            exit();
        }
        else {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systém povolení na práci</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <img src="Indorama.png" class="logo">
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
        <div class="headerB">
            <form action="" method="post">
                <input type="submit" value="Nové povolení" name="subNove">
            </form>
            <div class="uziv">
                <img src="user_icon.png" width="28%" style="margin-right: 2%;">
                <div class="uziv_inf">
                    <p><?php echo $jmeno; ?></p>
                    <p style="font-size: 12px; margin-left: 2px;"><?php echo $funkce; ?></p>
                </div>
            </div>
            <a href="login.php">
                <img src="logout_icon.png" width="78%" style="cursor: pointer;">
            </a>
        </div>
    </div>
    <div class="footer">
        <img src="Indorama.png">
    </div>
    <!-- <div class="container">
        <form action="" method="post">
            <input type="submit" value="Přehed a editace" name="subPrehled"><br>
            <input type="submit" value="Archiv" name="subArchiv"><br>
        </form>
    </div> -->
    <style>
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }   
        .footer{
            display: none;
            justify-content: center;
            padding: 0.75% 0;
        }
        form {
            background-color: #FFFFFF; 
            width: 60%;
            text-align: center;
        }
        input[type="submit"] {
            background-color: #003366; 
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            margin: 2% 0;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #d40000;
        }

        @media (max-width: 660px) {
            .header {
                flex-direction: column;
                align-items: center;
            }
            .footer{
                display: flex;  
            }
            .logo {
                display: none;
            }
            h1 {
                margin: 5% 0;
                font-size: 1.5em;
            }
        }
    </style>
</body>
</html>