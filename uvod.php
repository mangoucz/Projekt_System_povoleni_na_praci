<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subLogin'])) {
        $uziv = $_POST['uziv'];
        $_SESSION['uziv'] = $uziv;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } 
    else{
        if (isset($_SESSION['uziv'])){ 
            $uziv = $_SESSION['uziv'];
        }
        else{
            header("Location: login.html");
            exit();
        }
    }

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
    <div class="container">
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
        <span class="separator">|</span>
        <form action="" method="post">
            <input type="submit" value="Nové povolení" name="subNove"><br>
            <input type="submit" value="Přehed a editace" name="subPrehled"><br>
            <input type="submit" value="Archiv" name="subArchiv"><br>
        </form>
    </div>
    <div class="footer">
        <p style="margin-left: 1%;">Přihlášený uživatel: <?php echo $uziv ?> </p>
        <img src="Indorama.png" style="margin-right: 7.5%;">
        <a href="login.html">
            <img src="logout_icon.png" width="78%" style="cursor: pointer;">
        </a>
    </div>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 15% 0 0 0;
        }
        h1 {
            color: #003366; 
            font-size: 36px;
            margin: 1% 20px 0 0;
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
            .separator {
                display: none;
            }
            form {
                width: 90%;
                margin-top: 30%;
            }
        }
    </style>
</body>
</html>