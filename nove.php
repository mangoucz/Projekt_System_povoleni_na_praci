<?php
    session_start();

    if (isset($_SESSION['uziv']))
        $uziv = $_SESSION['uziv'];
    else{
        header("Location: login.html");
        exit();    
    }
    require_once 'server.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systém povolení na práci</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <div class="header">
        <h1>NOVÉ POVOLENÍ</h1>
    </div>
    <div class="container">
        <fieldset>
            <legend><b>Povolení</b></legend>
            <div class="vybery">
                <input type="checkbox" name="povolení" value="zarizeni">K práci na zařízení<br>
                <input type="checkbox" name="povolení" value="svarovani">Ke svařování a práci s otevřeným ohněm<br>
                <input type="checkbox" name="povolení" value="vstup">Ke vstupu do zařízení nebo pod úroveň terénu<br>
                <input type="checkbox" name="povolení" value="vybuch">K práci v prostředí s nebezpečím výbuchu<br>
                <input type="checkbox" name="povolení" value="oprava">K předání a převzetí zařízení do opravy a do provozu<br>
            </div>
        </fieldset>
    </div>
    <div class="footer">
        <p style="margin-left: 1%;">Přihlášený uživatel: <?php echo $uziv ?> </p>
        <img src="Indorama.png" style="margin-right: 5.7%;">
        <a href="login.html">
            <img src="logout_icon.png" width="78%" style="cursor: pointer;">
        </a>
    </div>
    <style>
        body {
            align-items: center;
            text-align: center;
        }
        .header{
            background-color: #b6c7e2;
            width: 100%;
            padding: 0.5% 0;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        fieldset {
            border: 2px solid #003366;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        legend {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
            padding: 0 10px;
        }
    </style>
</body>
</html>