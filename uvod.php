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
                z.funkce,
                z.stredisko,
                z.telefon,
                z.mobil
            FROM Zamestnanci AS z
            WHERE uziv_jmeno = '$uziv';";
    
    $result = sqlsrv_query($conn, $sql);
    if ($result === FALSE)
        die(print_r(sqlsrv_errors(), true));

    $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($result);

    $jmeno = $zaznam['jmeno'];
    $funkce = $zaznam['funkce'];
    $stredisko = $zaznam['stredisko'];
    $tel = $zaznam['telefon'];
    $mobil = $zaznam['mobil'];

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
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
    </div>
    <div class="subHeader">
        <div class="uziv">
            <img src="user_icon.png" width="25%" style="margin-right: 2%;">
            <div class="uziv_inf">
                <p><?php echo $jmeno; ?></p>
                <p style="font-size: 12px; margin-left: 2px;"><?php echo $funkce; ?></p>
            </div>
        </div>
    </div>
    <div class="uzivCont">
        <div class="uzivMore">
            <p>Středisko: <?php echo $stredisko; ?></p>
            <p>Telefon: <?php echo $tel; ?></p>
            <p>Mobil: <?php echo $mobil ?></p>
        </div>
    </div>
    <div class="container">
        <form action="" method="post">
            <input type="submit" value="Nové povolení" name="subNove"><br>
            <input type="submit" value="Přehed a editace" name="subPrehled"><br>
            <input type="submit" value="Archiv" name="subArchiv"><br>
        </form>
    </div>
    <div class="footer">
        <p style="margin-left: 1%;">Přihlášený uživatel: <?php echo $uziv ?> </p>
        <img src="Indorama.png" style="margin-right: 7.5%;">
        <a href="login.php">
            <img src="logout_icon.png" width="78%" style="cursor: pointer;">
        </a>
    </div>
    <style>
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }   
        .subHeader{
            background-color: #ffffff;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
            flex-direction: column;
        }    
        .uziv{
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            align-items: center;
            padding: 1% 2% 1% 0;
            cursor: pointer;    
        }
        .uziv_inf p{
            margin: 0;
            padding: 1% 0;
        }
        .uzivMore{
            display: inline-flex;
            background-color: #FFFFFF; 
            align-items: flex-start;
            flex-direction: column;
        }
        .uzivCont{
            display: flex;
            justify-content: flex-end;
        }
        form {
            background-color: #FFFFFF; 
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            max-width: 400px;
            width: 200%;
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
            .container {
                text-align: center;
                flex-direction: column;
                width: 90%;
            }
            h1{
                margin: 0 0 0 0;
            }
            form {
                width: 90%;
                margin-top: 30%;
            }
        }
    </style>
</body>
</html>